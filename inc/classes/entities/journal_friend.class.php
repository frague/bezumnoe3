<?

class JournalFriend extends EntityBase {
    // Constants
    const table = "journal_relations";

    const FORUM_ID = "FORUM_ID";
    const FRIENDLY_FORUM_ID = "FRIENDLY_FORUM_ID";

    // Properties
    var $ForumId;
    var $FriendlyForumId;

    function JournalFriend($forumId = -1, $friendlyForumId = -1) {
        $this->table = self::table;
        $this->Clear();

        $this->ForumId = $forumId;
        $this->FriendlyForumId = $friendlyForumId;
    }

    function Clear() {
        $this->ForumId = -1;
        $this->FriendlyForumId = -1;
    }

    function IsFull() {
        return $this->ForumId > 0 && $this->FriendlyForumId > 0;
    }

    // Fills
    function GetFor($forumId, $friendlyForumId) {
        $this->FillByCondition("t1.".self::FRIENDLY_FORUM_ID."=".round($friendlyForumId)." AND t1.".self::FORUM_ID."=".round($forumId)." LIMIT 1");
    }

    function FillFromResult($result) {
        $this->ForumId = $result->Get(self::FORUM_ID);
        $this->FriendlyForumId = $result->Get(self::FRIENDLY_FORUM_ID);
    }

    function FillByForumId($forumId) {
        return $this->FillByCondition("t1.".self::FORUM_ID."=".round($forumId));
    }


    // Gets
    function GetByFriendlyForumId($forumId) {
        return $this->GetByCondition("t1.".self::FRIENDLY_FORUM_ID."=".round($forumId), $this->ReadExtendedExpression());
    }

    function GetByForumId($forumId) {
        return $this->GetByCondition("t1.".self::FORUM_ID."=".round($forumId), $this->ReadExtendedExpression());
    }



    function ToJs($title, $login) {
        return "new fjdto(\"".
JsQuote($this->ForumId)."\",\"".
JsQuote($title)."\", \"".
JsQuote($login)."\", \"".
JsQuote($this->FriendlyForumId)."\")";
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li>".self::FORUM_ID.": ".$this->ForumId."</li>\n";
        $s.= "<li>".self::FRIENDLY_FORUM_ID.": ".$this->FriendlyForumId."</li>\n";
        if ($this->IsEmpty()) {
            $s.= "<li> <b>Journal relation is not saved!</b>";
        }
        $s.= "</ul>";
        return $s;
    }

    // SQL

    function Save() {
     global $db;
        if ($this->IsConnected() && $this->IsFull()) {
            // Check duplicates
            $q = $db->Query("SELECT
   ".self::FORUM_ID."
FROM
  ".$this->table."
WHERE
  ".self::FRIENDLY_FORUM_ID." = ".round($this->FriendlyForumId)." AND
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

    function ReadExpression() {
        return "SELECT
    t1.".self::FORUM_ID.",
    t1.".self::FRIENDLY_FORUM_ID."
FROM
    ".$this->table." AS t1
WHERE
    ##CONDITION##";
    }

    function ReadExtendedExpression() {
        return "SELECT
    t1.".self::FORUM_ID.",
    t1.".self::FRIENDLY_FORUM_ID.",
    t2.".Forum::TITLE.",
    t3.".User::LOGIN.",
    t4.".JournalSettings::ALIAS."
FROM
    ".$this->table." AS t1
    LEFT JOIN ".Forum::table." AS t2 ON t2.".Forum::FORUM_ID."=t1.".self::FRIENDLY_FORUM_ID."
    LEFT JOIN ".User::table." AS t3 ON t3.".User::USER_ID."=t2.".Forum::LINKED_ID."
    LEFT JOIN ".JournalSettings::table." AS t4 ON t4.".Forum::FORUM_ID."=t1.".self::FRIENDLY_FORUM_ID."
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table."
(".self::FORUM_ID.",
".self::FRIENDLY_FORUM_ID."
)
VALUES
(".round($this->ForumId).",
".round($this->FriendlyForumId)."
)";
    }

    function UpdateExpression() {
        return "";
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table."
WHERE
    ".self::FRIENDLY_FORUM_ID."=".SqlQuote($this->FriendlyForumId)." AND
    ".self::FORUM_ID."=".SqlQuote($this->ForumId);
    }

    function DeleteForForumExpression($forumId) {
        return "DELETE FROM ".$this->table."
WHERE
    ".self::FORUM_ID."=".round($forumId);
    }

    function DeleteForumExpression($forumId) {
        return "DELETE FROM ".$this->table."
WHERE
    ".self::FORUM_ID."=".round($forumId)." OR
    ".self::FRIENDLY_FORUM_ID."=".round($forumId);
    }

    // Expression to get user forums
    function RelatedJournalsExpression($forumId) {
        $forumId = round($forumId);

        return "SELECT DISTINCT
    t2.".Forum::FORUM_ID.",
    t2.".Forum::TITLE.",
    t3.".User::LOGIN."
FROM
    ".Forum::table." AS t2
    LEFT JOIN ".self::table." AS t1 ON t2.".Forum::FORUM_ID."=t1.".self::FORUM_ID."
    LEFT JOIN ".User::table." AS t3 ON t3.".User::USER_ID."=t2.".Forum::LINKED_ID."
WHERE
    (t1.".self::FORUM_ID."=".$forumId.")
ORDER BY t2.".Forum::FORUM_ID;
    }
}

?>
