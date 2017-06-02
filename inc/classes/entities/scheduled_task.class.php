<?php

class ScheduledTask extends EntityBase {
    // Constants
    const table = "scheduled_tasks";

    const SCHEDULED_TASK_ID = "SCHEDULED_TASK_ID";
    const TYPE = "TYPE";
    const EXECUTION_DATE = "EXECUTION_DATE";
    const PERIODICITY = "PERIODICITY";
    const PARAMETER1 = "PARAMETER1";
    const PARAMETER2 = "PARAMETER2";
    const PARAMETER3 = "PARAMETER3";
    const TRANSACTION_GUID = "TRANSACTION_GUID";
    const IS_ACTIVE = "IS_ACTIVE";

    const TYPE_UNBAN            = "unban";
    const TYPE_STATUS           = "status";
    const TYPE_INACTIVATED      = "inactivated";
    const TYPE_EXPIRED_SESSIONS = "expired_sessions";
    const TYPE_RATINGS          = "ratings";
    const TYPE_YTKA_BOT         = "ytka";
    const TYPE_VICTORINA_BOT    = "victorina";
    const TYPE_LINGVIST_BOT     = "lingvist";
    const TYPE_TELEGRAM_BOT     = "telegram";
    
    const SCHEDULER_LOGIN = "по расписанию";
    const HANGED_TIMEOUT = "00:10:00";      // 10 minutes to recover
    const PARAMETER = "PARAMETER";

    // Properties
    var $Type;
    var $ExecutionDate;
    var $Periodicity;
    var $Parameter1;
    var $Parameter2;
    var $Parameter3;
    var $TransactionGuid;
    var $IsActive;

    var $Bots = array(self::TYPE_YTKA_BOT, self::TYPE_VICTORINA_BOT, self::TYPE_LINGVIST_BOT, self::TYPE_TELEGRAM_BOT);

    function ScheduledTask() {
        $this->table = self::table;
        parent::__construct("", self::SCHEDULED_TASK_ID);

//      $this->SearchTemplate = "t1.".self::MESSAGE." LIKE '%#WORD#%' OR t4.".Nickname::TITLE." LIKE '%#WORD#%' OR t2.".User::LOGIN." LIKE '%#WORD#%' OR t5.".Nickname::TITLE." LIKE '%#WORD#%' OR t3.".User::LOGIN." LIKE '%#WORD#%'";
        $this->Order = "t1.".self::EXECUTION_DATE." ASC";
    }

    function Clear() {
        $this->Id = -1;
        $this->Type = self::TYPE_UNBAN;
        $this->ExecutionDate = "";
        $this->Periodicity = 0;
        $this->Parameter1 = "";
        $this->Parameter2 = "";
        $this->Parameter3 = "";
        $this->TransactionGuid = MakeGuid(10);
        $this->IsActive = 1;
    }

    function IsPeriodical() {
        return $this->Periodicity > 0;
    }

    function IsBot() {
        return !$this->IsEmpty() && in_array($this->Type, $this->Bots); 
    }

    function FillFromResult($result) {
        $this->Id = $result->Get(self::SCHEDULED_TASK_ID);
        $this->Type = $result->Get(self::TYPE);
        $this->ExecutionDate = $result->Get(self::EXECUTION_DATE);
        $this->Periodicity = $result->Get(self::PERIODICITY);
        $this->Parameter1 = $result->Get(self::PARAMETER1);
        $this->Parameter2 = $result->Get(self::PARAMETER2);
        $this->Parameter3 = $result->Get(self::PARAMETER3);
        $this->TransactionGuid = $result->Get(self::TRANSACTION_GUID);
        $this->IsActive = Boolean($result->Get(self::IS_ACTIVE));
    }

    function FillFromHash($hash) {
        $this->Id = round($hash[self::SCHEDULED_TASK_ID]);
        $this->Type = $hash[self::TYPE];
        $this->ExecutionDate = $hash[self::EXECUTION_DATE];
        $this->Periodicity = $hash[self::PERIODICITY];
        $this->IsActive = Boolean($hash[self::IS_ACTIVE]);
    }

