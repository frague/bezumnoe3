<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	$user = GetAuthorizedUser(true);

	Head("Правила чата");
	require_once $root."references.php";

?>
<h2>Правила чата</h2>

<?php

	Foot();
?>