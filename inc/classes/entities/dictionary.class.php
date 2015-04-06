<?

abstract class DictionaryItem extends EntityBase {
    const RANDOM_INDEX = "RANDOM_INDEX";

    // Fields
    var $FrequencyFieldName = "", $Size = 1000;

    function DictionaryItem($id, $table, $index_name, $freq_name, $size = 1000) {
        $this->table = $table;
        $this->FrequencyFieldName = $freq_name;
        $this->Size = $size;

        parent::__construct($id, $index_name);
    }

    function TruncateDictionary() {
      global $db;

        if (!$this->IsConnected()) {
            return false;
        }
        $q = $db->Query("DELETE FROM ".$this->table." WHERE ".$this->IdentityName." IN 
    (SELECT ".$this->IdentityName." FROM ".$this->table." ORDER BY ".$this->FrequencyFieldName." ASC LIMIT ".round($this->Size).", 1000)
        ");

        return true;
    }

    function PickRandom() {
      global $db;

        if (!$this->IsConnected()) {
            return false;
        }

        $this->FillByCondition("1=1 ORDER BY ".self::RANDOM_INDEX." ASC, ".$this->FrequencyFieldName." ASC LIMIT ".mt_rand(0, 10).", 1");
        // Touch filled item
        if (!$this->IsEmpty()) {
            $db->Query("UPDATE ".$this->table." 
                SET ".$this->FrequencyFieldName."=".$this->FrequencyFieldName."+1,
                ".self::RANDOM_INDEX."=".mt_rand(0, $this->Size)."
                WHERE ".$this->IdentityName."=".$this->Id);
        }

        return $item;
    }
}

// Dictionary items
class YtkaDictionaryItem extends DictionaryItem {
    const table = "ytka_vocab";
    
    const ITEM_ID = "ITEM_ID";
    const USER_ID = "USER_ID";
    const CONTENT = "CONTENT";
    const USED_TIMES = "USED_TIMES";

    var $Id;
    var $UserId;
    var $Content;
    var $UsedTimes;


    function YtkaDictionaryItem($id = -1) {
        parent::__construct($id, self::table, self::ITEM_ID, self::USED_TIMES);
    }

    function Clear() {
        $this->Id = -1;
        $this->UserId = -1;
        $this->Content = "";
        $this->UsedTimes = 0;
    }

    function FillFromResult($result) {
        $this->Id = round($result->Get(self::ITEM_ID));
        $this->UserId = round($result->Get(self::USER_ID));
        $this->Content = $result->Get(self::CONTENT);
        $this->UsedTimes = round($result->Get(self::USED_TIMES));
    }

    // SQL
    function ReadExpression() {
        return "SELECT 
    t1.".self::ITEM_ID.", 
    t1.".self::USER_ID.", 
    t1.".self::CONTENT.", 
    t1.".self::USED_TIMES.", 
    t1.".self::RANDOM_INDEX."
FROM 
    ".$this->table." AS t1 
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table." (
    ".self::USER_ID.",
    ".self::CONTENT.",
    ".self::USED_TIMES.",
    ".self::RANDOM_INDEX."
) VALUES (
    ".round($this->UserId).",
    '".SqlQuote($this->Content)."',
    0,
    ".mt_rand(0, $this->Size)."
)";
    }

    function UpdateExpression() {
        $result = "UPDATE ".$this->table." SET 
".self::USER_ID."=".round($this->UserId).",
".self::CONTENT."='".SqlQuote($this->Content)."',
".self::USED_TIMES."=".SqlQuote($this->UsedTimes)."
WHERE 
    ".self::ITEM_ID."=".round($this->Id);
        return $result;
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table." WHERE ".self::ITEM_ID."=".round($this->Id);
    }


}

?>