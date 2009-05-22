<?php

class Gallery extends ForumBase {

	function IsFull() {
		return (!$this->IsEmpty() && $this->Type == self::TYPE_GALLERY);
	}

	function Clear() {
		parent::Clear();
		$this->Type	= self::TYPE_GALLERY;
	}

	function DoPrint($link = "", $lastVisitDate = "") {
		echo "\n<h4".($this->IsProtected ? " class='Hidden'" : "").">";
		if ($link) {
			echo "<a href='".$link."?".self::ID_PARAM."=".$this->Id."'>";
		}
		echo $this->Title.($link ? "</a>" : "")."</h4>";
		echo "<div class='Counts'>� ������� ".Countable("����������", $this->TotalCount, "���");
		if ($lastVisitDate) {
			$unread = $this->GetUnreadCount($lastVisitDate);
			if ($unread > 0) {
				echo ", �� ��� <span class='Red'>".Countable("�����", $unread)."</span>";
			}
		}
		echo ".";
		if ($this->IsProtected) {
			echo " <span class='Red'>��� �������� �������.</span>";
		}

		echo "</div>";
	}
}	

?>