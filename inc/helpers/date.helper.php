<?
	// Time ranges
	$RangeMinute = 60;
	$RangeHour = 60 * $RangeMinute;
	$RangeDay = 24 * $RangeHour;
	$RangeMonth = 30 * $RangeDay;
	$RangeYear = 365 * $RangeDay;

	$NowDate = "";
	$NowDateTime = "";

	function DateFromTime($t, $format = "Y-m-d H:i:s") {
		if (!$t) {
		     $t = 0;
		}
		return date($format, $t);
	}

	function NowDateTime() {
	  global $NowDateTime;

		if (!$NowDateTime) {
			$NowDateTime = DateFromTime(time());
		}
		return $NowDateTime;
	}

	function NowDate() {
	  global $NowDate;

		if (!$NowDate) {
			$NowDate = DateFromTime(time(), "Y-m-d");
		}
		return $NowDate;
	}

	function ParseDate($s) {
		return strtotime($s);
	}

	function MakeTime($year, $month, $day) {
	  global $RangeDay ;

		if ($month > 12 || $month < 1) {
	   		$year += round($month / 12);
	   		$month %= 12;
	   		if ($month < 1) {
	   			$month += 12;
	   			$year--;
	   		}
	   	}
	   	$tempYear = $year + ($month == 12 ? 1 : 0);
	   	$tempMonth = ($month == 12 ? 1 : $month + 1);
	   	$days = date("d", mktime(0,0,0, $tempMonth, 1, $tempYear) - 1);

//	   	echo " *$days ".DateFromTime(mktime(0,0,0, $tempMonth, 1, $tempYear))."*";

	   	if ($day < 0) {
	    	$year = $year - ($month == 1 ? 1 : 0);
	   		$month = $month == 1 ? 12 : $month - 1;
	   		$days = date("d", mktime(0,0,0, 1, $month, $year) - 1);
	   		$day = $day + days;
	   	}
	   	if ($day > $days) {
	   		$day = $days - $day;
	   		$month++;
	   		if ($month == 13) {
	   			$month = 1;
   				$year++;
	   		}
	   	}
	   	return strtotime(sprintf("%04d-%02d-%02d 00:00:00", $year, $month, $day));
	}

	function PrintableDate($d, $printTime = 1) {
	  global $MonthsNamesForDate;

		$t = strtotime($d);
		$pattern = "Y Рі.".($printTime ? " РІ H:i" : "");
		return date("j", $t)." ".$MonthsNamesForDate[date("n", $t)]." ".date($pattern, $t);
	}

	function PrintableShortDate($d) {
	  global $MonthsNamesForDate;

		$t = strtotime($d);
		return date("j.m.Y, H:i", $t);
	}

	function PrintableDay($d) {
	  global $MonthsNamesForDate;

		$t = strtotime($d);
		return date("j.m.Y", $t);
	}

	function PrintableTime($d) {
		$t = strtotime($d);
		return date("H:i", $t);
	}

	function BirthdayDate($d) {
	  global $MonthsNamesForDate, $MonthsNames;

		$parts = split("-", $d);
		if (sizeof($parts) != 3) {
			return $d;
		}
		$result = round($parts[2]) ? round($parts[2])." " : "";
		$result .= $parts[1] ? ($result ? $MonthsNamesForDate[round($parts[1])]." ": $MonthsNames[round($parts[1])]." ") : "";
		$result .= round($parts[0]) ? round($parts[0])." Рі." : "";
		return trim($result);
	}

	function DatesDiff($d) {
		$current_time = mktime(); 
		$target_time = strtotime(date($d)); 
		$timediff = round(($current_time - $target_time) / (60 * 60 * 24));

		return $timediff; // Diff in days
	}
?>