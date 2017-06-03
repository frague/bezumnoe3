<?php 
class GalleryPhoto extends JournalRecord {

    var $RecordType = Forum::TYPE_GALLERY;

    function ToLink($trimBy = 0, $alias = "") {
        return self::MakeLink($this->ForumId, $this->Id);
    }

    function GetImageUrl($galleryFile = "", $isThumb = true) {
      global $SiteHost, $PathToGalleries;
        return "http://".$SiteHost.$PathToGalleries.$galleryFile.($isThumb ? "/thumbs/" : "/").$this->Content;
    }

    function ToPreview($galleryFile, $isThumb = true) {
      global $root, $PathToGalleries, $ServerPathToGalleries;

        $path = $galleryFile.($isThumb ? "/thumbs/" : "/").$this->Content;

        $result = $isThumb ? $this->ToLink() : "";
        $result .= HtmlImage($PathToGalleries.$path, $root.$ServerPathToGalleries.$path, "", $this->Title).($isThumb ? "</a>" : "");

        return $result;
    }

    function ToGalleryPreview($galleryFile, $isThumb = true) {
      global $root, $PathToGalleries, $ServerPathToGalleries;

        $path = $galleryFile.($isThumb ? "/thumbs/" : "/").$this->Content;

        $result = $isThumb ? "<a href=\"".($PathToGalleries.$galleryFile."/".$this->Content)."\" rel=\"pp[pp_gal]\" src=\"".$this->ForumId."/".$this->Id."|".($this->AnswersCount - $this->DeletedCount)."\">" : "";
        $result .= HtmlImage($PathToGalleries.$path, $root.$ServerPathToGalleries.$path, "", $this->Title).($isThumb ? "</a>" : "");

        return $result;
    }

    function ToPrint($galleryFile = "", $isThumb = true) {
      global $root, $PathToGalleries, $ServerPathToGalleries;

//      $result = $this->ToGalleryPreview($galleryFile, $isThumb);
        $result = $this->ToPreview($galleryFile, $isThumb);

        if ($this->Title) {
            $result .= "<p>".nl2br($this->Title)."</p>";
        }
        if ($this->AnswersCount > 0 && $isThumb) {
            $result .= $this->ToLink()."<p>".Countable("комментарий", $this->AnswersCount - $this->DeletedCount)."</p></a>";
        }

        return $result;
    }

    function GetHeight($path) {
      global $root, $PathToGalleries, $ServerPathToGalleries;
        if (!$this->IsEmpty()) {
            $s = @GetImageSize($root.$ServerPathToGalleries.$path."/".$this->Content);
            if ($s) {
                return $s[1];
            }
        }
        return 0;
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

    function GetParentLink($expression) {
        if (!$this->IsEmpty()) {
            $q = $this->GetByCondition($this->Index." AND ".self::FORUM_ID."=".round($this->ForumId), $expression);
            if ($q->NumRows()) {
                $q->NextResult();
                $id = $q->Get(self::RECORD_ID);
                return GalleryPhoto::MakeLink($this->ForumId, $id);
            }
        }
        return "<a class=\"NotShown\">";
    }

    function GetPreviousLink() {
        return str_replace("<a", "<a rel=\"prev\"", $this->GetParentLink($this->GetPreviousIdExpression()));
    }

    function GetNextLink() {
        return str_replace("<a", "<a rel=\"next\"", $this->GetParentLink($this->GetNextIdExpression()));
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

    function GetPreviousIdExpression() {
        return "SELECT ".self::RECORD_ID." FROM ".self::table." WHERE LENGTH(".self::INDEX.")=4 AND ".self::INDEX."< ##CONDITION## ORDER BY ".self::INDEX." DESC LIMIT 1";
    }

    function GetNextIdExpression() {
        return "SELECT ".self::RECORD_ID." FROM ".self::table." WHERE LENGTH(".self::INDEX.")=4 AND ".self::INDEX.">##CONDITION## ORDER BY ".self::INDEX." ASC LIMIT 1";
    }



    public static function MakeLink($forumId, $recordId, $commentId = "") {
        return "<a href='/gallery".$forumId."/".$recordId."/".(!$commentId || $recordId == $commentId ? "" : "#c".$commentId)."'>";
    }

}

?>
