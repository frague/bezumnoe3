<?

class JournalSkin extends EntityBase {
	// Constants
	const table = "journal_skins";

	const SKIN_ID = "SKIN_ID";
	const CREATED = "CREATED";
	const TEMPLATE_ID = "TEMPLATE_ID";
	const TITLE = "TITLE";
	const AUTHOR = "AUTHOR";
	const SCREENSHOT = "SCREENSHOT";
	const IS_DEFAULT = "IS_DEFAULT";
	const IS_FRIENDLY = "IS_FRIENDLY";

	// Properties
	var $Created;
	var $TemplateId;
	var $Title;
	var $Author;
	var $Screenshot;
	var $IsDefault;
	var $IsFriendly;

	// Fields
	function JournalSkin($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::SKIN_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->Created = NowDateTime();
		$this->TemplateId = -1;
		$this->Title = "";
		$this->Author = "";
		$this->Screenshot = "";
		$this->IsDefault = 0;
		$this->IsFriendly = 0;
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::SKIN_ID);
		$this->Created = $result->Get(self::CREATED);
		$this->TemplateId = $result->Get(self::TEMPLATE_ID);
		$this->Title = $result->Get(self::TITLE);
		$this->Author = $result->Get(self::AUTHOR);
		$this->Screenshot = $result->Get(self::SCREENSHOT);
		$this->IsDefault = $result->Get(self::IS_DEFAULT);
		$this->IsFriendly = $result->Get(self::IS_FRIENDLY);
	}

	function GetTemplateId($field) {
		$q = $this->GetByCondition("t1.".$field."=1", $this->ReadTemplateIdExpression());
		if ($q->NumRows()) {
			$q->NextResult();
			return round($q->Get(self::TEMPLATE_ID));
		}
		return -1;
	}

	function GetDefaultTemplateId() {
		return $this->GetTemplateId(self::IS_DEFAULT);
	}

	function GetFriendlyTemplateId() {
		return $this->GetTemplateId(self::IS_FRIENDLY);
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::SKIN_ID." = ".$this->Id."</li>\n";
		$s.= "<li>".self::CREATED." = ".$this->Created."</li>\n";
		$s.= "<li>".self::TEMPLATE_ID." = ".$this->TemplateId."</li>\n";
		$s.= "<li>".self::TITLE." = ".$this->Title."</li>\n";
		$s.= "<li>".self::AUTHOR." = ".$this->Author."</li>\n";
		$s.= "<li>".self::SCREENSHOT." = ".$this->Screenshot."</li>\n";
		$s.= "<li>".self::IS_DEFAULT." = ".$this->IsDefault."</li>\n";
		$s.= "<li>".self::IS_FRIENDLY." = ".$this->IsFriendly."</li>\n";

		if ($this->IsEmpty()) {
			$s.= "<li> <b>Journal Skin is not saved!</b>";
		}
		$s.= "</ul>";
		return $s;
	}

	function ToSelect() {
		$result = "";
		$q = $this->GetByCondition("1");
		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();
			$this->FillFromResult($q);
			$result .= "<option value='".$this->TemplateId."' style='background:url(".$this->Screenshot.")'>&laquo;".$this->Title."&raquo;".($this->Author ? ", ".$this->Author : "")."</option>";
		}
		return $result;
	}

	function ToHtml($pathToScreenshots, $onclick="") {
#		$s = "<div class=\"skinPreview\">";
		$s = "<label for=\"skin_".$this->Id."\"> <input type=\"radio\" name=\"skintemplateid\" value=\"".$this->TemplateId."\" id=\"skin_".$this->Id."\" onclick=\"".$onclick."\"> ";
		if (!$this->IsEmpty()) {
			$s .= $this->Title." (".$this->Author.")";
		} else {
			$s .= "Собственный шаблон";
		}
		$s .= "<img src=\"".$pathToScreenshots."/".($this->IsEmpty() ? "custom.jpg" : $this->Screenshot)."\" class=\"Photo\">";
		$s .= "</label>";
#		$s .= "</div>";
		return $s;
	}

	// SQL
	function Save() {
	 global $db;
		if (!$this->IsConnected()) {
			return false;
		}

		$resetQuery = "";
		if ($this->IsDefault) {
			$resetQuery = self::IS_DEFAULT."=0";
		}
		if ($this->IsFriendly) {
			$resetQuery .= ($resetQuery ? "," : "").self::IS_FRIENDLY."=0";
		}
		if ($resetQuery) {
			$q = $db->Query("UPDATE ".$this->table." SET ".$resetQuery);
		}

		return parent::Save();
	}



	function ReadExpression() {
		return "SELECT 
	t1.".self::SKIN_ID.",
	t1.".self::CREATED.",
	t1.".self::TEMPLATE_ID.",
	t1.".self::TITLE.",
	t1.".self::AUTHOR.",
	t1.".self::SCREENSHOT."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::CREATED.", 
".self::TEMPLATE_ID.", 
".self::TITLE.", 
".self::AUTHOR.", 
".self::SCREENSHOT.",
".self::IS_DEFAULT.",
".self::IS_FRIENDLY."
)
VALUES
('".SqlQuote($this->Created)."', 
'".SqlQuote($this->TemplateId)."', 
'".SqlQuote($this->Title)."', 
'".SqlQuote($this->Author)."', 
'".SqlQuote($this->Screenshot)."',
".Boolean($this->IsDefault).",
".Boolean($this->IsFriendly)."
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::CREATED."='".SqlQuote($this->Created)."', 
".self::TEMPLATE_ID."='".SqlQuote($this->TemplateId)."', 
".self::TITLE."='".SqlQuote($this->Title)."', 
".self::AUTHOR."='".SqlQuote($this->Author)."', 
".self::SCREENSHOT."='".SqlQuote($this->Screenshot)."',
".self::IS_DEFAULT."=".Boolean($this->IsDefault).",
".self::IS_FRIENDLY."=".Boolean($this->IsFriendly)."
WHERE 
	".self::TEMPLATE_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::SKIN_ID."=".SqlQuote($this->Id);
	}

	function ReadTemplateIdExpression() {
		return "SELECT 
	t1.".self::TEMPLATE_ID."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##
LIMIT 1";
	}
}

?>