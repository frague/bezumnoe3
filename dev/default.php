<?php

	$root = "../";
	require_once $root."server_references.php";

	$user = GetAuthorizedUser(true);
	SetUserSessionCookie($user->User);

	require $root."inc/ui_parts/templates.php";
	require $root."inc/ui_parts/news.php";

	Head("Dev.blog");
	require_once $root."references.php";

?>



<?php
	Foot();
?>