<?

class TelegramId extends EntityBase {
    // Constants
    const table = "telegram_ids";

    const UUID = "UUID";
    const TELEGRAM_USER_ID = "TELEGRAM_USER_ID";
    const TELEGRAM_USERNAME = "TELEGRAM_USERNAME";

    // Properties
    var $Uuid;
    var $TelegramUserId;
    var $TelegramUsername;

    // Fields

    function Delete() {
     global $db;
        if (!$this->IsConnected()) {
            return false;
        }
        $q = $db->Query($this->DeleteExpression());
        return true;
    }

    function TelegramId($id = 0) {
        $this->table = self::table;
        parent::__construct($uuid, self::TELEGRAM_USER_ID);
        $this->TelegramUserId = $id;
    }

    function Clear() {
        $this->Uuid = MakeGuid(10);
        $this->TelegramUserId = 0;
        $this->TelegramUsername = "";
    }

    function FillFromResult($result) {
        $this->Uuid = $result->Get(self::UUID);
        $this->TelegramUserId = $result->Get(self::TELEGRAM_USER_ID);
        $this->TelegramUsername = $result->Get(self::TELEGRAM_USERNAME);
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li>".self::UUID.": ".$this->Uuid."</li>\n";
        $s.= "<li>".self::TELEGRAM_USER_ID.": ".$this->TelegramUserId."</li>\n";
        $s.= "<li>".self::TELEGRAM_USERNAME.": ".$this->TelegramUsername."</li>\n";
        $s.= "</ul>";
        return $s;
    }

    // SQL
    function ReadExpression() {
        return "SELECT
    t1.".self::UUID.",
    t1.".self::TELEGRAM_USER_ID.",
    t1.".self::TELEGRAM_USERNAME."
FROM
    ".$this->table." AS t1
WHERE
    ##CONDITION##
ORDER BY
    t1.".self::UUID." ASC";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table."
(".self::UUID.",
".self::TELEGRAM_USER_ID.",
".self::TELEGRAM_USERNAME.")
VALUES
('".SqlQuote($this->Uuid)."',
'".SqlQuote($this->TelegramUserId)."',
'".SqlQuote($this->TelegramUsername)."')";
    }

    function UpdateExpression() {
        $result = "UPDATE ".$this->table." SET
".self::UUID."='".SqlQuote($this->Uuid)."',
".self::TELEGRAM_USERNAME."='".SqlQuote($this->TelegramUsername)."'
WHERE
    ".self::TELEGRAM_USER_ID."=".SqlQuote($this->TelegramUserId);
        return $result;
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table." WHERE ".self::TELEGRAM_USER_ID."=".SqlQuote($this->TelegramUserId);
    }
}

?>