    // Updates task's execution time to NOW() + PERIODICITY
    function Iterate() {
        if (!$this->IsEmpty()) {
            $this->GetByCondition("", $this->IterateExpression());
        }
    }

    // Marks currently pending tasks with TransactionGUID to avaiod duplicated execution
    function LockPendingTasks() {
        $q = $this->GetByCondition(
            "(((".self::EXECUTION_DATE." <= NOW() OR ".self::EXECUTION_DATE." IS NULL) AND ".self::TRANSACTION_GUID." IS NULL) OR ".self::EXECUTION_DATE." <= SUBTIME(NOW(), '".self::HANGED_TIMEOUT."')) AND ".self::IS_ACTIVE."=1",
            $this->LockExpression()
        );
        return $q->AffectedRows();
    }

    // Gets tasks marked with instance's TransactionGUID
    function GetLockedTasks() {
        return $this->GetByCondition("t1.".self::TRANSACTION_GUID."='".SqlQuote($this->TransactionGuid)."'");
    }

    // Releases tasks locked with instance's TransactionGUID
    function ReleaseLockedTasks() {
        $this->GetByCondition("", $this->ReleaseExpression());
    }

    // Returns active bots
    function GetActiveBots() {
        return $this->GetByCondition("t1.".self::TYPE." IN ('".join("', '", $this->Bots)."') AND t1.".self::IS_ACTIVE."=1 ORDER BY ".self::SCHEDULED_TASK_ID." DESC");
    }

    // Returns Action by given task type
    function GetAction() {
        if (!$this->IsEmpty()) {
            switch ($this->Type) {
                case self::TYPE_UNBAN:              return new UnbanAction($this);
                case self::TYPE_STATUS:             return new StatusAction($this);
                case self::TYPE_INACTIVATED:        return new InactivatedAction($this);
                case self::TYPE_EXPIRED_SESSIONS:   return new ExpiredSessionsAction($this);
                case self::TYPE_RATINGS:            return new UpdateRatingAction($this);
                case self::TYPE_YTKA_BOT:           return new YtkaBotAction($this);
                case self::TYPE_VICTORINA_BOT:      return new VictorinaBotAction($this);
                case self::TYPE_LINGVIST_BOT:       return new LingvistBotAction($this);
                case self::TYPE_TELEGRAM_BOT:       return new TelegramBotAction($this);
            }
        }
        return 0;
    }

    // Delete given user's unban tasks
    function DeleteUserUnbans($userId) {
        $this->GetByCondition(
            self::TYPE."='".self::TYPE_UNBAN."' AND ".self::PARAMETER1."=".round($userId),
            $this->ConditionalDeleteExpression()
        );
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li>".self::SCHEDULED_TASK_ID.": ".$this->Id."</li>\n";
        $s.= "<li>".self::TYPE.": ".$this->Type."</li>\n";
        $s.= "<li>".self::EXECUTION_DATE.": ".$this->ExecutionDate."</li>\n";
        $s.= "<li>".self::PERIODICITY.": ".$this->Periodicity."</li>\n";
        $s.= "<li>".self::PARAMETER1.": ".$this->Parameter1."</li>\n";
        $s.= "<li>".self::PARAMETER2.": ".$this->Parameter2."</li>\n";
        $s.= "<li>".self::PARAMETER3.": ".$this->Parameter3."</li>\n";
        $s.= "<li>".self::TRANSACTION_GUID.": ".$this->TransactionGuid."</li>\n";
        $s.= "<li>".self::IS_ACTIVE.": ".$this->IsActive."</li>\n";
        if ($this->IsEmpty()) {
            $s.= "<li> <b>Scheduled Task is not saved!</b>";
        }

        $s.= "</ul>";
        return $s;
    }

    function ToJs() {
        $s = "new stdto(".
round($this->Id).",\"".
JsQuote($this->Type)."\",\"".
JsQuote($this->ExecutionDate)."\",".
round($this->Periodicity).",".
Boolean($this->IsActive).")";
        return $s;
    }

