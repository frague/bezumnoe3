<?php

class RecordTag extends EntityBase {
    // Constants
    const table = "forum_records_tags";

    const TAG_ID = "TAG_ID";
    const RECORD_ID = "RECORD_ID";


    // Properties
    var $Id;
    var $RecordId;

    // Fields

    function RecordTag($recordId = -1, $tagId = -1) {
        $this->table = self::table;
        parent::__construct(-1, "");
        
        $this->RecordId = $recordId;
        $this->TagId = $tagId;
    }

    function Clear() {
        $this->TagId = -1;
        $this->RecordId = -1;
    }

    function IsFull() {
        return ($this->TagId > 0 && $this->RecordId > 0);
    }

    function GetExisting() {
        return $this->GetByCondition("t1.".self::RECORD_ID."=".round($this->RecordId)." AND t1.".self::TAG_ID."=".round($this->TagId));
    }

    function FillFromResult($result) {
        $this->TagId = $result->Get(self::TAG_ID);
        $this->RecordId = $result->Get(self::RECORD_ID);
    }

    function FillFromHash($hash) {
        $this->TagId = round($hash[self::TAG_ID]);
        $this->RecordId = round($hash[self::RECORD_ID]);
    }

    function DeleteRecordTags($recordId) {
        $recordId = round($recordId);
        if ($recordId > 0) {
            $this->GetByCondition(self::RECORD_ID."=".$recordId, $this->DeleteRecordTagsExpression());
        }
    }

    function BulkCreate($values, $recordId) {
        $recordId = round($recordId);
        $this->DeleteRecordTags($recordId);
        for ($i = 0; $i < sizeof($values); $i++) {
            $this->RecordId = $recordId;
            $this->GetByCondition("", $this->CreateLinkedExpression($values[$i]));
        }
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li>".self::TAG_ID.": ".$this->TagId."</li>\n";
        $s.= "<li>".self::RECORD_ID.": ".$this->RecordId."</li>\n";
        if ($this->IsEmpty()) {
            $s.= "<li> <b>Record Tag is not saved!</b>";
        }

        $s.= "</ul>";
        return $s;
    }

    function ToJs() {
        return "new tagdto("
.round($this->TagId).","
.round($this->RecordId).")";
    }


    function Save($by_query = "") {
     global $db;
        if ($this->IsConnected() && $this->IsFull()) {
            // Check duplicate
            $q = $this->GetExisting();
            if ($q->NumRows()) {
                return false;
            }
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
    t1.".self::TAG_ID.", 
    t1.".self::RECORD_ID."
FROM 
    ".$this->table." AS t1 
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table." 
(".self::TAG_ID.",
".self::RECORD_ID.")
VALUES
(".round($this->RecordId).",
".round($this->RecordId).")";
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
        return "DELETE FROM ".$this->table." WHERE ".self::TAG_ID."=".round($this->TagId)." AND ".self::RECORD_ID."=".round($this->RecordId);
    }

    function DeleteRecordTagsExpression() {
        return "DELETE FROM ".$this->table." WHERE ##CONDITION##";
    }

    function DeleteUnlinkedExpression() {
        return "DELETE ".$this->table.".* FROM ".$this->table."
LEFT JOIN ".ForumRecordBase::table." ON ".$this->table.".".self::RECORD_ID."=".ForumRecordBase::table.".".ForumRecordBase::RECORD_ID."
LEFT JOIN ".Tag::table." ON ".$this->table.".".self::TAG_ID."=".Tag::table.".".Tag::TAG_ID."
WHERE 
    ".ForumRecordBase::table.".".ForumRecordBase::RECORD_ID." IS NULL OR
    ".Tag::table.".".Tag::TAG_ID." IS NULL";
    }
}

?>