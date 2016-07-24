<?php

class Captcha extends EntityBase {
    // Constants
    const table = "captchas";

    const GUID = "GUID";
    const VALUE = "VALUE";
    const DATE = "DATE";

    // Properties
    var $Guid;
    var $Value;
    var $Date;

    function Captcha() {
        $this->table = self::table;
        $this->Clear();
    }

    function Clear() {
        $this->Guid = "";
        $this->Value = "";
        $this->Date = NowDateTime();
    }

    function Generate() {
        $this->Guid = MakeGuid(10);
        $this->Value = MakeGuid(4);
        $this->Value = str_replace("O", "0", strtoupper($this->Value));
    }

    function IsEmpty() {
        return !$this->Guid || !$this->Value;
    }

    function FillFromResult($result) {
        $this->UserId = $result->Get(self::USER_ID);
        $this->ForumId = $result->Get(self::FORUM_ID);
        $this->IsModerator = $result->Get(self::ACCESS);
    }

    function GetByUserId($userId) {
        return $this->FillByCondition("t1.".self::USER_ID."=".round($userId));
    }

    function GetByForumId($forumId) {
        return $this->FillByCondition("t1.".self::FORUM_ID."=".round($forumId));
    }

    function GetFor($userId, $forumId) {
        $this->FillByCondition("t1.".self::USER_ID."=".round($userId)." AND t1.".self::FORUM_ID."=".round($forumId)." LIMIT 1");
    }

    function Save($by_query = "") {
     global $db;
        if ($this->IsConnected() && $this->IsFull()) {
            // Check duplicates
            $q = $db->Query("SELECT 
   ".self::USER_ID."
FROM 
  ".$this->table."
WHERE
  ".self::USER_ID." = ".round($this->UserId)." AND
  ".self::FORUM_ID." = ".round($this->ForumId)." LIMIT 1");

            if ($q->NumRows()) {
                return false;
            }

            $q = $db->Query($this->CreateExpression());
            $this->Id = $q->GetLastId();
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

    function DeleteForForum($forum_id) {
        return $this->GetByCondition("", $this->DeleteForForumExpression($forum_id));
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
        $s.= "<li>".self::FORUM_ID.": ".$this->ForumId."</li>\n";
        $s.= "<li>".self::ACCESS.": ".$this->Access."</li>\n";
        if ($this->IsEmpty()) {
            $s.= "<li> <b>ForumUser is not saved!</b>";
        }
        $s.= "</ul>";
        return $s;
    }

    // SQL
    function ReadExpression() {
        return "SELECT 
    t1.".self::USER_ID.",
    t1.".self::FORUM_ID.",
    t1.".self::ACCESS."
FROM
    ".$this->table." AS t1 
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table." 
(".self::USER_ID.", 
".self::FORUM_ID.",
".self::ACCESS."
)
VALUES
(".round($this->UserId).", 
".round($this->ForumId).",
".Boolean($this->IsModerator)."
)";
    }

    function UpdateExpression() {
        return "";
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table."
WHERE
    ".self::USER_ID."=".SqlQuote($this->UserId)." AND
    ".self::FORUM_ID."=".SqlQuote($this->ForumId);
    }

    function DeleteForForumExpression($FORUM_id) {
        return "DELETE FROM ".$this->table."
WHERE
    ".self::FORUM_ID."=".round($forum_id);
    }
}

?>