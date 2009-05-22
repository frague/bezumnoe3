<?

class GalleryComment extends ForumRecordBase {

	function ToLink($trimBy = 0, $recordId) {
		$content = $trimBy ? TrimBy($this->Content, $trimBy) : $this->Content;
		return self::MakeLink($recordId, $this->Id, $content, $this->Type == self::TYPE_PROTECTED);
	}

	function ToJs() {
		$title = strip_tags($this->Title);
		$content = substr(strip_tags($this->Content), 0, 2048);

		return "new gcdto(\"".JsQuote($this->Id).
"\",\"".JsQuote($this->UserId).
"\",\"".JsQuote($this->Author).
"\",\"".JsQuote($title).
"\",\"".JsQuote($content).
"\",\"".JsQuote($this->Date).
"\",".round($this->Type).",".round($this->IsDeleted).")";
	}
	

}

?>