    // SQL
    function ReadExpression() {
        return "SELECT 
    t1.".self::SCHEDULED_TASK_ID.",
    t1.".self::TYPE.",
    t1.".self::EXECUTION_DATE.",
    t1.".self::PERIODICITY.",
    t1.".self::PARAMETER1.",
    t1.".self::PARAMETER2.",
    t1.".self::PARAMETER3.",
    t1.".self::TRANSACTION_GUID.",
    t1.".self::IS_ACTIVE."
FROM 
    ".$this->table." AS t1 
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table." (
    ".self::TYPE.", 
    ".self::EXECUTION_DATE.", 
    ".self::PERIODICITY.", 
    ".self::PARAMETER1.", 
    ".self::PARAMETER2.", 
    ".self::PARAMETER3.", 
    ".self::TRANSACTION_GUID.",
    ".self::IS_ACTIVE." 
) VALUES (
    '".SqlQuote($this->Type)."', 
    ".Nullable($this->ExecutionDate).", 
    ".round($this->Periodicity).", 
    ".SqlFnQuote($this->Parameter1).", 
    ".SqlFnQuote($this->Parameter2).", 
    ".SqlFnQuote($this->Parameter3).", 
    ".Nullable($this->TransactionGuid).",
    ".Boolean($this->IsActive)."
)";
    }

    function UpdateExpression() {
        $result = "UPDATE ".$this->table." SET 
".self::EXECUTION_DATE."=".Nullable($this->ExecutionDate).", 
".self::PERIODICITY."=".round($this->Periodicity).", 
".self::PARAMETER1."=".SqlFnQuote($this->Parameter1).", 
".self::PARAMETER2."=".SqlFnQuote($this->Parameter2).", 
".self::PARAMETER3."=".SqlFnQuote($this->Parameter3).", 
".self::TRANSACTION_GUID."=".Nullable($this->TransactionGuid).",
".self::IS_ACTIVE."=".Boolean($this->IsActive)."
WHERE 
    ".self::SCHEDULED_TASK_ID."=".round($this->Id);
        return $result;
    }

    function UpdateParameterExpression($parameter, $value) {
        $result = "UPDATE ".$this->table." SET 
".self::PARAMETER.round($parameter)."=".$value." 
WHERE 
    ".self::SCHEDULED_TASK_ID."=".round($this->Id);
        return $result;
    }


    function LockExpression() {
        $result = "UPDATE ".$this->table." SET 
".self::TRANSACTION_GUID."=".Nullable($this->TransactionGuid)."
WHERE 
    ##CONDITION##";
        return $result;
    }

    function ReleaseExpression() {
        $result = "UPDATE ".$this->table." SET 
".self::TRANSACTION_GUID."=NULL
WHERE 
    ".self::TRANSACTION_GUID."='".SqlQuote($this->TransactionGuid)."'";
        return $result;
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table." WHERE ".self::SCHEDULED_TASK_ID."=".SqlQuote($this->Id);
    }

    function ConditionalDeleteExpression() {
        return "DELETE FROM ".$this->table." WHERE ##CONDITION##";
    }

    function IterateExpression() {
        $result = "UPDATE ".$this->table." SET 
".self::EXECUTION_DATE."=DATE_ADD(NOW(), INTERVAL `".self::PERIODICITY."` MINUTE),
".self::TRANSACTION_GUID." = NULL
WHERE 
    ".self::SCHEDULED_TASK_ID."=".SqlQuote($this->Id);
        return $result;
    }
}

/* ---------------- Ready Tasks----------------- */

abstract class UserScheduledTask extends ScheduledTask {
    function UserScheduledTask($type, $userId, $executionDate) {
        parent::__construct();
    
        $this->ExecutionDate = $executionDate;
        $this->Parameter1 = round($userId);
        $this->TransactionGuid = "";
        $this->Type = $type;
    }
}

abstract class Bot extends UserScheduledTask {
    function Bot($type, $userId, $roomId) {
        parent::__construct($type, $userId, "");
    
        $this->Parameter2 = round($roomId);
        $this->Periodicity = 1;
    }
}

class UnbanScheduledTask extends UserScheduledTask {
    function UnbanScheduledTask($userId, $executionDate) {
        parent::__construct(ScheduledTask::TYPE_UNBAN, $userId, $executionDate);
    }
}

