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
	
	function GetJournalComments($user, $from = 0, $limit = 20) {
	  	return $this->GetJournalRecords(
	  		$user, 
	  		$from,
	  		$limit, 
	  		"LENGTH(t1.".self::INDEX.") <> 4
	  		ORDER BY t1.".self::DATE." DESC"
	  	); 
	}
	

	/* Static Methods */

	public static function MakeLink($recordId, $commentId = 0, $text = "комментарий", $is_hidden = 0) {
		return "<a ".($is_hidden ? "class='Hidden' " : "")."href='/3/journal/comments.php?".JournalRecord::ID_PARAM."=".round($recordId).($commentId > 0 ? "#cm".$commentId : "")."'>".$text."</a>";
	}
}

?>