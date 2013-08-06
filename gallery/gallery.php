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
	
	if ($access == Gallery::NO_ACCESS || $gallery->IsHidden) {
		ErrorPage("У вас нет доступа к данной фотогалерее.", "Права доступа к галерее ограничены владельцем.");
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
			$result.= "<td".($c < $row ? " width='".sprintf("%01.2f", 100 / $row)."%'" : "").">";
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
	$p = new Page($gallery->Title, $meta_description, "Фотогалерея");
	$p->AddCss(array("forum.css", "prettyPhoto.css"));
	$p->AddJs("gallery.js");
	$p->PrintHeader();

	$gallery->DoPrint();
	echo $result;
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

<?php

	$p->PrintFooter();
?>
