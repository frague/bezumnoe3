<?php

	function ExecuteScheduledTasks() {
		$task = new ScheduledTask();
		$tasks = $task->LockPendingTasks();
		if (!$tasks) {
			return false;	// No tasks to execute
		}

		$guid = $task->TransactionGuid;
		$q = $task->GetLockedTasks();
		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();
			$task->FillFromResult($q);

			// Execute the task
			$action = $task->GetAction();
			if ($action) {
				$action->Execute();
			}
			if ($task->IsPeriodical()) {
				$task->ExecutionDate = DateFromTime(strtotime(NowDateTime()."+".$task->Periodicity." minutes"));
				$task->TransactionGuid = "";
				$task->Save();
			} else {
				$task->Delete();
			}
		}
	}

	function UnconditionalTasks() {
	
	}

?>