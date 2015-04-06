<?

class GalleryComment extends JournalComment {

    var $RecordType = Forum::TYPE_GALLERY;

    function ToLink($trimBy = 0, $recordId = 0) {
        $content = $trimBy ? TrimBy($this->Content, $trimBy) : $this->Content;
        return GalleryPhoto::MakeLink($this->ForumId, $recordId ? $recordId : $this->Id, $this->Id).$content."</a>";
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