<?php

    function ExecuteScheduledTasks() {
        $task = new ScheduledTask();
        $tasks = $task->LockPendingTasks();
        if (!$tasks) {
            return false;   // No tasks to execute
        }

        $guid = $task->TransactionGuid;
        $q = $task->GetLockedTasks();
        for ($i = 0; $i < $q->NumRows(); $i++) {
            $q->NextResult();
            $task->FillFromResult($q);

            // Execute the task
            $action = $task->GetAction();
            if ($action) {
                try {
                    $action->ExecuteByTimer();
                } catch (Exception $e) {
                    SaveLog("Ошибка исполнения задачи по расписанию: ".$e->getMessage(), -1, ScheduledTask::SCHEDULER_LOGIN);
                    //TODO: Disable this task?
                }
            }
            if ($task->IsPeriodical()) {
                $task->Iterate();
            } else {
                $task->Delete();
            }
        }
        $q->Release();
    }

    function UnconditionalTasks() {
    
    }

    function TriggerBotsByMessage($message) {
        // Getting active bots
        $st = new ScheduledTask();
        $q = $st->GetActiveBots();

        print "/*";

        for ($i = 0; $i < $q->NumRows(); $i++) {
            $q->NextResult();
            $st->FillFromResult($q);

            if ($st->IsEmpty() || ($st->Parameter2 && $st->Parameter2 != $message->RoomId)) {
                continue;
            }
            
            $action = $st->GetAction();

            if ($action) {
                try {
                    print $st->Type;
                    $action->ExecuteByMessage($message);
                } catch (Exception $e) {
                    //SaveLog("Ошибка исполнения задачи по расписанию: ".$e->getMessage(), -1, ScheduledTask::SCHEDULER_LOGIN);
                    //TODO: Disable this task?
                }
            }
        }
        print "*/";
    }

?>