<?

class Tag extends EntityBase {
	// Constants
	const table = "tags";

	const TAG_ID = "TAG_ID";
	const TITLE = "TITLE";
	const WEIGHT = "WEIGHT";

	const PARAMETER = "tag";

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
		$this->Weight = 0;
	}

	function GetByRecordId($recordId) {
		$recordId = round($recordId);
		return $this->GetByCondition("t2.".RecordTag::RECORD_ID."=".$recordId, $this->ReadLinkedExpression());
	}

	function GetCloud($forumId) {
		return $this->GetByCondition("t3.".ForumRecord::FORUM_ID."=".round($forumId), $this->CloudExpression());
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::TAG_ID);
		$this->Title = $result->Get(self::TITLE);
		$this->Weight = $result->Get(self::WEIGHT);
	}

	function FillFromHash($hash) {
		$this->Id = round($hash[self::TAG_ID]);
		$this->Title = UTF8toWin1251($hash[self::TITLE]);
	}

	function BulkCreate($values) {
		for ($i = 0; $i < sizeof($values); $i++) {
			$this->Clear();
			$this->Title = $values[$i];
			$this->GetByCondition("", $this->CreateExpression());
		}
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

	function ToJs($mark = "") {
		return "new tagdto(\"".JsQuote($this->Title)."\",\"".JsQuote(Mark($this->Title, $mark))."\")";
	}

    function ToPrint($index, $alias, $style="", $text="") {
        if (!$text) {
            $text = $this->Title;
        }
		return ($index ? ", " : "")."<a href=\"/journal/".$alias."/tag/".urlencode($this->Title)."\"".($style ? " style='".$style."'" : "").">".$text."</a>";
	}

	function ToCloud($maxWeight, $alias) {
		$r = round(50 + 100 * ($this->Weight / $maxWeight));
		return $this->ToPrint(0, $alias, "font-size:".$r."%");
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

	function ReadLinkedExpression() {
		return "SELECT 
	t1.".self::TAG_ID.", 
	t1.".self::TITLE."
FROM 
	".$this->table." AS t1 
	LEFT JOIN ".RecordTag::table." t2 ON t2.".RecordTag::TAG_ID."=t1.".self::TAG_ID."
WHERE
	##CONDITION##
ORDER BY
	t1.".self::TITLE." ASC";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::TITLE.")
VALUES
('".SqlQuote($this->Title)."')
ON DUPLICATE KEY UPDATE ".self::TAG_ID."=".self::TAG_ID;
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

	function CloudExpression() {
		return "SELECT 
  COUNT(1) AS ".self::WEIGHT.",
  t1.".self::TITLE."
FROM ".$this->table." AS t1
  JOIN ".RecordTag::table." AS t2 ON t2.".RecordTag::TAG_ID."=t1.".self::TAG_ID."
  JOIN ".ForumRecord::table." AS t3 ON t3.".ForumRecord::RECORD_ID."=t2.".RecordTag::RECORD_ID."
WHERE
  ##CONDITION##
GROUP BY t1.".self::TAG_ID."
ORDER BY t1.".self::TITLE;
	}
}

?>
