<?php

class Gallery extends ForumBase {

	var $SpellType = "фотогалерею";

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
			echo "<a href='".$this->BasePath()."'>";
		}
		echo $this->Title.($link ? "</a>" : "")."</h4>";
		echo "<div class='Counts'>В галерее ".Countable("фотография", $this->TotalCount, "нет");
		if ($lastVisitDate) {
			$unread = $this->GetUnreadCount($lastVisitDate);
			if ($unread > 0) {
				echo ", из них <span class='Red'>".Countable("новое", $unread)."</span>";
			}
		}
		echo ".";
		if ($this->IsProtected) {
			echo " <span class='Red'>Это закрытая галерея.</span>";
		}

		echo "</div>";
	}

	function BasePath() {
		return "/gallery".$this->Id."/";
	}

	function GetLink($alias = "", $recordId = 0) {
		$recordId = round($recordId);
		return $this->IsEmpty() ? "" : "<a href=\"".$this->BasePath().($recordId ? $recordId : "")."\" target=\"gallery\">".$this->Title."</a>";
	}
}	
?>