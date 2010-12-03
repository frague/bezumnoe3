<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "journal.template.php";

	$meta_description = "Блог-сервис саратовского чата Безумное Чаепитие у Мартовского Зайца. Персональные журналы, интересные события из жизни Саратова и его обитателей. Блоги Саратова. Саратовский блог-сервис.";

	Head("Журналы", "forum.css", "forum.js", "/journal/rss/", "", "journals.gif");
	require_once $root."references.php";

?>

<table width="100%">
	<tr>
		<td width="40%" valign="top">
			<h4>Новые сообщения:</h4>
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