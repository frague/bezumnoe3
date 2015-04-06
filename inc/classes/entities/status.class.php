<?

class Status extends EntityBase {
	// Constants
	const table = "statuses";

	const STATUS_ID = "STATUS_ID";
	const RIGHTS = "RIGHTS";
	const TITLE = "TITLE";
	const COLOR = "COLOR";
	const IS_SPECIAL = "IS_SPECIAL";

	// Standard rights
	const RIGHTS_NEWBIE	= 1;
	const RIGHTS_OLDBIE	= 11;

	// Properties
	var $Id;
	var $Rights;
	var $Title;
	var $Color;
	var $IsSpecial;

	// Fields

	function Status($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::STATUS_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->Rights = 0;
		$this->Title = "";
		$this->Color = "White";
		$this->IsSpecial = 0;
	}

	function IsAdmin() {
	  global $AdminRights;

		return $this->Rights >= $AdminRights;
	}

	function IsSuperAdmin() {
	  global $AdminRights;

		return $this->Rights > $AdminRights;
	}

	function CheckSum($extended = false) {
		$cs = 0;
		$cs += CheckSum($this->Rights);
		$cs += CheckSum($this->Title);
		$cs += CheckSum($this->Color);
		return $cs;
/*

-	var $Id;
+	var $Rights;
+	var $Title;
+	var $Color;

*/
	
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::STATUS_ID);
		$this->Rights = $result->Get(self::RIGHTS);
		$this->Title = $result->Get(self::TITLE);
		$this->Color = $result->Get(self::COLOR);
		$this->IsSpecial = $result->Get(self::IS_SPECIAL);
	}

	function FillFromHash($hash) {
		$this->Id = round($hash[self::STATUS_ID]);
		$this->Rights = round($hash[self::RIGHTS]);
		$this->Title = UTF8toWin1251($hash[self::TITLE]);
		$this->Color = $hash[self::COLOR];
		$this->IsSpecial = 1;
	}

	function HasErrors() {
	  global $db;

		$errors = "";
		if (!$this->Title) {
			$errors .= "Не указано название!<br>";
		}
		return $errors;
	}

	function GetStandardStatus($rights) {
		return $this->FillByCondition("t1.".self::RIGHTS."=".round($rights)." AND t1.".self::IS_SPECIAL."=0");
	}

	function GetNewbie() {
		return $this->GetStandardStatus(self::RIGHTS_NEWBIE);
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::STATUS_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::RIGHTS.": ".$this->Rights."</li>\n";
		$s.= "<li>".self::TITLE.": ".$this->Title."</li>\n";
		$s.= "<li>".self::COLOR.": ".$this->Color."</li>\n";
		$s.= "<li>".self::IS_SPECIAL.": ".$this->IsSpecial."</li>\n";
		$s.= "<li>Checksum: ".$this->CheckSum()."\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Status is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	function ToPrint() {
		if ($this->IsEmpty()) {
			return "";
		}
		return "<div style='color:".$this->Color."'>".$this->Title."</div>";
	}

	function ToLog() {
		if ($this->IsEmpty()) {
			return "";
		}
		return "\"<span style='color:".$this->Color."'>".$this->Title."</span>\" (".$this->Rights.")";
	}

	function ToJs() {
		return "new sdto("
.round($this->Id).","
.round($this->Rights).",\""
.JsQuote($this->Color)."\",\""
.JsQuote($this->Title)."\")";
	}

	function ToSelect($name, $user) {
	  global $AdminRights;

		$s = "";
		if (!$user->IsEmpty()) {
			if ($user->Status->Rights > $AdminRights) {
				$q = $this->GetByCondition("1");
			} else {
				$q = $this->GetByCondition("(".self::IS_SPECIAL."=0 AND ".self::RIGHTS."<=".$user->Status->Rights.") OR ".self::STATUS_ID."=".SqlQuote($user->Status->Id));
			}
			for ($i = 0; $i < $q->NumRows(); $i++) {
				$q->NextResult();
				$this->FillFromResult($q);
				if (!$this->IsEmpty()) {
					$s .= "<option value='".$this->Id."' style='color:".$this->Color."'>".$this->Rights." - ".$this->Title;
				}
			}
		}
		echo $s;
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::STATUS_ID.", 
	t1.".self::RIGHTS.", 
	t1.".self::TITLE.", 
	t1.".self::COLOR.",
	t1.".self::IS_SPECIAL."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##
ORDER BY
	t1.".self::RIGHTS." ASC";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::TITLE.", 
".self::RIGHTS.", 
".self::COLOR.",
".self::IS_SPECIAL."
)
VALUES
('".SqlQuote($this->Title)."', 
'".SqlQuote($this->Rights)."', 
'".SqlQuote($this->Color)."',
".Boolean($this->IsSpecial)."
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::TITLE."='".SqlQuote($this->Title)."', 
".self::RIGHTS."='".SqlQuote($this->Rights)."', 
".self::COLOR."='".SqlQuote($this->Color)."',
".self::IS_SPECIAL."=".Boolean($this->IsSpecial)."
WHERE 
	".self::STATUS_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::STATUS_ID."=".SqlQuote($this->Id);
	}
}

?>