class InactivatedScheduledTask extends UserScheduledTask {
    function StatusScheduledTask($userId, $executionDate) {
        parent::__construct(ScheduledTask::TYPE_INACTIVATED, $userId, $executionDate);
    }
}

class StatusScheduledTask extends UserScheduledTask {
    function StatusScheduledTask($userId, $executionDate) {
        parent::__construct(ScheduledTask::TYPE_STATUS, $userId, $executionDate);
    }
}

class CheckSumScheduledTask extends UserScheduledTask {
    function CheckSumScheduledTask($userId, $executionDate) {
        parent::__construct(ScheduledTask::TYPE_CHECKUM, $userId, $executionDate);
    }
}

class UpdateRatingsScheduledTask extends UserScheduledTask {
    function UpdateRatingsScheduledTask($userId, $executionDate) {
        parent::__construct(ScheduledTask::TYPE_RATINGS, $userId, $executionDate);
    }
}

class YtkaBotScheduledTask extends Bot {
    function YtkaBotScheduledTask($userId = -1, $roomId = -1) {
        parent::__construct(ScheduledTask::TYPE_YTKA_BOT, $userId, $roomId);
    }
}

class VictorinaBotScheduledTask extends Bot {
    function VictorinaBotScheduledTask($userId = -1, $roomId = -1) {
        parent::__construct(ScheduledTask::TYPE_VICTORINA_BOT, $userId, $roomId);
    }
}

class LingvistBotScheduledTask extends Bot {
    function LingvistBotScheduledTask($userId = -1, $roomId = -1) {
        parent::__construct(ScheduledTask::TYPE_LINGVIST_BOT, $userId, $roomId);
    }
}

class TelegramBotScheduledTask extends Bot {
    function TelegramBotScheduledTask($userId = -1, $roomId = -1) {
        parent::__construct(ScheduledTask::TYPE_TELEGRAM_BOT, $userId, $roomId);
    }
}


/* ------------------ Actions ------------------ */

abstract class BaseAction {
    var $Task, $user;
    function __construct($task) {
        $this->Task = $task;
        $this->user = "";
    }

    function GetUser($userId = -1) {
        if ($userId <= 0) {
            $userId = round($this->Task->Parameter1);
        }
        $user = new UserComplete();
        $user->GetById($userId);

        if ($user->IsEmpty()) {
            SaveLog("Пользователь (Id: ".$this->Task->Parameter1.") не найден. Задача: ".$this->Task->Type, -1, ScheduledTask::SCHEDULER_LOGIN, AdminComment::SEVERITY_ERROR);
            return false;
        }
        $this->user = $user;
        return true;
    }

    abstract function ExecuteByTimer();
}

// Unban action
class UnbanAction extends BaseAction {
    
    function ExecuteByTimer() {
        if (!$this->GetUser()) {
            return false;
        }

        if (!$this->user->User->IsBanned()) {
            SaveLog("Не удалось разбанить пользователя - не забанен.", $this->user->User->Id, ScheduledTask::SCHEDULER_LOGIN, AdminComment::SEVERITY_ERROR);
            return false;
        }
        $this->user->User->StopBan();
        $this->user->User->Save();
        LogBanEnd($this->user->User->Id, ScheduledTask::SCHEDULER_LOGIN);
        return true;
    }
}

// Setting "Oldbie" status to user
class StatusAction extends BaseAction {
    function ExecuteByTimer() {
        if (!$this->GetUser()) {
            return false;
        }
        
        $status = new Status();
        $status->GetStandardStatus(Status::RIGHTS_OLDBIE);
        if ($status->IsEmpty()) {
            SaveLog("Не удалось установить пользователю статус \"старожил\". Статус не найден.", $this->user->User->Id, self::SCHEDULER_LOGIN, AdminComment::SEVERITY_ERROR);
            return false;
        }
        $p = new Profile();
        $p->GetByUserId($this->user->User->Id);

        if ($p->IsEmpty()) {
            SaveLog("Не удалось изменить статус пользователю: профиль не найден", $this->user->User->Id, ScheduledTask::SCHEDULER_LOGIN, AdminComment::SEVERITY_ERROR);
            return false;
        }

        if (DatesDiff($p->LastVisit) > 31) {
            SaveLog("Отказано в установке нового статуса: не появлялся в чате больше месяца", $this->user->User->Id, ScheduledTask::SCHEDULER_LOGIN, AdminComment::SEVERITY_ERROR);
            return false;
        }

        $this->user->User->StatusId = $status->Id;
        SaveLog("Установлен статус \"старожил\".", $this->user->User->Id, ScheduledTask::SCHEDULER_LOGIN);
        $this->user->User->Save();
        return true;
    }
}

