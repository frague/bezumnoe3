<?

	$root = "../";
	require_once $root."references.php";

	set_time_limit(600);

	echo "<h2>Attempting to modify forum records organization:</h2>";
	echo "<h4>New table fields thread_order and depth going to be filled.</h4>";
	
	$record = new ForumRecord();
	$r = new ForumRecord();

	$f = new ForumBase();
	$q = $f->GetAll();


    	$last_thread = 123557;
    	$threads = 5000;
    	$start = 0;

    
    
    echo "<ol>";
	for ($i=0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$f->FillFromResult($q);
    	echo "	<li> ".$f->Title;

    	print "<ul>";

		$q2 = $record->GetForumThreads($f->Id, 4, 0, 500000);
		for ($j=0; $j < $q2->NumRows(); $j++) {
			$q2->NextResult();
			$record->FillFromResult($q2);

			$index = $record->GetTopicIndex();
			$order = 0;

			if (!$start) {
				if ($record->Id == $last_thread || !$last_thread) {
					$start = 1;
				} else {
					continue;
			   	}
			}

			if ($start && !$threads--) {
				print "	<li> ".$record->Id;
				exit;
			}
#			echo "<li> ".$record->Title;


			$d = -1;
			$v1 = -1;
			$v2 = -1;

			$q3 = $record->GetByIndex($f->Id, 4, $index, 0, 10000);
			for ($k=0; $k < $q3->NumRows(); $k++) {
				$q3->NextResult();
				$r->FillFromResult($q3);

				$r->ThreadId = $record->Id;
				$r->ThreadOrder = $order++;
				$r->Depth = substr_count($r->Index, "_");

				if ($d >= 0) {
					if ($d <= $r->Depth) {
						$d = -1;
						$v1 = -1;
						$v2 = -1;
					} else if ($v1 > 0) {
						if ($v1 != $r->UserId) {
							$v2 = $r->UserId;
							$r->VisibleTo = $v1;
						} else if ($v2) {
							$r->VisibleTo = $v2;
						}
					}
				} else if ($r->Type > 0) {
					$v1 = $r->UserId;
					$d = $r->Depth;
					if ($d) {
						$r->VisibleTo = $prevId;
					}
				}

				$r->Save();
				$prevId = $r->UserId;
			}
		}
    	print "</ul>";
	}
    echo "</ol>";

?>