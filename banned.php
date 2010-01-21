<?php
	
	$root = "./";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	Head("&laquo;Чёрный список&raquo;", "banned.css");
	require_once $root."references.php";

?>

На этой странице собраны все нарушители чата, отбывающие наказание в виде бана.

<h4>Нарушители:</h4>
<?php include $root."inc/ui_parts/banned.php"; ?>

<br /><br /><br /><br /><br />

<?php


	Foot();
?>