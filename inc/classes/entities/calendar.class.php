<?
class Calendar {
	var $Days = array();

	var $Month;
	var $Year;

	var $MonthDays;
	var $FirstDay;

	var $ShowMonthName = 1;
	var $ShowDayNames = 1;

	var $Months = array("","Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");
	var $DayNames = array("","Пн.","Вт.","Ср.","Чт.","Пт.","Сб.","Вс.");

	var $PrevMonth, $NextMonth;


	function Calendar($m = 0, $y = 0) {

		$this->Month = $m ? $m : date("n");
		$this->Year = $y ? $y : date("Y");

		$dat = MakeTime($this->Year, $this->Month, 1);

		$this->MonthDays = date("t", $dat);
		$this->FirstDay = date("w", $dat) - 1;
		if ($this->FirstDay < 0) {
			$this->FirstDay += 7;
		}

		for ($i = 1; $i <= $this->MonthDays; $i++) {
			$this->Days[$i] = $i;
		}
	}

	function __tostring() {
	    $result = "";
		
		$result .= "<table class='Calendar'>\n";
		if ($this->ShowMonthName) {
			$result .= "<tr class='Header'><th colspan=7>".$this->Months[round($this->Month)].", ".$this->Year."</th></tr>\n";
		}
		if ($this->ShowDayNames) {
		    $perc = sprintf("%.02f", 100 / 7);
		
			$result .= "<tr class='DayNames'>";
			for ($i = 1; $i <= 7; $i++) {
				$result.="<td width='".$perc."%'".($i==6 || $i==7 ? " class='Weekend'" : "").">".$this->DayNames[$i]."</td>";
			}
			$result .= "</tr>\n";

			$flag = 2;
			$day = 1;
			while ($flag) {
				$result .= "<tr class='Week'>\n	";
				for ($i = 0; $i < 7; $i++) {
					$result .= "<td".($i==5 || $i==6 ? " class='Weekend'" : "").">";

					if ($flag == 2 && $i == $this->FirstDay) {
						$flag--;
					}
					if ($flag == 1) {
						if ($day <= $this->MonthDays) {
							$result .= $this->Days[$day++];
						} else {
							$flag--;
						}
					}
					$result .= "</td>";
				}
				$result .= "</tr>\n";
			}
		}
		if ($this->PrevMonth || $this->NextMonth) {
			$result .= "<tr class='Navigation'><td colspan=7>".$this->PrevMonth." | ".$this->NextMonth."</td></tr>";
		}
		
		$result .= "</table>";
		return $result;
	}

	function SetHeader($hdr) {
		$this->Header = $hdr;
	}
	
	function SetDay($day, $value) {
		$this->Days[$day] = $value;
	}

	function SetPrevNext($prev, $next) {
		$this->PrevMonth = $prev;
		$this->NextMonth = $next;
	}
}

?>