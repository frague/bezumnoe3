<?php

class Journal extends ForumBase {

	function IsFull() {
		return (!$this->IsEmpty() && $this->Type == self::TYPE_JOURNAL);
	}

	function Clear() {
		parent::Clear();
		$this->Type	= self::TYPE_JOURNAL;
	}
}	

?>