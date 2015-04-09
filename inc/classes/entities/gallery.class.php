<?php

class Gallery extends ForumBase {

    var $SpellType = "фотогалерею";

    function IsFull() {
        return (!$this->IsEmpty() && $this->Type == self::TYPE_GALLERY);
    }

    function Clear() {
        parent::Clear();
        $this->Type = self::TYPE_GALLERY;
    }

    function ToPrint($makeLink = false, $lastVisitDate = "") {
        $result = "<h2".($this->IsProtected ? " class='Hidden'" : "").">";
        $result .= $this->Title."</h2>";

        if ($makeLink) {
            $result = "<a href='".$this->BasePath()."'>".$result."</a>";
        }

        $result .= "<div class='Counts'>В галерее ".Countable("фотография", $this->TotalCount, "нет");
        if ($lastVisitDate) {
            $unread = $this->GetUnreadCount($lastVisitDate);
            if ($unread > 0) {
                $result .= ", из них <strong>".Countable("новое", $unread)."</strong>";
            }
        }
        $result .= ".";
        if ($this->IsProtected) {
            $result .= " <span class='Red'>Это закрытая галерея.</span>";
        }

        $result .= "</div>";
        return $result;
    }

    function ToLink($lastVisitDate = "") {
        $result = "<a href='".$this->BasePath()."'".($this->IsProtected ? " class='Hidden'" : "").">";
        $result .= $this->Title."</a>";

        $result .= " <span>(".Countable("фотография", $this->TotalCount, "нет");
        if ($lastVisitDate) {
            $unread = $this->GetUnreadCount($lastVisitDate);
            if ($unread > 0) {
                $result .= ", <strong>".Countable("новый комментарий", $unread)."</strong>";
            }
        }
        $result .= ")</span>";
        if ($this->IsProtected) {
            $result .= " <span class='Red'>Это закрытая галерея.</span>";
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
