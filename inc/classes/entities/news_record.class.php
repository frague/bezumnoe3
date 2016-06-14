<?php

class NewsRecord extends EntityBase {
    // Constants
    const table = "news_records";

    const NEWS_RECORD_ID = "NEWS_RECORD_ID";
    const OWNER_ID = "OWNER_ID";
    const AUTHOR_ID = "AUTHOR_ID";
    const DATE = "DATE";
    const TITLE = "TITLE";
    const CONTENT = "CONTENT";
    const IS_HIDDEN = "IS_HIDDEN";

    // Properties
    var $OwnerId;
    var $AuthorId;
    var $Date;
    var $Title;
    var $Content;
    var $IsHidden;

    // Fields

    function NewsRecord($id = -1) {
        $this->table = self::table;
        parent::__construct($id, self::NEWS_RECORD_ID);
        $this->SearchTemplate = "t1.".self::TITLE." LIKE '%#WORD#%' OR t1.".self::CONTENT." LIKE '%#WORD#%'";
    }

    function Clear() {
        $this->Id = -1;
        $this->OwnerId = -1;
        $this->AuthorId = -1;
        $this->Date = NowDateTime();
        $this->Title = "";
        $this->Content = "";
        $this->IsHidden = false;
    }

    function FillFromResult($result) {
        $this->Id = $result->Get(self::NEWS_RECORD_ID);
        $this->OwnerId = $result->Get(self::OWNER_ID);
        $this->AuthorId = $result->Get(self::AUTHOR_ID);
        $this->Date = $result->Get(self::DATE);
        $this->Title = $result->Get(self::TITLE);
        $this->Content = $result->Get(self::CONTENT);
        $this->IsHidden = $result->Get(self::IS_HIDDEN) > 0;
    }

    function FillFromHash($hash) {
        $this->Id = round($hash[self::NEWS_RECORD_ID]);
        $this->OwnerId = round($hash[self::OWNER_ID]);
        $this->AuthorId = round($hash[self::AUTHOR_ID]);
        $this->Date = $hash[self::DATE];
        if (!$this->Date) {
            $this->Date = NowDateTime();
        }
        $this->Title = UTF8toWin1251($hash[self::TITLE]);
        $this->Content = UTF8toWin1251($hash[self::CONTENT]);
        $this->IsHidden = Boolean($hash[self::IS_HIDDEN]);
    }

    function GetByOwnerId($ownerId, $from = 0, $amount = 0, $condition = "") {
        return $this->GetByCondition(
            ($condition  ? $condition." AND " : "").
            "t1.".self::OWNER_ID."=".round($ownerId).
            " ORDER BY t1.".self::DATE." DESC".($amount ? " LIMIT ".($from ? $from."," : "").$amount : ""));
    }

    function GetRecordsCount($owner_id, $condition) {
        return $this->GetResultsCount(($condition  ? $condition." AND " : "")."t1.".self::OWNER_ID."=".round($owner_id));
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li>".self::NEWS_RECORD_ID.": ".$this->Id."</li>\n";
        $s.= "<li>".self::OWNER_ID.": ".$this->OwnerId."</li>\n";
        $s.= "<li>".self::AUTHOR_ID.": ".$this->AuthorId."</li>\n";
        $s.= "<li>".self::DATE.": ".$this->Date."</li>\n";
        $s.= "<li>".self::TITLE.": ".$this->Title."</li>\n";
        $s.= "<li>".self::CONTENT.": ".$this->Content."</li>\n";
        $s.= "<li>".self::IS_HIDDEN.": ".$this->IsHidden."</li>\n";
        if ($this->IsEmpty()) {
            $s.= "<li> <b>News record is not saved!</b>";
        }

        $s.= "</ul>";
        return $s;
    }

    function ToPrint() {
        echo "<li> <h6><span>".PrintableDay($this->Date)."</span> ".$this->Title."</h6>";
        echo nl2br(trim($this->Content));
    }

    function ToJs() {
        return "new nrdto("
.round($this->Id).","
.round($this->OwnerId).",\""
.JsQuote($this->Title)."\",\""
.JsQuote($this->Content)."\","
.Boolean($this->IsHidden).",\""
.JsQuote(substr($this->Date, 0, 10))."\")";
    }

    // SQL
    function ReadExpression() {
        return "SELECT
    t1.".self::NEWS_RECORD_ID.",
    t1.".self::OWNER_ID.",
    t1.".self::AUTHOR_ID.",
    t1.".self::DATE.",
    t1.".self::TITLE.",
    t1.".self::CONTENT.",
    t1.".self::IS_HIDDEN."
FROM
    ".$this->table." AS t1
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table."
(".self::OWNER_ID.",
".self::AUTHOR_ID.",
".self::DATE.",
".self::TITLE.",
".self::CONTENT.",
".self::IS_HIDDEN."
)
VALUES
('".round($this->OwnerId)."',
".round($this->AuthorId).",
'".SqlQuote($this->Date)."',
'".SqlQuote($this->Title)."',
'".SqlQuote($this->Content)."',
".Boolean($this->IsHidden)."
)";
    }

    function UpdateExpression() {
        $result = "UPDATE ".$this->table." SET
".self::OWNER_ID."='".round($this->OwnerId)."',
".self::AUTHOR_ID."=".round($this->AuthorId).",
".self::DATE."='".SqlQuote($this->Date)."',
".self::TITLE."='".SqlQuote($this->Title)."',
".self::CONTENT."='".SqlQuote($this->Content)."',
".self::IS_HIDDEN."=".Boolean($this->IsHidden)."
WHERE
    ".self::NEWS_RECORD_ID."=".SqlQuote($this->Id);
        return $result;
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table." WHERE ".self::NEWS_RECORD_ID."=".round($this->Id);
    }
}

?>
