<?php

	$yesterday = DateFromTime(time() - 60*60*24);	// Yesterday

	$gallery = new Gallery();
	if ($someoneIsLogged) {
		$q = $gallery->GetByConditionWithUserAccess("1=1", $user->User->Id);
	} else {
		$q = $gallery->GetByCondition("1=1");
	}

	echo "<ul>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$gallery->FillFromResult($q);

		if (!$gallery->IsHidden) {
			echo "<li>".$gallery->ToLink($yesterday);
		}
		
	}
	echo "</ul>";
	$q->Release();

?>