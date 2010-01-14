<?

class GalleryPhoto extends JournalRecord {

	var $RecordType = Forum::TYPE_GALLERY;

    function ToLink() {
    	return self::MakeLink($this->ForumId, $this->Id);
    }
	
	function ToPreview($galleryFile, $isThumb = true) {
	  global $root, $PathToGalleries, $ServerPathToGalleries;

		$path = $galleryFile.($isThumb ? "/thumbs/" : "/").$this->Content;

		$result = $isThumb ? $this->ToLink() : "";
		$result .= HtmlImage($PathToGalleries.$path, $root.$ServerPathToGalleries.$path, "", $this->Title).($isThumb ? "</a>" : "");

		return $result;
	}

	function ToPrint($galleryFile, $isThumb = true) {
	  global $root, $PathToGalleries, $ServerPathToGalleries;

		$result = $this->ToPreview($galleryFile, $isThumb);

		if ($this->Title) {
			$result .= "<p>".nl2br($this->Title)."</p>";
		}
		if ($this->AnswersCount > 0 && $isThumb) {
			$result .= $this->ToLink()."<p>".Countable("комментарий", $this->AnswersCount - $this->DeletedCount)."</p></a>";
		}

		return $result;
	}

	function ToJs($mark = "") {
		$title = strip_tags($this->Title);
		$content = substr(strip_tags($this->Content), 0, 100);

		return "new grdto(\"".
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

	function GetGalleryPhotos($access, $from = 0, $limit, $condition) {
		$from = round($from);
		$limit = round($limit);

	  	return $this->GetByCondition(
	  		$condition." LIMIT ".($from ? $from."," : "").$limit,
	  		$this->GalleryPhotosExpression($access)
	  	); 
	}
	
	function GalleryPhotosExpression($access) {
		return str_replace(
		"WHERE",
		"LEFT JOIN ".Gallery::table." AS t5 ON t5.".Gallery::FORUM_ID."=t1.".self::FORUM_ID."
WHERE 
	t5.".Journal::TYPE."='".Journal::TYPE_GALLERY."' AND ",
		$this->ReadThreadExpression($access));
	}

    public static function MakeLink($forumId, $recordId, $commentId = "") {
    	return "<a href='/gallery".$forumId."/".$recordId."/".(!$commentId || $recordId == $commentId ? "" : "#c".$commentId)."'>";
    }
	
}

?>