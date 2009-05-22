<?php
	
	require "../inc/ui_parts/templates.php";

	$postAccess = 1;
	$user = GetAuthorizedUser(true);
	if (!$user->IsEmpty()) {
		$user->User->TouchSession();
		$user->User->Save();
	}

	function IsPostingAllowed() {
		return !AddressIsBanned(new Bans(0, 1, 0));
	}

?>