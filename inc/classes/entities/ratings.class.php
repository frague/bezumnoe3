<?

class Rating extends EntityBase {
	// Constants
	const table = "ratings";

	const IDS = "IDS";
	const TYPE = "TYPE";
	const RATING = "RATING";
	const DATE = "DATE";
	const IP = "IP";

	const SUM_TYPE = "SUM_TYPE";

	const TYPE_PROFILE = "profile";
	const TYPE_JOURNAL = "journal";

	// Properties
	var $Ids;
	var $Type;
	var $Rating;
	var $Date;
	var $Ip;

	function Rating($id, $rating, $type = self::TYPE_PROFILE) {
		$this->table = self::table;
		$this->Clear();

		$this->Ids = round($id);
		$this->Rating = $rating;
		$this->Type = $type;
	}

	function Clear() {
		$this->Ids = -1;
		$this->Type = self::TYPE_PROFILE;
		$this->Rating = 0;
		$this->Date = NowDate();
		$this->Ip = "";
	}

	function IsFull() {
		return $this->Ids > 0 && $this->Type && $this->Rating != 0;
	}

	function FillFromResult($result) {
		$this->Ids = $result->Get(self::IDS);
		$this->Type = $result->Get(self::TYPE);
		$this->Rating = $result->Get(self::RATING);
		$this->Date = $result->Get(self::DATE);
		$this->Ip = $result->Get(self::IP);
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::IDS.": ".$this->Ids."</li>\n";
		$s.= "<li>".self::TYPE.": ".$this->Type."</li>\n";
		$s.= "<li>".self::RATING.": ".$this->Rating."</li>\n";
		$s.= "<li>".self::DATE.": ".$this->Date."</li>\n";
		$s.= "<li>".self::IP.": ".$this->Ip."</li>\n";
		$s.= "</ul>";
		return $s;
	}


	// SQL

	function Save() {
	 global $db;

		if ($this->IsConnected() && $this->Ip) {
			// Check duplicates
			$q = $db->Query("SELECT 
   ".self::IDS."
FROM 
  ".$this->table."
WHERE
  ".self::IDS." = ".SqlQuote($this->Ids)." AND
  ".self::TYPE." = '".SqlQuote($this->Type)."' AND
  ".self::IP." = '".SqlQuote($this->Ip)."' LIMIT 1");

			if ($q->NumRows()) {
				return false;
			}
		}

		$q = $db->Query($this->CreateExpression());
		return true;
	}


	function ReadExpression() {
		return "SELECT 
	t1.".self::IDS.",
	t1.".self::TYPE.",
	t1.".self::RATING.",
	t1.".self::DATE.",
	t1.".self::IP."
FROM
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::IDS.", 
".self::TYPE.", 
".self::RATING.", 
".self::DATE.",
".self::IP."
)
VALUES
(".round($this->Ids).", 
'".SqlQuote($this->Type)."',
".round($this->Rating).",
'".SqlQuote($this->Date)."',
".Nullable($this->Ip)."
)";
	}

	function UpdateExpression() {
		return "";
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ##CONDITION##";
	}

	// Sums all daily user's ratings
	public static function UpdateUsersRatingsExpression($dat) {
		return "UPDATE ".Profile::table." t1 JOIN (SELECT IDS, SUM(t3.".Rating::RATING.") AS RATING_SUM 
FROM ".Rating::table." t3
WHERE 
	t3.".Rating::DATE."<'".$dat."' AND 
	t3.".Rating::TYPE."='".Rating::TYPE_PROFILE."'
GROUP BY t3.".Rating::IDS.") t2 ON t1.".Profile::USER_ID."=t2.".Rating::IDS."
SET t1.".Profile::RATING." = t1.".Profile::RATING." + t2.RATING_SUM";
	}

	// Sums all daily user's ratings
	public static function UpdateForumsRatingsExpression($dat) {
		return "UPDATE ".Journal::table." t1 JOIN (
	SELECT IDS, SUM(t3.".Rating::RATING.") AS RATING_SUM 
	FROM ".Rating::table." t3
	WHERE 
		t3.".Rating::DATE."<'".$dat."' AND 
		t3.".Rating::TYPE."='".Rating::TYPE_JOURNAL."'
	GROUP BY t3.".Rating::IDS.") t2 ON t1.".Journal::FORUM_ID."=t2.".Rating::IDS."
SET t1.".Journal::RATING." = t1.".Journal::RATING." + t2.RATING_SUM";
	}

	// Reduces ratings that didn't changed
	public static function ReduceRatingsExpression() {
		return "UPDATE ".Profile::table."
SET ".Profile::RATING."=(CASE WHEN ".Profile::RATING."<10 THEN 0 ELSE ".Profile::RATING."-10 END)
WHERE ".Profile::RATING."=".Profile::LAST_RATING;
	}

	// Calcultes all user's phrazes and add 0.1 rating points for each
	public static function CountTodayMessagesExpression($dat) {
		return "UPDATE ".Profile::table." t1 JOIN (
	SELECT ".Message::USER_ID.", ROUND(COUNT(1)/10) AS SAID 
	FROM ".Message::table." 
	WHERE ".Message::DATE." LIKE '".$dat."%'
	GROUP BY ".Message::USER_ID.") t2 ON t1.".Profile::USER_ID." = t2.".Message::USER_ID."
SET t1.".Profile::RATING." = t1.".Profile::RATING." + t2.SAID";
	}

	// Updates all user ratings
	public static function UpdateRatings() {
	  global $db;

		$d = NowDate();
		
		$db->Query(Profile::PushRatingsExpression());

		$db->Query(Forum::PushRatingsExpression());

		$db->Query(Rating::CountTodayMessagesExpression($d));

		$db->Query(Rating::UpdateUsersRatingsExpression($d));

		$db->Query(Rating::UpdateForumsRatingsExpression($d));

		$db->Query(Rating::ReduceRatingsExpression());

		// TODO: Calculate journals ratings

		$r = new Rating(0, 0);
		$r->GetByCondition(Rating::DATE."<'".$d."'", $r->DeleteExpression());
		JsPoint("Delete");
	}

}

?>