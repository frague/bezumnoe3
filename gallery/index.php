<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "gallery.template.php";

	$meta_description = "���� ������� ������������ ���� �������� �������� � ����������� �����. ���������� ����� ���������� ������� ����. ����� ������. ���������� ������� ��������.";

	Head("�����������", "forum.css");
	require_once $root."references.php";

?>
<style>
	h1 .char2 {
		margin-left: -1px;
	}
	h1 .char3 {
		margin-left: -1px;
	}
	h1 .char4 {
		margin-left: -1px;
	}
	h1 .char6 {
		margin-left: -2px;
	}
	h1 .char7 {
		margin-left: 1px;
	}
	h1 .char8 {
		margin-left: 1px;
	}
	h1 .char9 {
		margin-left: -1px;
	}
</style>

<table width="100%">
	<tr>
		<td width="40%">
			<h3>����� �����������</h3>
			<?php include $root."/inc/ui_parts/gallery.comments.php" ?>
			</td>
		<td valign="top">
			<h3>������� � ��������� ����������</h3>
			<?php include $root."/inc/ui_parts/galleries.php" ?>
			</td></tr></table>
<?php

	Foot();
?>