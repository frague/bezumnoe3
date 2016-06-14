<?php

class TODO extends EntityBase {
    // Constants
    const table = "todo";

    const ID = "ID";
    const TITLE = "TITLE";
    const PRIORITY = "PRIORITY";
    const IS_PLANNED = "IS_PLANNED";
    const FINISH_DATE = "FINISH_DATE";

    // Properties
    var $Title;
    var $Priority;
    var $IsFinished;
    var $FinishDate;

    // Fields

    function TODO($id = -1) {
        $this->table = self::table;
        parent::__construct($id, self::ID);
    }

    function Clear() {
        $this->Id = -1;
        $this->Title = "";
        $this->Priority = 0;
        $this->IsPlanned = 0;
        $this->FinishDate = "";
    }

    function FillFromResult($result) {
        $this->Id = $result->Get(self::ROOM_ID);
        $this->Title = $result->Get(self::TITLE);
        $this->Priority = round($result->Get(self::PRIORITY));
        $this->IsPlanned = $result->Get(self::IS_PLANNED);
        $this->FinishDate = $result->Get(self::FINISH_DATE);
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li>".self::ID.": ".$this->Id."</li>\n";
        $s.= "<li>".self::TITLE.": ".$this->Title."</li>\n";
        $s.= "<li>".self::PRIORITY.": ".$this->Priority."</li>\n";
        $s.= "<li>".self::IS_PLANNED.": ".$this->IsPlanned."</li>\n";
        $s.= "<li>".self::FINISH_DATE.": ".$this->FinishDate."</li>\n";
        $s.= "</ul>";
        return $s;
    }

    function ToPrint() {
        if ($this->IsEmpty()) {
            return "";
        }
        return "<p>".$this->Title."</p>";
    }

    function ToJs() {
        return "new Room(\"".JsQuote($this->Id)."\",\"".JsQuote($this->Title)."\",\"".JsQuote($this->Topic)."\",\"".JsQuote($this->TopicLock)."\",\"".JsQuote($this->TopicAuthorId)."\",\"".JsQuote($this->TopicAuthorName)."\",".Boolean($this->IsLocked).",".Boolean($this->IsInvitationRequired).",".round($this->OwnerId).")";
    }

    // SQL
    function ReadExpression() {
        return "SELECT
    t1.".self::ID.",
    t1.".self::TITLE.",
    t1.".self::PRIORITY.",
    t1.".self::IS_PLANNED.",
    t1.".self::FINISH_DATE."
FROM
    ".$this->table." AS t1
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table."
(".self::TITLE.",
".self::PRIORITY.",
".self::IS_PLANNED.",
".self::FINISH_DATE."
)
VALUES
('".SqlQuote($this->Title)."',
".round($this->Priority).",
".Boolean($this->IsPlanned).",
".Nullable($this->FinishDate)."
)";
    }

    function UpdateExpression() {
        $result = "UPDATE ".$this->table." SET
".self::TITLE."='".SqlQuote($this->Title)."',
".self::PRIORITY."='".SqlQuote($this->Priority)."',
".self::IS_PLANNED."='".SqlQuote($this->IsPlanned)."',
".self::FINISH_DATE."='".round($this->FinishDate)."'
WHERE
    ".self::ROOM_ID."=".SqlQuote($this->Id);
        return $result;
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table." WHERE ".self::ID."=".round($this->Id);
    }
}


?>
