<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	$user = GetAuthorizedUser(true);

	Head("������� ����");
	require_once $root."references.php";

?>
<h2>������� ����</h2>

<?php

	Foot();
?>