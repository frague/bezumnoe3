<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "gallery.template.php";

	$meta_description = "���� ������� ������������ ���� �������� �������� � ����������� �����. ���������� ����� ���������� ������� ����. ����� ������. ���������� ������� ��������.";

	Head("�����������", "forum.css", "", "", "");
	require_once $root."references.php";

?>
<table width="100%">
	<tr>
		<td width="40%">
			<h4>����� �����������:</h4>
			<?php include $root."/inc/ui_parts/gallery.comments.php" ?>
			</td>
		<td valign="top">
			<h4>�������:</h4>
			<?php include $root."/inc/ui_parts/galleries.php" ?>
			</td></tr></table>
<?php

	Foot();
?>