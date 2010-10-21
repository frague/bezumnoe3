<?php

	$u = new User();
	$q = $u->GetByCondition(
		"t1.".User::BANNED_BY." IS NOT NULL AND t1.".User::IS_DELETED."=0  ORDER BY ".User::LOGIN,
		$u->BannedExpression()
	);

	echo "<div class='UserList'><ul>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$u->FillFromResult($q);
		$admin = $q->Get("ADMIN");
		echo MakeListItem()." ".$u->ToInfoLink()."<div>".$u->BannedInfo($admin)."</div>";
	}
	echo "</ul></div>";
	$q->Release();

?>