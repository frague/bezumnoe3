<?

class Tag extends EntityBase {
	// Constants
	const table = "tags";

	const TAG_ID = "TAG_ID";
	const TITLE = "TITLE";


	// Properties
	var $Id;
	var $Title;

	// Fields

	function Tag($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::TAG_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->Title = "";
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::TAG_ID);
		$this->Title = $result->Get(self::TITLE);
	}

	function FillFromHash($hash) {
		$this->Id = round($hash[self::TAG_ID]);
		$this->Title = UTF8toWin1251($hash[self::TITLE]);
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::TAG_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::TITLE.": ".$this->Title."</li>\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Tag is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	function ToJs() {
		return "new tagdto("
.round($this->Id).",\""
.JsQuote($this->Title)."\")";
	}


	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::TAG_ID.", 
	t1.".self::TITLE."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##
ORDER BY
	t1.".self::TITLE." ASC";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::TITLE.")
VALUES
('".SqlQuote($this->Title)."')";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::TITLE."='".SqlQuote($this->Title)."'
WHERE 
	".self::TAG_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::TAG_ID."=".SqlQuote($this->Id);
	}
}

?>