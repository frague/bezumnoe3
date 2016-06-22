<?php

class News extends EntityBase {
    // Constants
    const table = "news";

    const OWNER_ID = "OWNER_ID";
    const TITLE = "TITLE";
    const DESCRIPTION = "DESCRIPTION";

    // Properties
    var $Title;
    var $Desription;

    // Fields

    function News($id = -1) {
        $this->table = self::table;
        parent::__construct($id, self::OWNER_ID);
    }

    function IsEmpty() {
        return $this->Id == 0;
    }

    function Clear() {
        $this->Id = 0;      // Important not -1
        $this->Title = "";
        $this->Description = "";
    }

    function FillFromResult($result) {
        $this->Id = $result->Get(self::OWNER_ID);
        $this->Title = $result->Get(self::TITLE);
        $this->Description = $result->Get(self::DESCRIPTION);
    }

    function FillFromHash($hash) {
        $this->Id = round($hash[self::OWNER_ID]);
        $this->Title = UTF8toWin1251($hash[self::TITLE]);
        $this->Description = UTF8toWin1251($hash[self::DESCRIPTION]);
    }

    function GetByUserId($userId) {
        return $this->FillByCondition("t1.".self::OWNER_ID."=".SqlQuote($userId));
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li>".self::OWNER_ID.": ".$this->Id."</li>\n";
        $s.= "<li>".self::TITLE.": ".$this->Title."</li>\n";
        $s.= "<li>".self::DESCRIPTION.": ".$this->Description."</li>\n";
        if ($this->IsEmpty()) {
            $s.= "<li> <b>News section is not saved!</b>";
        }

        $s.= "</ul>";
        return $s;
    }

    function ToJs() {
        return "new ndto("
.round($this->Id).",\""
.JsQuote($this->Title)."\",\""
.JsQuote($this->Description)."\")";
    }


    function Save() {
     global $db;
        if (!$this->IsConnected()) {
            return false;
        }
        $updateFlag = false;
        if (!$this->IsEmpty()) {
            $q = $db->Query("SELECT ".self::OWNER_ID." FROM ".$this->table." WHERE ".self::OWNER_ID."=".SqlQuote($this->Id));
            $updateFlag = $q->NumRows() > 0;
        }
        if ($updateFlag === true) {
            $q->Query($this->UpdateExpression());
        } else {
            $q = $db->Query("SELECT MIN(".self::OWNER_ID.") AS ".self::OWNER_ID." FROM ".$this->table." LIMIT 1");
            if ($q->NumRows()) {
                $q->NextResult();
                $this->Id = $q->Get(self::OWNER_ID) - 1;
            } else {
                $this->Id = -1;
            }
            $q = $db->Query($this->CreateExpression());
            $this->Id = $q->GetLastId();
        }
        return mysql_error();
    }

    // SQL
    function ReadExpression() {
        return "SELECT
    t1.".self::OWNER_ID.",
    t1.".self::TITLE.",
    t1.".self::DESCRIPTION."
FROM
    ".$this->table." AS t1
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table."
(".self::OWNER_ID.",
".self::TITLE.",
".self::DESCRIPTION."
)
VALUES
('".round($this->Id)."',
'".SqlQuote($this->Title)."',
'".SqlQuote($this->Description)."'
)";
    }

    function UpdateExpression() {
        $result = "UPDATE ".$this->table." SET
".self::OWNER_ID."='".round($this->Id)."',
".self::TITLE."='".SqlQuote($this->Title)."',
".self::DESCRIPTION."='".SqlQuote($this->Description)."'
WHERE
    ".self::OWNER_ID."=".round($this->Id);
        return $result;
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table." WHERE ".self::OWNER_ID."=".round($this->Id);
    }
}

?>
