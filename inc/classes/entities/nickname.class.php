<?

class Nickname extends EntityBase {
	// Constants
	const table = "nicknames";

	const NICKNAME_ID = "NICKNAME_ID";
	const USER_ID = "USER_ID";
	const TITLE = "NAME";
	const IS_SELECTED = "IS_SELECTED";

	// Properties
	var $UserId;
	var $Title;
	var $IsSelected;

	// Fields

	function Nickname($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::NICKNAME_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->UserId = -1;
		$this->Title = "";
		$this->IsSelected = false;
	}

	function CheckSum($extended = false) {
		$cs = CheckSum($this->Title);
		return $cs;

/*	

-	var $UserId;
+	var $Title;
-	var $IsSelected;

*/
	}

	function Validate() {
	 global $MaxNicknameLength;

		if (strlen($this->Title) > $MaxNicknameLength) {
			return "Превышена допустимая длина имени (максимум - ".Countable("символ", $MaxNicknameLength).").";
		}
		if (preg_match("/[a-zA-Z]/", $this->Title) && preg_match("/[а-яА-Я]/", $this->Title)) {
			return "Недопустимо смешение в имени русского и латинского алфавитов.";
		}
		return "";
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::NICKNAME_ID);
		$this->UserId = $result->Get(self::USER_ID);
		$this->Title = $result->Get(self::TITLE);
		$this->IsSelected = $result->Get(self::IS_SELECTED) > 0;
	}

	function GetByUserId($userId) {
		return $this->FillByCondition("t1.".self::USER_ID."=".SqlQuote($userId)." AND t1.".self::IS_SELECTED."=".Boolean($this->IsSelected));
	}

	function GetUserNicknames($userId) {
		return $this->GetByCondition("t1.".self::USER_ID."=".SqlQuote($userId)." ORDER BY t1.".self::NICKNAME_ID." ASC");
	}

	function Save() {
	 global $db;
		if ($this->IsConnected()) {
			// Check duplicates
			$q = $db->Query("SELECT 
   COUNT(*) +
   (SELECT COUNT(*) FROM ".User::table." WHERE ".User::LOGIN." = '".SqlQuote($this->Title)."') AS s
FROM 
  ".$this->table."
WHERE
  ".self::TITLE." = '".SqlQuote($this->Title)."'
  ".($this->IsEmpty ? "" : " AND ".self::NICKNAME_ID."<>".SqlQuote($this->Id)));

			if ($q->NumRows()) {
				$q->NextResult();
				if ($q->Get("s") > 0) {
					return false;
				}
			}
		
			// Reset selected User nickname
			if ($this->IsSelected) {
				$q = $db->Query("UPDATE ".$this->table." SET 
	".self::IS_SELECTED."=0 
WHERE
	".self::USER_ID."=".$this->UserId);
			}
		}

		return !parent::Save();
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::NICKNAME_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
		$s.= "<li>".self::TITLE.": ".$this->Title."</li>\n";
		$s.= "<li>".self::IS_SELECTED.": ".$this->IsSelected."</li>\n";
		$s.= "<li>Checksum: ".$this->CheckSum()."\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Nickname is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::NICKNAME_ID.",
	t1.".self::USER_ID.",
	t1.".self::TITLE.", 
	t1.".self::IS_SELECTED."
FROM
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::USER_ID.", 
".self::TITLE.", 
".self::IS_SELECTED."
)
VALUES
('".SqlQuote($this->UserId)."', 
'".SqlQuote($this->Title)."', 
'".Boolean($this->IsSelected)."'
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::USER_ID."='".SqlQuote($this->UserId)."', 
".self::TITLE."='".SqlQuote($this->Title)."', 
".self::IS_SELECTED."='".Boolean($this->IsSelected)."'
WHERE
	".self::NICKNAME_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::NICKNAME_ID."=".SqlQuote($this->Id);
	}
}

?>