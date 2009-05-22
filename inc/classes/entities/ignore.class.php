<?

class Ignore extends EntityBase {
	// Constants
	const table = "ignores";

	const IGNORE_ID = "IGNORE_ID";
	const USER_ID = "USER_ID";
	const IGNORANT_ID = "IGNORANT_ID";

	// Properties
	var $UserId;
	var $IgnorantId;

	function Ignore($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::IGNORE_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->UserId = -1;
		$this->IgnorantId = -1;
	}

	function IsFull() {
		return $this->UserId > 0 && $this->IgnorantId > 0;
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::IGNORE_ID);
		$this->UserId = $result->Get(self::USER_ID);
		$this->IgnorantId = $result->Get(self::IGNORANT_ID);
	}

	function GetByUserId($userId) {
		return $this->FillByCondition("t1.".self::USER_ID."=".SqlQuote($userId)." OR t1.".self::IGNORANT_ID."=".SqlQuote($userId));
	}

	function GetForOnlineUsers($user_id) {
		return $this->GetByCondition("t2.".User::ROOM_ID."<>-1", $this->ReadForOnlineUsersExpression($user_id));
	}

	function Save() {
	 global $db;
		if ($this->IsConnected() && $this->IsFull() && $this->UserId != $this->IgnorantId) {
			// Check duplicates
			$q = $db->Query("SELECT 
   ".self::IGNORE_ID."
FROM 
  ".$this->table."
WHERE
  ".self::USER_ID." = '".SqlQuote($this->UserId)."' AND
  ".self::IGNORANT_ID." = '".SqlQuote($this->IgnorantId)."' LIMIT 1");

			if ($q->NumRows()) {
				return false;
			}

			$q = $db->Query($this->CreateExpression());
			$this->Id = $q->GetLastId();
			return true;
		}
	}

	function Delete() {
	 global $db;

		if ($this->IsConnected() && $this->IsFull()) {
			$q = $db->Query($this->DeleteExpression());
			$this->Clear();
			return true;
		}
		return false;
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::IGNORE_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
		$s.= "<li>".self::IGNORANT_ID.": ".$this->IgnorantId."</li>\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Ignore is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::IGNORE_ID.",
	t1.".self::USER_ID.",
	t1.".self::IGNORANT_ID."
FROM
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function ReadForOnlineUsersExpression($user_id) {
		$user_id = round($user_id);
		return str_replace("WHERE", "
JOIN ".User::table." AS t2 ON 
	(t2.".User::USER_ID."=t1.".self::USER_ID." AND t1.".self::IGNORANT_ID."=".$user_id.") OR 
	(t2.".User::USER_ID."=t1.".self::IGNORANT_ID." AND t1.".self::USER_ID."=".$user_id.") 
WHERE", $this->ReadExpression());
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::USER_ID.", 
".self::IGNORANT_ID."
)
VALUES
('".SqlQuote($this->UserId)."', 
'".SqlQuote($this->IgnorantId)."'
)";
	}

	function UpdateExpression() {
		return "";
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table."
WHERE
	".self::USER_ID."=".SqlQuote($this->UserId)." AND
	".self::IGNORANT_ID."=".SqlQuote($this->IgnorantId);
	}
}
?>
