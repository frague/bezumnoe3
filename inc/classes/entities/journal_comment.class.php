<?

class JournalComment extends JournalRecord {

	function ToLink($trimBy = 0, $recordId) {
		$content = $trimBy ? TrimBy($this->Content, $trimBy) : $this->Content;
		return self::MakeLink($recordId, $this->Id, $content, $this->Type == self::TYPE_PROTECTED);
	}

	function ToJs() {
		$title = strip_tags($this->Title);
		$content = substr(strip_tags($this->Content), 0, 2048);

		return "new jcdto(\"".JsQuote($this->Id).
"\",\"".JsQuote($this->UserId).
"\",\"".JsQuote($this->Author).
"\",\"".JsQuote($title).
"\",\"".JsQuote($content).
"\",\"".JsQuote(PrintableDate($this->Date)).
"\",".round($this->Type).",".round($this->IsDeleted).")";
	}
	
	// Gets comments for single journal
	function GetJournalComments($access, $from = 0, $limit = 20) {
	  	return $this->GetJournalRecords(
	  		$access, 
	  		$from,
	  		$limit, 
	  		"LENGTH(t1.".self::INDEX.") <> 4
	  		ORDER BY t1.".self::DATE." DESC"
	  	); 
	}
	
	// Gets comments from multiple journals with access logic
	function GetMixedJournalsComments($userId, $from = 0, $limit = 20) {
	  	return $this->GetMixedJournalsRecords(
	  		$userId, 
	  		$from,
	  		$limit, 
	  		"LENGTH(t1.".self::INDEX.") <> 4
	  		ORDER BY t1.".self::DATE." DESC"
	  	); 
	}

	/* Static Methods */

	public static function MakeLink($recordId, $commentId = 0, $text = "комментарий", $is_hidden = 0) {
		return "<a ".($is_hidden ? "class='Hidden' " : "")."href='/journal/comments.php?".JournalRecord::ID_PARAM."=".round($recordId).($commentId > 0 ? "#cm".$commentId : "")."'>".$text."</a>";
	}
}

?>