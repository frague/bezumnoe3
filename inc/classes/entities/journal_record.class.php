<?

class JournalRecord extends ForumRecordBase {

    var $RecordType = Forum::TYPE_JOURNAL;

    function ToPrint($login = "") {
        $result = "<h2>".$this->Title."</h2>";
        $result .= Smartnl2br(ereg_replace("##[a-zA-Z]+(=(([^#]|#[^#])+)){0,1}##", "\\2", $this->Content));
        if ($login) {
            $result .= "<author>".$login."</author>";
        }
        return $result;
    }

    function MakeCss() {
        return $this->Type ? ($this->Type == 1 ? "Friends" : "Private") : "";
    }

    function ToLink($trimBy = 0, $alias = "") {
        $title = $trimBy ? TrimBy($this->Title, $trimBy) : $this->Title;
        return JournalSettings::MakeLink($alias, $title, $this);
    }

    function ToHref($alias = "") {
        return "http://www.bezumnoe.ru".JournalSettings::MakeHref($alias, $this->Id);
    }

    function GetImageUrl() {
        if ($this->IsEmpty()) {
            return "";
        }
        if (preg_match("/<img[^>]+src=[\"']*([^\"' >]+)[\"'][^>]*>/", $this->Content, $matches)) {
            if (sizeof($matches)) {
                if (strrpos($matches[1], "://") === false) {
                    return "http://bezumnoe.ru".$matches[1];
                } else {
                    return $matches[1];
                }
            }
        }
        return "";
    }


    // ----- Single Journal -----
    // Gets journal records by condition
    function GetJournalRecords($access, $from = 0, $limit, $condition) {
        $from = round($from);
        $limit = round($limit);

        return $this->GetByCondition(
            $condition." LIMIT ".($from ? $from."," : "").$limit,
            $this->JournalPostsExpression($access)
        );
    }

    // Gets journal topics by condition
    function GetJournalTopics($access, $from = 0, $limit, $forumId = 0, $condition = "") {
        $forumId = round($forumId);

        return $this->GetJournalRecords(
            $access,
            $from,
            $limit,
            ($condition ? $condition." AND " : "").
            ($forumId > 0 ? "t1.".self::FORUM_ID."=".$forumId." AND " : "")."LENGTH(t1.".self::INDEX.")=4
            ORDER BY t1.".self::DATE." DESC"
        );
    }

    // Gets journal topics by condition
    function GetJournalTopicsByTag($access, $from = 0, $limit, $forumId = 0, $tag) {
        $forumId = round($forumId);
        $from = round($from);
        $limit = round($limit);

        return $this->GetByCondition(
            ($forumId > 0 ? "t1.".self::FORUM_ID."=".$forumId." AND " : "")."
    LENGTH(t1.".self::INDEX.")=4 AND
    t7.".Tag::TITLE."='".SqlQuote($tag)."'
            ORDER BY t1.".self::DATE." DESC
            LIMIT ".($from ? $from."," : "").$limit,
            $this->JournalPostsByTagExpression($access)
        );
    }

    // ----- Multiple Journals -----
    // Gets records from different journals with user access
    // by condition
    function GetMixedJournalsRecords($userId, $from = 0, $limit, $condition) {
        $from = round($from);
        $limit = round($limit);

        return $this->GetByCondition(
            $this->AccessExpression($userId, "t5", "t4")." AND ".
            $condition.
            " LIMIT ".($from ? $from."," : "").$limit,
            $this->MixedJournalsPostsExpression()
        );
    }

    // Gets journal topics by condition
    function GetMixedJournalsTopics($userId, $from = 0, $limit, $condition = "", $order_by_date = True) {
        $forumId = round($forumId);
        return $this->GetMixedJournalsRecords(
            $userId,
            $from,
            $limit,
            ($condition ? $condition." AND " : "").
            "LENGTH(t1.".self::INDEX.")=4
            ORDER BY ".($order_by_date ? "t1.".self::DATE." DESC" : "t1.".self::RECORD_ID." DESC")
        );
    }

