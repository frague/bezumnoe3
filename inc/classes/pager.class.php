<?php

	class Pager {
		var $Title = "";
		var $ShownRange = 20;
		var $Divider = " ";
		var $Parameter = "from";

		function Pager($total, $perpage = 10, $current = 0, $base = "/") {
			$this->Total = $total;
			$this->PerPage = $perpage;
			$this->CurrentPage = $current;
			$this->ScriptName = $base;
		}

		function PrintLink($page, $prefix = "", $postfix = "", $showDivider = true) {
			$result = ($prefix ? $prefix : ($showDivider ? $this->Divider : ""));
			$css = (!$page || !$showDivider) ? " class=\"Sided\"" : "";
			if ($page == $this->CurrentPage) {
				$result .= "<strong".$css.">".($page + 1)."</strong>";
			} else {
				$result .= "<a href='".$this->ScriptName.($page ? "page".$page.".html" : "")."'".$css.">";
				$result .= ($page + 1);
				$result .= "</a>";
			}
			$result .= $postfix;
			return $result;
		}
	
		function __tostring() {
		    $result = "";
			$pagesCount = ceil($this->Total / $this->PerPage);
			$showFrom = $this->CurrentPage - round($this->ShownRange / 2);
			if ($showFrom <= 0) {
				$showFrom = 0;
			} else {
				$result .= $this->PrintLink(0, "", " .. ");
			}

			$showTill = $showFrom + $this->ShownRange;
			if ($showTill > $pagesCount) {
				$showTill = $pagesCount;
			}

			for ($i = $showFrom; $i < $showTill; $i++) {
				$result .= $this->PrintLink($i, "", "", ($i != $showFrom));
			}

			if ($showTill != $pagesCount && $pagesCount > 0) {
				$result .= $this->PrintLink($pagesCount - 1, " .. ", "", false);
			}
			
			if ($result) {
				$result = "<div class='Pager'>".($this->Title ? "<h4>".$this->Title."</h4>" : "").$result."</div>";
			}

			return $result;
		}
	}

?>