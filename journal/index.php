<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "journal.template.php";

	$meta_description = "����-������ ������������ ���� �������� �������� � ����������� �����. ������������ �������, ���������� ������� �� ����� �������� � ��� ����������. ����� ��������. ����������� ����-������.";

	Head("�������", "forum.css", "forum.js", "/journal/rss/", "", "journals.gif");
	require_once $root."references.php";

?>

<table width="100%">
	<tr>
		<td width="40%" valign="top">
			<h4>����� ���������:</h4>
			<?php include $root."inc/ui_parts/journal.posts.php"; ?>
			<?php include $root."inc/ui_parts/updated.journals.php"; ?>

			<?php include $root."inc/ui_parts/popular.journals.php"; ?>
		</td>
		<td valign="top">
			<?php include $root."inc/ui_parts/journal.comments.php"; ?>
		</td>
	</tr>
</table>

<?php


	Foot();
?>