<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require_once "gallery.template.php";

	$cols = 3;
	$row = 3;
	$perPage = $cols * $row;
	$show_from = round(LookInRequest("from"));
	$galleryId = round(LookInRequest(Gallery::ID_PARAM));
	$from = round($_GET["from"]);

	if (!$galleryId) {
		DieWith404();
	}

	$gallery = new Gallery();
	$gallery->GetById($galleryId);


	$access = 1 - $gallery->IsProtected;
	if ($someoneIsLogged) {
		$access = $gallery->GetAccess($user->User->Id);
	}
	
	if ($access == Gallery::NO_ACCESS) {
		error("У вас нет доступа к форуму.");
		Foot();
		die;
	}

	Head($gallery->Title, "gallery.css", "");

	echo $gallery->DoPrint();

	$photo = new GalleryPhoto();
	$q = $photo->GetForumThreads($gallery->Id, $user, $from * $perPage, $perPage);

	$c = 0;
	$amount = $q->NumRows();

	echo "<table class='Preview'>";
	while ($c < $amount) {
		echo "<tr>";
		for ($i = 0; $i < $row; $i++) {
			echo "<td".($c < $row ? " width='".(100 / $row)."%'" : "").">";
			if (($c < $amount)) {
				$q->NextResult();
				$photo->FillFromResult($q);
				echo $photo->ToPrint($PathToGalleries.$gallery->Description);
				$c++;
			}
			echo "</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
	
	$threads = $photo->GetForumThreadsCount($gallery->Id, $access);
	$pager = new Pager($threads, $perPage, $from);
	echo $pager;

	Foot();
?>
