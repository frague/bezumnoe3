<?php

	require_once "base.service.php";

	$t = new TreeNode();
	$result = "";
	
	$q = $t->GetTreeUsers();
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$result .= ($i > 0 ? "," : "").$t->UserInfoToJs($q);
	}
	$q->Release();

	echo "u=[".$result."];";
	$result = "";

	$q = $t->GetByCondition("1=1");
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$t->FillFromResult($q);
		$result .= ($i > 0 ? "," : "").$t->ToJs();
	}
	$q->Release();

	echo "r=[".$result."];";
?>