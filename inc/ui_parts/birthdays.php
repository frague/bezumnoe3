<?php

    $t = time();
    $today = DateFromTime($t, "%-m-d");
    $tomorrow = DateFromTime($t + $RangeDay, "%-m-d");
    $afterTomorrow = DateFromTime($t + 2 * $RangeDay, "%-m-d");

	$u = new User();
/*	$q = $u->GetByCondition(
		"t2.".Profile::BIRTHDAY." LIKE '".$today."' OR
		t2.".Profile::BIRTHDAY." LIKE '".$tomorrow."' OR
		t2.".Profile::BIRTHDAY." LIKE '".$afterTomorrow."'", 
		$u->BirthdaysExpression());*/
	$q = $u->GetByCondition(
		"t2.".Profile::BIRTHDAY." LIKE '".$today."' OR
		t2.".Profile::BIRTHDAY." LIKE '".$tomorrow."'",
		$u->BirthdaysExpression());

	$days = array("�������", "������", "�����������");
	$index = 0;
	$lastDay = "     ";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$u->FillFromResult($q);
		$hbd = $q->Get(Profile::BIRTHDAY);
		$day = substr($hbd, -2);
		if ($day != $lastDay) {
			echo ($index ? "" : "")."<h4>".$days[$index++]."</h4>";
			$lastDay = $day;
		} else {
			echo ", ";
		}
		echo $u->ToInfoLink();
	}

?>