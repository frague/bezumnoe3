<?

class JournalSettings extends EntityBase {
	// Constants
	const table = "journal_settings";

	const JOURNAL_SETTINGS_ID = "JOURNAL_SETTINGS_ID";
	const FORUM_ID = "FORUM_ID";
	const ALIAS = "ALIAS";
	const REQUESTED_ALIAS = "REQUESTED_ALIAS";
	const SKIN_TEMPLATE_ID = "SKIN_TEMPLATE_ID";
	const LAST_MESSAGE_DATE = "LAST_MESSAGE_DATE";

	const PARAMETER = "alias";

	// Properties
	var $ForumId;
	var $Alias;
	var $RequestedAlias;
	var $SkinTemplateId;
	var $LastMessageDate;

	// Fields
	function JournalSettings($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::JOURNAL_SETTINGS_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->ForumId = -1;
		$this->Alias = "";
		$this->RequestedAlias = "";
		$this->SkinTemplateId = -1;
		$this->LastMessageDate = "";
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::JOURNAL_SETTINGS_ID);
		$this->ForumId = $result->Get(self::FORUM_ID);
		$this->Alias = $result->Get(self::ALIAS);
		$this->RequestedAlias = $result->Get(self::REQUESTED_ALIAS);
		$this->SkinTemplateId = $result->GetNullableId(self::SKIN_TEMPLATE_ID);
		$this->LastMessageDate = $result->Get(self::LAST_MESSAGE_DATE);
	}

	function GetByAlias($alias) {
		return $this->FillByCondition(self::ALIAS."='".SqlQuote(substr($alias, 0, 20))."'");
	}

	function GetByForumId($forumId) {
		return $this->FillByCondition(self::FORUM_ID."=".round($forumId));
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::JOURNAL_SETTINGS_ID." = ".$this->Id."</li>\n";
		$s.= "<li>".self::FORUM_ID." = ".$this->ForumId."</li>\n";
		$s.= "<li>".self::ALIAS." = ".$this->Alias."</li>\n";
		$s.= "<li>".self::REQUESTED_ALIAS." = ".$this->RequestedAlias."</li>\n";
		$s.= "<li>".self::SKIN_TEMPLATE_ID." = ".$this->SkinTemplateId."</li>\n";
		$s.= "<li>".self::LAST_MESSAGE_DATE." = ".$this->LastMessageDate."</li>\n";

		if ($this->IsEmpty()) {
			$s.= "<li> <b>Journal Settings are not saved!</b>";
		}
		$s.= "</ul>";
		return $s;
	}

	function ToJs($title = "", $description = "", $is_protected = 0) {
		$s = "[\"".
JsQuote($this->Alias)."\",\"".
JsQuote($this->RequestedAlias)."\",\"".
JsQuote($title)."\", \"".
JsQuote($description)."\",".
Boolean($is_protected)."]";
		return $s;
	}

	function ToLink($login) {
		return self::MakeLink($this->Alias, $login);
	}

	function ToTitleLink($title, $target = "") {
		return self::MakeLink($this->Alias, ($title ? $title : "без названия"), new ForumRecordBase(), $target);
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::JOURNAL_SETTINGS_ID.",
	t1.".self::FORUM_ID.",
	t1.".self::ALIAS.",
	t1.".self::REQUESTED_ALIAS.",
	t1.".self::SKIN_TEMPLATE_ID.",
	t1.".self::LAST_MESSAGE_DATE."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::FORUM_ID.", 
".self::ALIAS.", 
".self::REQUESTED_ALIAS.", 
".self::SKIN_TEMPLATE_ID.", 
".self::LAST_MESSAGE_DATE."
)
VALUES
(".round($this->ForumId).", 
".Nullable($this->Alias).", 
".Nullable($this->RequestedAlias).", 
".NullableId($this->SkinTemplateId).", 
".Nullable($this->LastMessageDate)."
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::FORUM_ID."=".SqlQuote($this->ForumId).", 
".self::ALIAS."=".Nullable($this->Alias).", 
".self::REQUESTED_ALIAS."=".Nullable($this->RequestedAlias).", 
".self::SKIN_TEMPLATE_ID."=".NullableId($this->SkinTemplateId).", 
".self::LAST_MESSAGE_DATE."=".Nullable($this->LastMessageDate)."
WHERE 
	".self::JOURNAL_SETTINGS_ID."=".round($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::JOURNAL_SETTINGS_ID."=".round($this->Id);
	}

	function GetJournalDataExpression() {
		return "SELECT
t1.".self::FORUM_ID.",
t1.".self::ALIAS.",
t1.".self::LAST_MESSAGE_DATE.",
t3.".User::LOGIN."
	FROM 
".self::table." t1
LEFT JOIN ".Forum::table." t2 ON t2.".Forum::FORUM_ID."=t1.".self::FORUM_ID."
LEFT JOIN ".User::table." t3 ON t3.".User::USER_ID."=t2.".Forum::LINKED_ID."
	WHERE
##CONDITION##";
	}

	function GetUpdatedTemplatesExpression() {
		$result = str_replace("FROM",
		",
t5.".JournalTemplate::UPDATED."
	FROM",
		$this->GetJournalDataExpression());

		$result = str_replace("WHERE",
		"LEFT JOIN ".JournalTemplate::table." t5 ON t5.".JournalTemplate::FORUM_ID."=t1.".self::FORUM_ID."
WHERE",
		$result);

		return $result;
	}

	/* Static Methods */

	function MakeHref($alias, $recordId = 0) {
		$recordId = round($recordId);
		if ($recordId > 0) {
			$tail = "/post".$recordId;
		}
		return "/journal/".$alias.$tail;
	}

	function MakeLink($alias, $text, $record = "", $target = "") {
		if (!$alias && !$record) {
			return $text;
		}

		$css = "";
		if ($record && !$record->IsEmpty()) {
			$css = " class='".$record->MakeCss()."'";
		}

		return "<a".$css.($target ? " target='".$target."'" : "")." href='".self::MakeHref($alias, $record->Id)."'>".$text."</a>";
	}
	
}

?>