<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();	// TODO: Implement client functionality
	}


	$message = new Message();

	// --- Filtering ---
	$condition = "";
	$roomsCondition = "";

	// Dates condition
	$d = $_POST["DATE"];
	if ($d) {
		$t = ParseDate($d);
		if ($t !== false) {
			$condition = "t1.".Message::DATE." LIKE '".DateFromTime($t, "Y-m-d")."%' ";
			$roomsCondition = $condition;
		}
	}

	// Search keywords
	$keywords = trim(substr(UTF8toWin1251($_POST["SEARCH"]), 0, 1024));
	$search = MakeKeywordSearch($keywords, $message->SearchTemplate);
	if ($search) {
		$condition .= ($condition ? " AND " : "").$search;
		$roomsCondition = $condition;
	}

	// Filter by room
	$room_id = round($_POST["ROOM_ID"]);
	if ($room_id > 0) {
		$condition .= ($condition ? " AND " : "")."(t1.".Message::ROOM_ID."=".$room_id." OR t1.".Message::ROOM_ID."=-1)";
	}
	// ---

	echo "this.data=[";
	if ($condition) {
		$expression = $message->ReadExpression();
		$expression = str_replace("FROM", ",t6.".Settings::FONT_COLOR." FROM", $expression);
		$expression = str_replace(
			"WHERE", 
			"LEFT JOIN ".Settings::table." AS t6
	ON t6.".Settings::USER_ID."=t1.".Message::USER_ID."
WHERE", 
			$expression);

		$q = $message->GetRange($from, $amount, $condition." ORDER BY t1.".Message::DATE." DESC", $expression);

		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();
			$message->FillFromResult($q);
			if ($keywords) {
				$message->Text = Mark($message->Text, $keywords);
			}
			echo ($i ? "," : "").$message->ToJs($q->Get(Settings::FONT_COLOR));
		}
	}
	echo "];";

	echo "this.Total=".$message->GetResultsCount($condition).";";

	// Rooms
	$room = new Room();
	$select = "SELECT DISTINCT t1.".Message::ROOM_ID." ".substr($message->ReadExpression(), strpos($message->ReadExpression(), "FROM"));
	$select = str_replace("##CONDITION##", $roomsCondition ? $roomsCondition : "1=1", $select);

	$q = $room->GetByCondition(
		"t1.".Room::ROOM_ID." IN (".$select.")", 
		$room->ListRoomsExpression());
	echo "this.Rooms=[";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$room->FillFromResult($q);
		echo ($i ? "," : "").$room->ToDTO();
	}
	echo "]";
?>