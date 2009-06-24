<?

	$root = "../";
	require_once $root."references.php";
	require_once "step_tracking.php";

	set_time_limit(120);

	$mz = new User();
	$mz->FillByCondition(User::LOGIN."='Мартовский Заяц'");

	echo "<h2>Фотогалереи:</h2>";
	
	echo "<h3>Очистка таблиц:</h3>";
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

	echo "<h3>Перенос данных:</h3>";
	$q = $db->Query("SELECT * FROM _galleries ORDER BY id ASC");
	echo "<ol>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$gallery = new Gallery();
		$id = $q->Get("id");
		$gallery->Title = $q->Get("description");
		$gallery->Description = $q->Get("cat");
		if (!$mz->IsEmpty()) {
			$gallery->LinkedId = $mz->Id;
		}
		$gallery->Save();

		echo "<li> ".$gallery->Title;
		echo "<ul class='Small'>";
		$q1 = $db->Query("SELECT * FROM _gallery WHERE gallery_id=".$id." ORDER BY id ASC");
		for ($j = 0; $j < $q1->NumRows(); $j++) {
			$index = sprintf("%04d", $j + 1);

			$q1->NextResult();
			$photo = new GalleryPhoto();
			$photo->ForumId = $gallery->Id;
			$photo->Title = $q1->Get("description");
			if (!$mz->IsEmpty()) {
				$photo->Author = $mz->Login;
				$photo->UserId = $mz->Id;
			}
			$photo->Content = $q1->Get("filename");
			$photo->Index = $index;

			// Comments
			$q2 = $db->Query("SELECT t1.*, t2.USER_ID FROM _gallery_comments t1 
			LEFT JOIN users t2 ON t2.LOGIN = t1.author
			WHERE post_id=".$q1->Get("id")." ORDER BY t1.id ASC");

			$answers = $q2->NumRows();
			$photo->AnswersCount = $answers;
			$photo->Save();

			echo "<li> ".$photo->Title;

			for ($l = 0; $l < $answers; $l++) {
				$q2->NextResult();
				$record1 = new JournalComment();
				$record1->ForumId = $gallery->Id;
				$record1->Index = $index."_".sprintf("%02d", $l+1);
				$record1->Author = $q2->Get("author");
				$record1->UserId = $q2->Get("USER_ID");
				$record1->Title = " ";
				$record1->Type = $photo->Type;
				$record1->Content = $q2->Get("comment");
				$record1->Date = DateFromTime($q2->Get("date"));
				$record1->Save();
			}
		}
		echo "</ul>";

		$gallery->TotalCount = $q1->NumRows();
		$gallery->Save();
	}
	echo "</ol>";

	Passed();
?>