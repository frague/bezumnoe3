<?

class JournalTemplate extends EntityBase {
	// Constants
	const table = "journal_templates";

	const TEMPLATE_ID = "TEMPLATE_ID";
	const FORUM_ID = "FORUM_ID";
	const BODY = "BODY";
	const MESSAGE = "MESSAGE";
	const CSS = "CSS";
	const UPDATED = "UPDATED";

	// Properties
	var $ForumId;
	var $Body;
	var $Message;
	var $Css;
	var $Updated;

	// Fields
	function JournalTemplate($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::TEMPLATE_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->TemplateId = -1;
		$this->ForumId = -1;
		$this->Body = "";
		$this->Message = "";
		$this->Css = "";
		$this->Updated = NowDateTime();
	}

	function IsComplete() {
		return ($this->Body != "" && $this->Message != "" && $this->Css != "");
	}

	function MergeWith($template) {
		if (!$this->Body) {
			$this->Body = $template->Body;
		}
		if (!$this->Message) {
			$this->Message = $template->Message;
		}
		if (!$this->Css) {
			$this->Css = $template->Css;
		}
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::TEMPLATE_ID);
		$this->TemplateId = $result->Get(self::TEMPLATE_ID);
		$this->ForumId = $result->GetNullableId(self::FORUM_ID);
		$this->Body = $result->Get(self::BODY);
		$this->Message = $result->Get(self::MESSAGE);
		$this->Css = $result->Get(self::CSS);
		$this->Updated = $result->Get(self::UPDATED);
		if (!$this->Updated) {
			$this->Updated = NowDateTime();
		}
	}

	function GetByForumId($ForumId) {
		return $this->GetByCondition("t1.".self::FORUM_ID."=".round($ForumId));
	}

	function FillByForumId($ForumId) {
		$q = $this->GetByForumId($ForumId);
		if ($q->NumRows()) {
			$q->NextResult();
			$this->FillFromResult($q);
		}
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::TEMPLATE_ID." = ".$this->Id."</li>\n";
		$s.= "<li>".self::FORUM_ID." = ".$this->ForumId."</li>\n";
		$s.= "<li>".self::BODY." = ".$this->Body."</li>\n";
		$s.= "<li>".self::MESSAGE." = ".$this->Message."</li>\n";
		$s.= "<li>".self::CSS." = ".$this->Css."</li>\n";
		$s.= "<li>".self::UPDATED." = ".$this->Updated."</li>\n";

		if ($this->IsEmpty()) {
			$s.= "<li> <b>Journal Template is not saved!</b>";
		}
		$s.= "</ul>";
		return $s;
	}

	function ToJs() {
		$s = "[\"".
JsQuote($this->Body)."\", \"".
JsQuote($this->Message)."\",  \"".
JsQuote($this->Css)."\"]";
		return $s;
	}


	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::TEMPLATE_ID.",
	t1.".self::FORUM_ID.",
	t1.".self::BODY.",
	t1.".self::MESSAGE.",
	t1.".self::CSS.",
	t1.".self::UPDATED."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::FORUM_ID.", 
".self::BODY.", 
".self::MESSAGE.", 
".self::CSS.",
".self::UPDATED."
)
VALUES
(".NullableId($this->ForumId).", 
'".SqlQuote($this->Body)."', 
'".SqlQuote($this->Message)."', 
'".SqlQuote($this->Css)."',
".Nullable(SqlQuote($this->Updated))."
)";
	}

	function UpdateExpression() {
		$this->Updated = NowDateTime();

		$result = "UPDATE ".$this->table." SET 
".self::FORUM_ID."=".NullableId($this->ForumId).", 
".self::BODY."='".SqlQuote($this->Body)."', 
".self::MESSAGE."='".SqlQuote($this->Message)."', 
".self::CSS."='".SqlQuote($this->Css)."',
".self::UPDATED."=".Nullable(SqlQuote($this->Updated))."
WHERE 
	".self::TEMPLATE_ID."=".SqlQuote($this->Id);

		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::RECORD_ID."=".SqlQuote($this->Id);
	}

	function DeleteForForumExpression($forumId) {
		return "DELETE FROM ".$this->table." WHERE ".self::FORUM_ID."=".round($forumId);
	}
}

?>