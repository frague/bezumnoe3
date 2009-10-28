<?

class JournalRecord extends ForumRecordBase {

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
	function GetMixedJournalsTopics($userId, $from = 0, $limit, $condition = "") {
		$forumId = round($forumId);
	  	return $this->GetMixedJournalsRecords(
	  		$userId, 
	  		$from,
	  		$limit,
	  		($condition ? $condition." AND " : "").
	  		"LENGTH(t1.".self::INDEX.")=4
	  		ORDER BY t1.".self::DATE." DESC"
	  	); 
	}

	//------ SQL ------

	// Journal postst SQL expression
	function JournalPostsExpression($access) {
		return str_replace(
		"WHERE",
		"	LEFT JOIN ".Journal::table." AS t5 ON t5.".Journal::FORUM_ID."=t1.".self::FORUM_ID."
WHERE 
	t5.".Journal::TYPE."='".Journal::TYPE_JOURNAL."' AND ",
		$this->ReadThreadExpression($access));
	}

	// Journal postst by tag SQL expression
	function JournalPostsByTagExpression($access) {
		$result = str_replace(
		"WHERE",
		"	LEFT JOIN ".Journal::table." AS t5 ON t5.".Journal::FORUM_ID."=t1.".self::FORUM_ID."
	JOIN ".RecordTag::table." AS t6 ON t6.".RecordTag::RECORD_ID."=t1.".self::RECORD_ID."
	JOIN ".Tag::table." AS t7 ON t7.".Tag::TAG_ID."=t6.".RecordTag::TAG_ID."
WHERE 
	t5.".Journal::TYPE."='".Journal::TYPE_JOURNAL."' AND ",
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
	t5.".Journal::TYPE."='".Journal::TYPE_JOURNAL."' AND ",
		$this->ReadExpression());
		$result = str_replace(
			"FROM",
			",
	t6.".JournalSettings::ALIAS."
FROM", 
			$result
		);
		return $result;
	}
}

?>