// Removing expired user sessions
class ExpiredSessionsAction extends BaseAction {
    function ExecuteByTimer() {
      global $db;

        $user1 = new User();
        $q = $user1->GetExpiredUsers();
        $u = array();
        for ($i = 0; $i < $q->NumRows(); $i++) {
            $q->NextResult();
            $roomId = $q->Get(User::ROOM_ID);
            $session = $q->Get(User::SESSION);
            if (!preg_match("/^telegram[0-9A-Z]+$/", $session)) {
                $u[$roomId] .= ($u[$roomId] ? ", " : "").$q->Get(User::LOGIN);
            }
        }
        
        // Left user sessions, but remove room information
        $q = $db->Query("UPDATE ".User::table." t1 SET t1.".User::ROOM_ID."=NULL, t1.".User::SESSION_PONG."=NULL WHERE ".$user1->ExpireCondition());

        if (sizeof($u) > 0) {
            while (list($roomId,$users) = each($u)) {
                $message = new QuitMessage($users.(preg_match("/, /", $users) ? " покидают" : " покидает")." чат. ", $roomId);
                $message->Save();
                TriggerBotsByMessage($message);
            }
        }
        return true;
    }
}

// Removing accounts not activated during the 48 hours after the registration
class InactivatedAction extends BaseAction {
    function ExecuteByTimer() {
        $deadline = DateFromTime(time() - 60 * 60 * 48);

        $users = new UserComplete();
        $q = $users->GetNotActivatedBefore($deadline);
        $n = $q->NumRows();
        if (!$n) {
            SaveLog("Проверка неактивированных за 48 часов аккаунтов - не обнаружено", -1, ScheduledTask::SCHEDULER_LOGIN, AdminComment::SEVERITY_WARNING);
            return true;
        }
        $log = "Удалены неактивированные аккаунты (".$n."): ";
        for ($i = 0; $i < $n; $i++) {
            $q->NextResult();
            $tmp_user = new UserComplete();
            $tmp_user->FillFromResult($q);
            $log .= ($i ? ", " : " ").$tmp_user->User->Login;
            $tmp_user->Delete();
        }
        SaveLog($log, -1, ScheduledTask::SCHEDULER_LOGIN, AdminComment::SEVERITY_WARNING);

        return true;
    }
}

// Update ratings
class UpdateRatingAction extends BaseAction {
    function ExecuteByTimer() {

        Rating::UpdateRatings();
        SaveLog("Обновление рейтингов.", -1, ScheduledTask::SCHEDULER_LOGIN, AdminComment::SEVERITY_WARNING);

        return true;
    }
}


//---------------------------------------------------------------------------
// Base bots actions actions
abstract class BotBaseAction extends BaseAction {
    var $room;
    var $lastExecutionTime;
    var $messages;

    var $settings, $name;

    function GetRoom() {
        $this->room = new Room();
        $this->room->GetById(round($this->Task->Parameter2));
        return !($this->room->IsEmpty() || $this->room->IsDeleted);
    }

    function IsValid() {
        if (!$this->GetUser() || !$this->GetRoom()) {
            SaveLog("Бот (".$this->Task->Type.") остановлен. Некорректно указан пользователь или комната.", -1, ScheduledTask::SCHEDULER_LOGIN, AdminComment::SEVERITY_ERROR);
            $this->Task->IsActive = 0;
            $this->Task->Save();
            return false;
        }
        return true;
    }
    
