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
				$d = ParseDate($task->ExecutionDate);
				$t = time();
				if ($t - $d > $task->Periodicity) {
					$d += (1 + round(($t - $d) / $task->Periodicity)) * $task->Periodicity;
				} else {
					$d += $task->Periodicity;
				}
				$task->ExecutionDate = DateFromTime($d);
				$task->TransactionGuid = "";
				$task->Save();
			} else {
				$task->Delete();
			}
		}
	}

?>