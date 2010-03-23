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
		$this->$UserId = -1;
		$this->$Rating = 0;
		$this->$Date = NowDate();
		$this->$Ip = "";
	}

	function IsFull() {
		return $this->UserId > 0 && $this->Rating != 0;
	}

	function FillFromResult($result) {
		$this->$UserId = $result->Get(self::USER_ID);
		$this->$Rating = $result->Get(self::RATING);
		$this->$Date = $result->Get(self::DATE);
		$this->$Ip = $result->Get(self::IP);
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

	public static function UpdateRatingsExpression() {
		$d = NowDate();
		return "UPDATE ".Profile::table." t1, ".Rating::table." t2
SET
	t1.".Profile::RATING." = t1.".Profile::RATING." + (
			SELECT SUM(t3.".Rating::RATING.") 
			FROM ".Rating::table." t3
			WHERE t3.".Rating::DATE."<'".$d."' AND t3.".Rating::USER_ID."=t1.".Profile::USER_ID."
		)";
	}

	public static function UpdateRatings() {
	  global $db;
		
		$db->Query(Profile::PushRatingsExpression());
	}

}

?>