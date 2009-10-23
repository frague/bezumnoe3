<?

class GalleryPhoto extends ForumRecordBase {

    function MakeLink() {
    	return "<a href='/gallery/comments.php?".self::ID_PARAM."=".$this->Id."'>";
    }
	
	function ToPrint($galleryFile, $isThumb = 1) {
		$path = $galleryFile.($isThumb ? "/thumbs/" : "/").$this->Content;

		$result = $isThumb ? $this->MakeLink() : "";
		$result .= HtmlImage($PathToGallery.$path, $ServerPathToGallery.$path).($isThumb ? "</a>" : "");
		if ($this->Title) {
			$result .= "<p>".$this->Title."</p>";
		}
		if ($this->AnswersCount > 0 && $isThumb) {
			$result .= $this->MakeLink()."<p>".Countable("комментарий", $this->AnswersCount)."</p></a>";
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
}

?>