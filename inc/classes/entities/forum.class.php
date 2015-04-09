<?

class Forum extends ForumBase {

    function Clear() {
        parent::Clear();
        $this->Type = self::TYPE_FORUM;
    }

    function ToPrint($link = "", $lastVisitDate = "") {
        $result = "\n<h2".($this->IsProtected ? " class='Hidden'" : "").">";
        $result.= $this->Title;
        $result.= " <span>(".$this->TotalCount.")</span>";
        $result.= "</h2>";

        if ($link) {
            $result = "<a href='".$this->BasePath()."'>".$result."</a>";
        }

        $result.= $this->Description;
        $result.= "<div>";
        
        #$result.= "� ������ ".Countable("����", $this->TotalCount, "���").". ";
        
        if ($lastVisitDate) {
            $unread = $this->GetUnreadCount($lastVisitDate);
            if ($unread > 0) {
                $result.= " <span class='Red'>".Countable("����� ���������", $unread)."</span>.";
            }
        }
        if ($this->IsProtected) {
            $result.= " <span class='Red'>��� �������� �����.</span>";
        }
        $result .= "</div>";
        return $result;
    }

    function DoPrint($link = "", $lastVisitDate = "") {
        echo $this->ToPrint($link, $lastVisitDate);
    }
}

?>