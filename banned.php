<?php
	
	$root = "./";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	Head("&laquo;׸���� ������&raquo;", "banned.css");
	require_once $root."references.php";

?>

�� ���� �������� ������� ��� ���������� ����, ���������� ��������� � ���� ����.

<h4>����������:</h4>
<?php include $root."inc/ui_parts/banned.php"; ?>

<br /><br /><br /><br /><br />

<?php


	Foot();
?>