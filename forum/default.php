<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "forum.template.php";

	Head("Форумы", "forum.css", "forum.js");
	require_once $root."references.php";

	$yesterday = DateFromTime(time() - 60*60*24);	// Yesterday

	$forum = new Forum();
	$q = $forum->GetByCondition("1=1");
	echo "<ul class='Forums'>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$forum->FillFromResult($q);

		echo "<li>";
		$forum->DoPrint("forum.php", $yesterday);
	}
	echo "</ul>";

	Foot();
?>