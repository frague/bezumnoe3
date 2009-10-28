<?php

class Journal extends ForumBase {

	var $SpellType = "������";

	function IsFull() {
		return (!$this->IsEmpty() && $this->Type == self::TYPE_JOURNAL);
	}

	function Clear() {
		parent::Clear();
		$this->Type	= self::TYPE_JOURNAL;
	}

	function BasePath() {
		return "/journal/";
	}

	function GetLink($alias, $recordId = 0) {
		if ($this->IsEmpty() || !$alias) {
			return;
		}
		$recordId = round($recordId);
		return "<a href=\"".$this->BasePath().$alias.($recordId ? "/post".$recordId.".html" : "")."\" target=\"journal\">".$this->Title."</a>";
	}
}	

?>