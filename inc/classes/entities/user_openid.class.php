<?

class UserOpenId extends EntityBase {
	// Constants
	const table = "user_open_ids";

	const USER_ID = "USER_ID";
	const OPENID_PROVIDER_ID = "OPENID_PROVIDER_ID";
	const LOGIN = "LOGIN";


	// Properties
	var $UserId;
	var $OpenIdProviderId;
	var $Login;

	// Fields

	function UserOpenId() {
		$this->table = self::table;
		parent::__construct(-1, "");
	}

	function Clear() {
		$this->UserId = -1;
		$this->OpenIdProviderId = -1;
		$this->Login = "";
	}

	function IsFull() {
		return ($this->UserId > 0 && $this->OpenIdProviderId > 0 && $this->Login);
	}

	function FillFromResult($result) {
		$this->UserId = $result->Get(self::USER_ID);
		$this->OpenIdProviderId = $result->Get(self::OPEN_ID_PROVIDER_ID);
		$this->Login = $result->Get(self::LOGIN);
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
		$s.= "<li>".self::OPEN_ID_PROVIDER_ID.": ".$this->OpenIdProviderId."</li>\n";
		$s.= "<li>".self::LOGIN.": ".$this->Login."</li>\n";
		if (!$this->IsFull()) {
			$s.= "<li> <b>User OpenId is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

/*	function ToJs() {
		return "new tagdto("
.round($this->TagId).","
.round($this->RecordId).")";
	}*/

	function Save() {
	 global $db;
		if ($this->IsConnected() && $this->IsFull()) {
			$q = $db->Query($this->CreateExpression());
			return true;
		}
	}

	function Delete() {
		if ($this->IsFull()) {
			$this->GetByCondition("", $this->DeleteExpression());
			$this->Clear();
			return true;
		}
		return false;
	}


	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::USER_ID.", 
	t1.".self::OPEN_ID_PROVIDER_ID.",
	t1.".self::LOGIN."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::USER_ID.",
".self::OPEN_ID_PROVIDER_ID.",
".self::LOGIN.")
VALUES
(".round($this->UserId).",
".round($this->OpenIdProviderId).",
".SqlQuote($this->Login).")";
	}

	function CreateLinkedExpression($tagTitle) {
		return "INSERT INTO ".$this->table." 
(".self::TAG_ID.",
".self::RECORD_ID.")
VALUES
((SELECT ".Tag::TAG_ID." FROM ".Tag::table." WHERE ".Tag::TITLE."='".SqlQuote($tagTitle)."' LIMIT 1),
".round($this->RecordId).")";
	}

	function UpdateExpression() {
		return "";
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE 
".self::USER_ID."=".round($this->UserId)." AND 
".self::OPEN_ID_PROVIDER_ID."=".round($this->OpenIdProviderId)." AND
".self::LOGIN."=".SqlQuote($this->Login);
	}

	function DeleteUserIdsExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::USER_ID."=".round($this->UserId);
	}
}

?>