<?

	function DateFromTime($t, $format = "Y-m-d H:i:s") {
		if (!$t) {
		     $t = 0;
		}
		return date($format, $t);
	}

	function NowDateTime() {
		return DateFromTime(time());
	}

	function ParseDate($s) {
		return strtotime($s);
	}

	function MakeTime($year, $month, $day) {
	  global $daySeconds;

		if ($month > 12 || $month < 1) {
	   		$year += round($month / 12);
	   		$month %= 12;
	   		if ($month < 1) {
	   			$month += 12;
	   			$year--;
	   		}
	   	}
	   	$tempYear = $year + ($month == 12 ? 1 : 0);
	   	$tempMonth = $month == 12 ? 1 : $month + 1;
	   	$days = date("d", mktime($tempYear, $tempMonth, 1, 0,0,0) - $daySeconds);
	   	if ($day < 0) {
	    	$year = $year - ($month == 1 ? 1 : 0);
	   		$month = $month == 1 ? 12 : $month - 1;
	   		$days = date("d", mktime($tempYear, $tempMonth, 1, 0,0,0) - $daySeconds);
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

	function PrintableDate($d) {
	  global $MonthsNamesForDate;

		$t = strtotime($d);
		return date("j", $t)." ".$MonthsNamesForDate[date("n", $t)]." ".date("Y ã. â H:i", $t);
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
		$result .= round($parts[0]) ? round($parts[0])." ã." : "";
		return trim($result);
	}

?>