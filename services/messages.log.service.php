<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();	// TODO: Implement client functionality
	}


	$message = new Message();

	// --- Filtering ---
	$condition = "";
	$roomsCondition = MakeSearchCriteria("DATE", Message::DATE, "SEARCH", $message->SearchTemplate);

	// Filter by room
	$room_id = round($_POST["ROOM_ID"]);
	if ($room_id > 0) {
		$condition = ($roomsCondition ? $roomsCondition." AND " : "")."(t1.".Message::ROOM_ID."=".$room_id." OR t1.".Message::ROOM_ID."=-1)";
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
				$message->UserName = Mark($message->UserName, $keywords);
				$message->ToUserName = Mark($message->ToUserName, $keywords);
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