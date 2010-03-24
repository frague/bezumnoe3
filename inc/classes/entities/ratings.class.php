<?

class Rating extends EntityBase {
	// Constants
	const table = "ratings";

	const USER_ID = "USER_ID";
	const RATING = "RATING";
	const DATE = "DATE";
	const IP = "IP";

	const SUM_RATING = "SUM_RATING";

	// Properties
	var $UserId;
	var $Rating;
	var $Date;
	var $Ip;

	function Rating($userId, $rating) {
		$this->table = self::table;
		$this->Clear();

		$this->UserId = round($userId);
		$this->Rating = round($rating);
	}

	function Clear() {
		$this->UserId = -1;
		$this->Rating = 0;
		$this->Date = NowDate();
		$this->Ip = "";
	}

	function IsFull() {
		return $this->UserId > 0 && $this->Rating != 0;
	}

	function FillFromResult($result) {
		$this->UserId = $result->Get(self::USER_ID);
		$this->Rating = $result->Get(self::RATING);
		$this->Date = $result->Get(self::DATE);
		$this->Ip = $result->Get(self::IP);
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
		$s.= "<li>".self::RATING.": ".$this->Rating."</li>\n";
		$s.= "<li>".self::DATE.": ".$this->Date."</li>\n";
		$s.= "<li>".self::IP.": ".$this->Ip."</li>\n";
		$s.= "</ul>";
		return $s;
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::USER_ID.",
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
(".self::USER_ID.", 
".self::RATING.", 
".self::DATE.",
".self::IP."
)
VALUES
(".round($this->UserId).", 
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
	public static function UpdateRatingsExpression($dat) {
		return "UPDATE ".Profile::table." t1, ".Rating::table." t2
SET
	t1.".Profile::RATING." = t1.".Profile::RATING." + (
			SELECT SUM(t3.".Rating::RATING.") 
			FROM ".Rating::table." t3
			WHERE t3.".Rating::DATE."<'".$dat."' AND t3.".Rating::USER_ID."=t1.".Profile::USER_ID."
		)";
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

		$db->Query(Rating::CountTodayMessagesExpression($d));
		$db->Query(Rating::UpdateRatingsExpression($d));
		$db->Query(Rating::ReduceRatingsExpression());
		$r = new Rating(0, 0);
		$r->GetByCondition(Rating::DATE."<'".$d."'", $r->DeleteExpression());
	}

}

?>