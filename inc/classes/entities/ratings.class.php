<?

class Rating extends EntityBase {
	// Constants
	const table = "ratings";

	const RATING_ID = "RATING_ID";
	const USER_ID = "USER_ID";
	const RATING = "RATING";
	const DATE = "DATE";

	// Properties
	var $RatingId;
	var $UserId;
	var $Rating;
	var $Date;

	function Rating($ratingId = -1) {
		$this->table = self::table;
		$this->Clear();

		$this->RatingId = $ratingId;
	}

	function Clear() {
		$this->RatingId = -1;
		$this->$UserId = -1;
		$this->$Rating = 0;
		$this->$Date = NowDateTime();
	}

	function IsFull() {
		return $this->UserId > 0 && $this->Rating != 0;
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::RATING_ID);
		$this->$UserId = $result->Get(self::USER_ID);
		$this->$Rating = $result->Get(self::RATING);
		$this->$Date = $result->Get(self::DATE);
	}


		$s = "<ul type=square>";
		$s.= "<li>".self::RATING_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
		$s.= "<li>".self::RATING.": ".$this->Rating."</li>\n";
		$s.= "<li>".self::DATE.": ".$this->Date."</li>\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Rating is not saved!</b>";
		}
		$s.= "</ul>";
		return $s;
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::RATING_ID.",
	t1.".self::USER_ID.",
	t1.".self::RATING.",
	t1.".self::DATE."
FROM
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::USER_ID.", 
".self::RATING.", 
".self::DATE."
)
VALUES
(".round($this->UserId).", 
".round($this->Rating).",
'".SqlQuote($this->Date)."'
)";
	}

	function UpdateExpression() {
		return "";
	}

	function DeleteExpression() {
		return "";
	}
}

?>