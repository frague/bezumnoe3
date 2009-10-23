<?

class ForumRecord extends ForumRecordBase {

	/* Printing methods */

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::RECORD_ID." = ".$this->Id."</li>\n";
		$s.= "<li>".self::FORUM_ID." = ".$this->ForumId."</li>\n";
		$s.= "<li>".self::INDEX." = ".$this->Index."</li>\n";
		$s.= "<li>".self::TYPE." = ".$this->Type."</li>\n";
		$s.= "<li>".self::AUTHOR." = ".$this->Author."</li>\n";
		$s.= "<li>".self::USER_ID." = ".$this->UserId."</li>\n";
		$s.= "<li>".self::TITLE." = ".$this->Title."</li>\n";
		$s.= "<li>".self::CONTENT." = ".$this->Content."</li>\n";
		$s.= "<li>".self::DATE." = ".$this->Date."</li>\n";
		$s.= "<li>".self::ADDRESS." = ".$this->Address."</li>\n";
		$s.= "<li>".self::CLICKS." = ".$this->Clicks."</li>\n";
		$s.= "<li>".self::GUID." = ".$this->Guid."</li>\n";
		$s.= "<li>".self::IS_COMMENTABLE." = ".$this->IsCommentable."</li>\n";
		$s.= "<li>".self::IS_DELETED." = ".$this->IsDeleted."</li>\n";
		$s.= "<li>".self::UPDATE_DATE." = ".$this->UpdateDate."</li>\n";
		$s.= "<li>".self::ANSWERS_COUNT." = ".$this->AnswersCount."</li>\n";
		$s.= "<li>".self::DELETED_COUNT." = ".$this->DeletedCount."</li>\n";

		if ($this->IsEmpty()) {
			$s.= "<li> <b>Forum Record is not saved!</b>";
		}
		$s.= "</ul>";
		return $s;
	}

	function ToPrint($link = "", $prevLevel = 0, $lastVisit = "") {
		$result = "";
		if ($prevLevel != $this->Level) {
			$less = ($prevLevel < $this->Level);
			for ($i = 0; $i < abs($this->Level - $prevLevel); $i++) {
				$result .= $less ? "<ul>" : "</ul>";
			}
		}

		/* Protected & Hidden messages */
		$cssClass = $this->IsDeleted ? "Hidden " : "";
		$cssClass .= $this->IsProtected() ? "Protected" : "";
		$cssClass .= $this->UpdateDate > $lastVisit ? " Recent" : "";

		$result .= "\n<li class='".$cssClass."'>";
		$result .= " &laquo;".($link ? "<a href='".$link."?".self::ID_PARAM."=".$this->Id."'>" : "");
		$result .= $this->Title;
		$result .= ($link ? "</a>" : "");
		$result .= "&raquo;, ".$this->Author;
		$result .= ", ".PrintableShortDate($this->Date).".";
		if ($this->AnswersCount > $this->DeletedCount && $this->IsCommentable) {
			$result .= " <span class='Counts'>".Countable("ответ", $this->AnswersCount - $this->DeletedCount);
			if ($this->AnswersCount) {
				$result .= ", последний от ".PrintableShortDate($this->UpdateDate);
			}
			$result .= "</span>";
		}

		return $result;
	}

	function ToJs($mark = "") {
		$title = strip_tags($this->Title);
		$content = substr(strip_tags($this->Content), 0, 100);
		return "new jrdto(\"".
JsQuote($this->Id)."\",\"".
JsQuote($title)."\",\"".
JsQuote($content)."\",\"".
JsQuote($this->Date)."\",".
$this->Comments.",".
round($this->Type).")";
	}
}

?>