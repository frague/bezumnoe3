<?php

	$u = new User();
	$q = $u->GetByCondition("", $u->PhotosExpression());

	$n = $q->NumRows();
	$r = round(rand(0, $n - 1));
	$s = "";

	echo "<ul>";
	for ($i = 0; $i < $n; $i++) {
		$q->NextResult();
		$u->FillFromResult($q);
		$photo = $q->Get(Profile::PHOTO);
		if ($i == $r) {
			$s = "<div class=\"Centered\">".$u->ToInfoLink("<img src=\"/img/photos/small/".$photo."\"><br />".$u->Login)."</div>";
		}
		echo MakeListItem()." ".$u->ToInfoLink();
	}
	echo "</ul>";

	if ($s) {
		echo $s;
	}

?>