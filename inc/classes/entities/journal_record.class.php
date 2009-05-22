<?

class JournalRecord extends ForumRecordBase {

	function ToPrint($login = "") {
		$result = "<h2>".$this->Title."</h2>";
		$result .= nl2br($this->Content);
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

	function ToJs($mark = "") {
		$title = strip_tags($this->Title);
		$content = substr(strip_tags($this->Content), 0, 100);
/*		if ($mark) {
			$title = HightlightWords($title, $mark);
			$content = HightlightWords($content, $mark);
		}*/
		return "new jrdto(\"".
JsQuote($this->Id)."\",\"".
JsQuote($title)."\",\"".
JsQuote($content)."\",\"".
JsQuote($this->Date)."\",".
$this->AnswersCount.",".
round($this->Type).")";
	}

	function ToFullJs() {
		return "new Array(\"".
round($this->Id)."\",\"".
JsQuote($this->Title)."\",\"".
JsQuote($this->Content)."\",\"".
JsQuote($this->Date)."\",".
round($this->Type).",".
Boolean($this->IsCommentable).");";
	}

	function GetJournalRecords($user, $from = 0, $limit, $condition) {
		$from = round($from);
		$limit = round($limit);

	  	return $this->GetByCondition(
	  		$condition." LIMIT ".($from ? $from."," : "").$limit,
	  		$this->JournalPostsExpression($user)
	  	); 
	}
	
	function GetJournalTopics($user, $from = 0, $limit, $forumId = 0, $condition = "") {
		$forumId = round($forumId);
	  	return $this->GetJournalRecords(
	  		$user, 
	  		$from,
	  		$limit,
	  		($condition ? $condition." AND " : "").($forumId > 0 ? "t1.".self::FORUM_ID."=".$forumId." AND " : "")."LENGTH(t1.".self::INDEX.")=4
	  		ORDER BY t1.".self::DATE." DESC"
	  	); 
	}
	
	function JournalPostsExpression($user) {
		return str_replace(
		"WHERE",
		"LEFT JOIN ".Journal::table." AS t5 ON t5.".Journal::FORUM_ID."=t1.".self::FORUM_ID."
WHERE 
	t5.".Journal::TYPE."='".Journal::TYPE_JOURNAL."' AND ",
		$this->ReadThreadExpression($user));
	}
}

?>