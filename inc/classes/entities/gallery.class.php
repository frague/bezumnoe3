<?php

class Gallery extends ForumBase {

	var $SpellType = "�����������";

	function IsFull() {
		return (!$this->IsEmpty() && $this->Type == self::TYPE_GALLERY);
	}

	function Clear() {
		parent::Clear();
		$this->Type	= self::TYPE_GALLERY;
	}

	function ToPrint($makeLink = false, $lastVisitDate = "") {
		$result = "";
		$result .= "\n<h4".($this->IsProtected ? " class='Hidden'" : "").">";
		if ($makeLink) {
			$result .= "<a href='".$this->BasePath()."'>";
		}
		$result .= $this->Title.($makeLink ? "</a>" : "")."</h4>";
		$result .= "<div class='Counts'>� ������� ".Countable("����������", $this->TotalCount, "���");
		if ($lastVisitDate) {
			$unread = $this->GetUnreadCount($lastVisitDate);
			if ($unread > 0) {
				$result .= ", �� ��� <span class='Red'>".Countable("�����", $unread)."</span>";
			}
		}
		$result .= ".";
		if ($this->IsProtected) {
			$result .= " <span class='Red'>��� �������� �������.</span>";
		}

		$result .= "</div>";
		return $result;
	}
	
	function ToLink($lastVisitDate = "") {
		$result = "&laquo;<a href='".$this->BasePath()."'".($this->IsProtected ? " class='Hidden'" : "").">";
		$result .= $this->Title."</a>&raquo;";

		$result .= " (".Countable("����������", $this->TotalCount, "���");
		if ($lastVisitDate) {
			$unread = $this->GetUnreadCount($lastVisitDate);
			if ($unread > 0) {
				$result .= ", <span class='Red'>".Countable("����� �����������", $unread)."</span>";
			}
		}
		$result .= ")";
		if ($this->IsProtected) {
			$result .= " <span class='Red'>��� �������� �������.</span>";
		}
		return $result;
	}
	
	function DoPrint($makeLink = false, $lastVisitDate = "") {
		echo $this->ToPrint($makeLink, $lastVisitDate);
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