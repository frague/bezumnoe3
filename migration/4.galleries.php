<?

	$root = "../";
	require_once $root."references.php";
	require_once "step_tracking.php";

	set_time_limit(120);



	echo "<h2>Migration:</h2>";
	
	echo "<h3>Removing existing galleries:</h3>";
	$gallery = new Gallery();
	$q = $gallery->GetByCondition("1=1");
	echo "<ol>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$gallery->FillFromResult($q);
		echo "<li> ".$gallery->Title;
		$gallery->Delete();
	}
	echo "</ol>";

	echo "<h3>Migration:</h3>";
	$q = $db->Query("SELECT * FROM _galleries ORDER BY id ASC");
	echo "<ol>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$gallery = new Gallery();
		$id = $q->Get("id");
		$gallery->Title = $q->Get("description");
		$gallery->Description = $q->Get("cat");

		echo "<li> ".$gallery->Title;
		echo "<ul class='Small'>";
		$q1 = $db->Query("SELECT * FROM _gallery WHERE gallery_id=".$id);
		for ($j = 0; $j < $q1->NumRows(); $j++) {
			$q1->NextResult();
			$photo = new GalleryPhoto();
			$photo->ForumId = $gallery->Id;
			$photo->Title = $q1->Get("description");
			$photo->Content = $q1->Get("filename");
			$photo->Index = sprintf("%04d", $j + 1);
			$photo->Save();
			echo "<li> ".$photo->Title;
		}
		echo "</ul>";

		$gallery->TotalCount = $q1->NumRows();
		$gallery->Save();
	}
	echo "</ol>";
	Passed();
?>