<?

class JournalFriend extends ForumUser {
	function ToJs($login) {
		return "new jfdto(".$this->UserId.",\"".JsQuote($login)."\")";
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::FORUM_ID.": ".$this->ForumId."</li>\n";
		$s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
		$s.= "<li>".self::IS_MODERATOR.": ".$this->IsModerator."</li>\n";

		if ($this->IsEmpty()) {
			$s.= "<li> <b>Journal friend is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	function GetByJournalId($journalId) {
		return $this->GetByCondition(
			"t1.".self::FORUM_ID."=".round($journalId),
			$this->ReadWithLoginExpression());
	}

	function FillByJournalAndUser($journalId, $userId) {
		return $this->FillByCondition(
			"t1.".self::FORUM_ID."=".round($journalId)." AND ".
			"t1.".self::USER_ID."=".round($userId));
	}

	/* SQL */

	function ReadWithLoginExpression() {
		return "SELECT 
	t1.".self::USER_ID.",
	t2.".User::LOGIN.",
	t3.".JournalSettings::ALIAS."
FROM
	".$this->table." AS t1 
	LEFT JOIN ".User::table." AS t2 ON t2.".User::USER_ID."=t1.".self::USER_ID."
	LEFT JOIN ".JournalSettings::table." AS t3 ON t3.".User::USER_ID."=t1.".self::USER_ID."
WHERE
	##CONDITION##
ORDER BY
	t2.".User::LOGIN;
	}
}

?>