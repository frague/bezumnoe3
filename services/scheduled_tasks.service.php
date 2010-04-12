<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty() || !$user->IsSuperAdmin()) {
		die();	// TODO: Implement client functionality
	}

	$id = round($_POST[ScheduledTask::SCHEDULED_TASK_ID]);
	$task = new ScheduledTask();

	switch ($go) {
		case "save":
			$task->FillFromHash($_POST);
			$error = $task->SaveChecked();
			if ($error) {
				echo JsAlert($error, 1);
			} else {
				echo JsAlert("Изменения сохранены.");
			}
			break;
		case "delete":
			if ($id) {
				$task->Id = $id;
				$task->Retrieve();
				if (!$task->IsEmpty()) {
					$task->Delete();
					echo JsAlert("Задача удалена.");
				} else {
					echo JsAlert("Задача не найдена!", 1);
				}
			}
			break;
	}

	
	$types = array(ScheduledTask::TYPE_STATUS, ScheduledTask::TYPE_UNBAN, ScheduledTask::TYPE_EXPIRED_SESSIONS, ScheduledTask::TYPE_RATINGS);
	$condition = "";
	for ($i = 0; $i < sizeof($types); $i++) {
		if ($_POST[$types[$i]]) {
			$condition .= ($condition ? " OR " : "").ScheduledTask::TYPE."='".$types[$i]."'";
		}
	}

	if (!$condition) {
		$condition = "0=1";	// Return nothing
	}

	echo "this.data=[";
	$q = $task->GetRange($from, $amount, $condition);

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$task->FillFromResult($q);
		echo ($i ? "," : "").$task->ToJs();
	}
	echo "];";
	$q->Release();

	echo "this.Total=".$task->GetResultsCount($condition).";";

?>