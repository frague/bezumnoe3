<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "gallery.template.php";

	Head("Фотогалерея", "forum.css", "forum.js");
	require_once $root."references.php";


	$gallery = new Gallery();
	if ($someoneIsLogged) {
		$q = $gallery->GetByConditionWithUserAccess("1=1", $user->User->Id);
	} else {
		$q = $gallery->GetByCondition("1=1");
	}

	echo "<ul class='Forums'>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$gallery->FillFromResult($q);

		echo "<li>";
		$gallery->DoPrint("/gallery", $yesterday);
	}
	echo "</ul>";
	

?>



<?php


	Foot();
?>