    // Gets topics from different journals by array of ids
    function GetJournalsTopicsByIds($ids) {
        return $this->GetMixedJournalsTopics(0, 0, 100, JournalRecord::RECORD_ID." IN (".implode(",", $ids).")");
    }

    // Gets topics of friendly journals of given
    function GetFriendlyTopics($forumId, $from = 0, $limit) {
        return $this->GetByCondition(
            " LIMIT ".($from ? $from."," : "").$limit,
            $this->FriendlyTopicsExpression($forumId)
        );
    }

    // Gets number of friendly topics
    function GetFriendlyThreadsCount($forumId) {
        return $this->GetExpressionCount($this->FriendlyTopicsExpression($forumId));
    }

    //------ SQL ------

    // Journal postst SQL expression
    function JournalPostsExpression($access) {
        return str_replace(
        "WHERE",
        "   LEFT JOIN ".Journal::table." AS t5 ON t5.".Journal::FORUM_ID."=t1.".self::FORUM_ID."
WHERE
    t5.".Journal::TYPE."='".$this->RecordType."' AND ",
        $this->ReadThreadExpression($access));
    }

    // Journal postst by tag SQL expression
    function JournalPostsByTagExpression($access) {
        $result = str_replace(
        "WHERE",
        "   LEFT JOIN ".Journal::table." AS t5 ON t5.".Journal::FORUM_ID."=t1.".self::FORUM_ID."
    JOIN ".RecordTag::table." AS t6 ON t6.".RecordTag::RECORD_ID."=t1.".self::RECORD_ID."
    JOIN ".Tag::table." AS t7 ON t7.".Tag::TAG_ID."=t6.".RecordTag::TAG_ID."
WHERE
    t5.".Journal::TYPE."='".$this->RecordType."' AND ",
        $this->ReadThreadExpression($access));

        $result = str_replace(
            "SELECT",
            "SELECT DISTINCT",
            $result
        );
        return $result;
    }

    // Journal posts from different journals with access expression
    function MixedJournalsPostsExpression() {
        $userId = round($userId);

        $result = str_replace(
        "WHERE",
        "
    LEFT JOIN ".ForumUser::table." AS t4 ON t4.".ForumUser::USER_ID."=".$userId." AND t4.".ForumUser::FORUM_ID."=t1.".JournalRecord::RECORD_ID."
    LEFT JOIN ".Journal::table." AS t5 ON t5.".Journal::FORUM_ID."=t1.".self::FORUM_ID."
    LEFT JOIN ".JournalSettings::table." AS t6 ON t6.".JournalSettings::FORUM_ID."=t1.".self::FORUM_ID."
WHERE
    t5.".Journal::TYPE."='".$this->RecordType."' AND ",
        $this->ReadExpression());
        $result = str_replace(
            "FROM",
            ",
    t5.".Journal::DESCRIPTION.",
    t6.".JournalSettings::ALIAS."
FROM",
            $result
        );
        return $result;
    }

    // Journal posts from different journals with access expression
    function FriendlyTopicsExpression($forumId) {
        $forumId = round($forumId);

        $result = str_replace(
        "WHERE",
        "
    JOIN ".JournalFriend::table." AS t4 ON t4.".JournalFriend::FORUM_ID."=".$forumId." AND t4.".JournalFriend::FRIENDLY_FORUM_ID."=t1.".JournalRecord::FORUM_ID."
    LEFT JOIN ".JournalSettings::table." AS t5 ON t5.".JournalSettings::FORUM_ID."=t1.".self::FORUM_ID."
WHERE
    LENGTH(t1.".self::INDEX.")=4 AND
    t1.".self::TYPE."='".self::TYPE_PUBLIC."'
    ORDER BY t1.".self::DATE." DESC",
        $this->ReadExpression());

        $result = str_replace(
            "FROM",
            ",
    t5.".JournalSettings::ALIAS."
FROM",
            $result
        );
        return $result;
    }
}

?>
