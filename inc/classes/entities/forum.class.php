<?

class Forum extends ForumBase {

	function Clear() {
		parent::Clear();
		$this->Type	= self::TYPE_FORUM;
	}

	function ToPrint($link = "", $lastVisitDate = "") {
		$result = "\n<h4".($this->IsProtected ? " class='Hidden'" : "").">";
		if ($link) {
			$result.= "<a href='".$this->BasePath()."'>";
		}
		$result.= $this->Title.($link ? "</a>" : "")."</h4>";
		$result.= $this->Description;
		$result.= "<div class='Counts'>� ������ ".Countable("����", $this->TotalCount, "���").".";
		if ($lastVisitDate) {
			$unread = $this->GetUnreadCount($lastVisitDate);
			if ($unread > 0) {
				$result.= " <span class='Red'>".Countable("����� ���������", $unread)."</span>.";
			}
		}
		if ($this->IsProtected) {
			$result.= " <span class='Red'>��� �������� �����.</span>";
		}
		$result.= "</div>";
		return $result;
	}

	function DoPrint($link = "", $lastVisitDate = "") {
		echo $this->ToPrint($link, $lastVisitDate);
	}
}

?>