    function Init() {
      global $db;
        if (!$this->Task->IsActive || !$this->IsValid()) {
            return false;
        }

        $this->lastExecutionTime = $this->Task->ExecutionDate;
        if (!$this->lastExecutionTime && $this->user->User->RoomId) {
            $text = $this->user->Settings->EnterMessage;
            if (!$text) {
                $text = "В чат входит %name.";
            }
            $message = new EnterMessage(str_replace("%name", Clickable($this->user->DisplayedName()), $text), $this->room->Id);
            $message->Save();

            // Entering to room
            $this->user->User->RoomId = $this->room->Id;
            $this->user->User->Save();
        }
        // Ponging session & updating checksum
        $this->user->User->TouchSession();
        $this->user->UpdateChecksum();
        return true;
    }

    function ShutDown() {
      global $db;
        if (!$this->Task->IsActive || !$this->IsValid()) {
            return false;
        }
        $text = $this->user->Settings->QuitMessage;
        if (!$text) {
            $text = "%name выходит из чата.";
        }
        $message = new QuitMessage(str_replace("%name", Clickable($this->user->DisplayedName()), $text), $this->room->Id);
        $message->Save();

        // Leaving
        $this->user->User->GoOffline();
        $this->user->User->Save();
    }

    abstract function ExecuteByMessage($message);
}

/*
    These bots actions will be performed on timer basis:
    e.g. Victorina phases switching, YTKA random phrase etc.
    + Session maintainance to be shown in users list.

    New message reactions will be set in separate class.
*/


// Ytka bot actions
class YtkaBotAction extends BotBaseAction {
    function ExecuteByTimer() {
      global $db;

        if ($this->Init()) {
            #$m = new Message(sizeof($this->messages), $this->user);
            #$m->RoomId = $this->room->Id;
            #$m->Save();

            $this->Task->Parameter3 = "fn:NOW()";
            $this->Task->Save();

            return true;
        }
        return false;
    }
    
    function ExecuteByMessage($message) {
      global $db;

        if ($this->Init() && !$message->IsPrivate() && !$message->FromYtka) {
            $myName = $this->user->DisplayedName();
            if (mb_strpos($message->Text, $myName) !== false) {
                $item = new YtkaDictionaryItem();

                // Reply
                $item->PickRandom();
                
                $u = new User();
                $authorName = $u->GetUserCurrentName($message->UserId) OR "ты";
                $ytkaName = $u->GetUserCurrentName($this->user->Id) OR "YTKA";
                $msg = new Message(str_replace("%name", $authorName, $item->Content), $this->user->User);
                $msg->RoomId = $message->RoomId;
                $msg->UserName = $ytkaName;
                $msg->Save();
                $msg->FromYtka = True;
                TriggerBotsByMessage($msg);

                // Store new item in the dictionary
                $item->Clear();
                $item->UserId = $message->UserId;
                $item->Content = str_replace($myName, "%name", $message->Text);
                $item->Save();
            }


            return true;
        }
        return false;
    }
}

// Victorina bot actions
class VictorinaBotAction extends BotBaseAction {
    function ExecuteByTimer() {
      global $db;
        
        if ($this->Init()) {
            $this->Task->Parameter3 = ++$this->Task->Parameter3%5;
            $this->Task->Save();

            $m = new Message($this->Task->Parameter3, $this->user->User);
            $m->RoomId = $this->room->Id;
            #$m->Save();
            return true;
        }
        return false;
    }

    function ExecuteByMessage($message) {
        return true;
    }
}

// Lingvist bot actions
class LingvistBotAction extends BotBaseAction {
    function ExecuteByTimer() {
      global $db;
        
        return true;
    }

    function ExecuteByMessage($message) {
        return true;
    }
}

// Telegram bot actions
class TelegramBotAction extends BotBaseAction {
    function ExecuteByTimer() {
        return true;
    }
    
    function ExecuteByMessage($message) {
      global $db;

        if (!$message->IsPrivate() && !$message->FromTelegram) {
            try {
                $data = json_encode($message->ToJSON());

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, "http://bzmn.herokuapp.com/push");
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    "Content-Type: application/json", 
                    "Content-Length: ".strlen($data))
                );
                curl_setopt($curl, CURLOPT_HEADER, 0);
                curl_setopt($curl, CURLOPT_TIMEOUT, 1);
                curl_exec($curl);
                curl_close($curl);
            }
            catch (Exception $e) {
                print $e;
            }
        }
        return true;
    }
}



?>
