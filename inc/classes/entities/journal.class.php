<?php

class Journal extends ForumBase {

	var $SpellType = "журнал";

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

	function GetLink($alias = "", $recordId = 0, $setTarget = true) {
		if ($this->IsEmpty() || !$alias) {
			return;
		}
		$recordId = round($recordId);
		return "<a href=\"".$this->BasePath().$alias.($recordId ? "/post".$recordId."/" : "")."\"".($setTarget ? " target=\"journal\"" : "").">".$this->Title."</a>";
	}
}	

?>