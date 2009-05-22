<?php

class Journal extends ForumBase {

	function IsFull() {
		return (!$this->IsEmpty() && $this->Type == self::TYPE_JOURNAL);
	}

	function Clear() {
		parent::Clear();
		$this->Type	= self::TYPE_JOURNAL;
	}

	function GetByUserId($user_id) {
		return $this->FillByCondition("t1.".self::LINKED_ID."=".round($user_id)." AND t1.".self::TYPE."='".self::TYPE_JOURNAL."'");
	}
}	

?>