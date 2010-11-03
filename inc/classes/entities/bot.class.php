<?

class Bot extends EntityBase {
	// Constants
	const table = "bots";

	const BOT_ID = "BOT_ID";
	const TYPE = "TYPE";
	const USER_ID = "USER_ID";
	const ROOM_ID = "ROOM_ID";
	const IS_ACTIVE = "IS_ACTIVE";

	const TYPE_YTKA = "1";
	const TYPE_VICTORINA = "2";
	const TYPE_LYNGVIST = "3";
	
	// Properties
	var $Id;
	var $Type;

	// Fields

	function Bot($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::BOT_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->Type = "";
		$this->UserId = -1;
		$this->RoomId = -1;
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::BOT_ID);
		$this->Type = $result->Get(self::TYPE);
		$this->UserId = $result->Get(self::USER_ID);
		$this->RoomId = $result->Get(self::ROOM_ID);
		$this->IsActive = $result->Get(self::IS_ACTIVE);
	}

	function FillFromHash($hash) {
		$this->Id = round($hash[self::BOT_ID]);
		$this->Type = round($hash[self::TYPE]);
		$this->UserId = round($hash[self::USER_ID]);
		$this->RoomId = round($hash[self::ROOM_ID]);
		$this->IsActive = Boolean($hash[self::IS_ACTIVE]);
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::BOT_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::TYPE.": ".$this->Type."</li>\n";
		$s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
		$s.= "<li>".self::ROOM_ID.": ".$this->RoomId."</li>\n";
		$s.= "<li>".self::IS_ACTIVE.": ".$this->IsActive."</li>\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Bot is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::BOT_ID.", 
	t1.".self::TYPE.",
	t1.".self::USER_ID.",
	t1.".self::ROOM_ID.",
	t1.".self::IS_ACTIVE."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##
ORDER BY
	t1.".self::TYPE." ASC";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." (
	".self::TYPE.",
	".self::USER_ID.",
	".self::ROOM_ID.",
	".self::IS_ACTIVE."
) VALUES (
	'".round($this->Type)."', 
	".NullableId($this->UserId).", 
	".NullableId($this->RoomId).", 
	".Boolean($this->IsActive)."
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
	".self::TYPE."=".round($this->Type).",
	".self::USER_ID."=".NullableId($this->UserId).",
	".self::ROOM_ID."=".NullableId($this->RoomId).",
	".self::IS_ACTIVE."=".Boolean($this->IsActive)."
WHERE 
	".self::BOT_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::BOT_ID."=".SqlQuote($this->Id);
	}
}

?>