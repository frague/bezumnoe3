<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	Head("Фамильное древо", "tree.css", "", "", "", "tree.gif");
	require_once $root."references.php";

	$t = new TreeNode();
	$result = "";
	
	$q = $t->GetTreeUsers();
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$result .= ($i > 0 ? "," : "").$t->UserInfoToJs($q);
	}
	$q->Release();

	echo "<script>u=[".$result."];";
	$result = "";

	$q = $t->GetByCondition("1=1");
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$t->FillFromResult($q);
		$result .= ($i > 0 ? "," : "").$t->ToJs();
	}
	$q->Release();

	echo "r=[".$result."];</script>";

?>

<div id="bb"></div>

<script language="javascript" src="/js1/tree.js"></script>
<script>DrawTree()</script>
