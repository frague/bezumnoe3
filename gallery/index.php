<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "gallery.template.php";

	$meta_description = "Фото галерея саратовского чата Безумное Чаепитие у Мартовского Зайца. Фотографии самых интересных событий чата. Фотки чатлан. Интересные события Саратова.";

	Head("Фотогалерея", "forum.css", "", "", "");
	require_once $root."references.php";

?>
<table width="100%">
	<tr>
		<td width="40%">
			<h4>Новые комментарии:</h4>
			<?php include $root."/inc/ui_parts/gallery.comments.php" ?>
			</td>
		<td valign="top">
			<h4>Галереи:</h4>
			<?php include $root."/inc/ui_parts/galleries.php" ?>
			</td></tr></table>
<?php

	Foot();
?>