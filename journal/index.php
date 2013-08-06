<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "journal.template.php";

	$meta_description = "Блог-сервис саратовского чата Безумное Чаепитие у Мартовского Зайца. Персональные журналы, интересные события из жизни Саратова и его обитателей. Блоги Саратова. Саратовский блог-сервис.";

	$p = new Page("Журналы", $meta_description);
	$p->AddCss("forum.css");
	$p->SetRss("/journal/rss/");
	$p->PrintHeader();

	require_once $root."references.php";

?>

<style>
	h1 .char2 {
		margin-left: -2px;
	}
	h1 .char3 {
		margin-left: -2px;
	}
	h1 .char4 {
		margin-left: -1px;
	}
	h1 .char5 {
		margin-left: -1px;
	}
	h1 .char6 {
		margin-left: 1px;
	}
</style>

<table width="100%">
	<tr>
		<td width="40%" valign="top">
			<h3>Новые сообщения</h3>
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

	$p->PrintFooter();
?>