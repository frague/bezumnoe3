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
	$lastModified = "";

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
		Head("Ошибка", "gallery.css", "");
		error("У вас нет доступа к галерее.");
		Foot();
		die;
	}

	$meta_description = "Фотогалерея '".MetaContent($gallery->Title)."'";

	$photo = new GalleryPhoto();
	$q = $photo->GetForumThreads($gallery->Id, $user, $from * $perPage, $perPage);

	$c = 0;
	$amount = $q->NumRows();

	$result.= "<table class='Preview'>";
	while ($c < $amount) {
		$result.= "<tr>";
		for ($i = 0; $i < $row; $i++) {
			$result.= "<td".($c < $row ? " width='".(100 / $row)."%'" : "").">";
			if (($c < $amount)) {
				$q->NextResult();
				$photo->FillFromResult($q);
				$result.= $photo->ToPrint($gallery->Description);
				$c++;

				if ($photo->UpdateDate > $lastModified) {
					$lastModified = $photo->UpdateDate;
				}
			}
			$result.= "</td>";
		}
		$result.= "</tr>";
	}
	$result.= "</table>";
	$q->Release();
	
	$threads = $photo->GetForumThreadsCount($gallery->Id, $access);
	$pager = new Pager($threads, $perPage, $from, $gallery->BasePath());
	$result.= $pager;

	// Printing
	AddEtagHeader(strtotime($lastModified));
	Head($gallery->Title, "forum.css", "");

	$gallery->DoPrint();

	echo $result;
	Foot();
?>
