<?

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

	const TYPE_UNBAN	= "unban";
	const TYPE_STATUS	= "status";
	
	const SCHEDULER_LOGIN = "по расписанию";

	// Properties
	var $Type;
	var $ExecutionDate;
	var $Periodicity;
	var $Parameter1;
	var $Parameter2;
	var $Parameter3;
	var $TransactionGuid;

	function ScheduledTask() {
		$this->table = self::table;
		parent::__construct("", self::SCHEDULED_TASK_ID);

//		$this->SearchTemplate = "t1.".self::MESSAGE." LIKE '%#WORD#%' OR t4.".Nickname::TITLE." LIKE '%#WORD#%' OR t2.".User::LOGIN." LIKE '%#WORD#%' OR t5.".Nickname::TITLE." LIKE '%#WORD#%' OR t3.".User::LOGIN." LIKE '%#WORD#%'";
		$this->Order = "t1.".self::EXECUTION_DATE." ASC";
	}

	function Clear() {
		$this->Id = -1;
		$this->Type = self::TYPE_UNBAN;
		$this->ExecutionDate = NowDateTime();
		$this->Periodicity = 0;
		$this->Parameter1 = "";
		$this->Parameter2 = "";
		$this->Parameter3 = "";
		$this->TransactionGuid = MakeGuid(10);
	}

	function IsPeriodical() {
		return $this->Periodicity > 0;
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
	}

	// Marks currently pending tasks with TransactionGUID to avaiod duplicated execution
	function LockPendingTasks() {
		$q = $this->GetByCondition(
			self::EXECUTION_DATE."<='".NowDateTime()."' AND ".self::TRANSACTION_GUID." IS NULL",
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

	// Returns Action by given task type
	function GetAction() {
		if (!$this->IsEmpty()) {
			switch ($this->Type) {
				case self::TYPE_UNBAN:	return new UnbanAction($this);
				case self::TYPE_STATUS:	return new StatusAction($this);
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
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Scheduled Task is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

/*	function ToJs() {
		$s = "new stdto(".
round($this->Id).",".
round($id).",\"".
JsQuote($name)."\",".
Boolean($isIncoming).",\"".
JsQuote($this->Date)."\",\"".
JsQuote($this->Message)."\",".
Boolean($this->IsRead).")";
		return $s;
	}*/

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
	t1.".self::TRANSACTION_GUID."
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
	".self::TRANSACTION_GUID." 
) VALUES (
	'".SqlQuote($this->Type)."', 
	'".SqlQuote($this->ExecutionDate)."', 
	".round($this->Periodicity).", 
	'".SqlQuote($this->Parameter1)."', 
	'".SqlQuote($this->Parameter2)."', 
	'".SqlQuote($this->Parameter3)."', 
	".Nullable($this->TransactionGuid)."
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::TYPE."='".SqlQuote($this->Type)."', 
".self::EXECUTION_DATE."='".SqlQuote($this->ExecutionDate)."', 
".self::PERIODICITY."=".round($this->Periodicity).", 
".self::PARAMETER1."='".SqlQuote($this->Parameter1)."', 
".self::PARAMETER2."='".SqlQuote($this->Parameter2)."', 
".self::PARAMETER3."='".SqlQuote($this->Parameter3)."', 
".self::TRANSACTION_GUID."=".Nullable($this->TransactionGuid)."
WHERE 
	".self::SCHEDULED_TASK_ID."=".SqlQuote($this->Id);
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
}

/* ---------------- Ready Tasks----------------- */

abstract class UserScheduledTask extends ScheduledTask {
	function UserScheduledTask($userId, $executionDate) {
		parent::__construct();
	
		$this->Parameter1 = round($userId);
		$this->ExecutionDate = $executionDate;
		$this->TransactionGuid = "";
	}
}

class UnbanScheduledTask extends UserScheduledTask {
	function UnbanScheduledTask($userId, $executionDate) {
		parent::__construct($userId, $executionDate);
		$this->Type = ScheduledTask::TYPE_UNBAN;
	}
}

class StatusScheduledTask extends UserScheduledTask {
	function StatusScheduledTask($userId, $executionDate) {
		parent::__construct($userId, $executionDate);
		$this->Type = ScheduledTask::TYPE_STATUS;
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
			$userId = $this->Task->Parameter1;
		}
		$user = new User($userId);
		$user->Retrieve();
		if ($user->IsEmpty()) {
		 	SaveLog("Пользователь (Id: ".$this->Task->Parameter1.") не найден. Задача: ".$this->Task->Type, -1, ScheduledTask::SCHEDULER_LOGIN, AdminComment::SEVERITY_ERROR);
			return false;
		}
		$this->user = $user;
		return true;
	}

	abstract function Execute();
}

// Unban action
class UnbanAction extends BaseAction {
	
	function Execute() {
		if (!$this->GetUser()) {
			return false;
		}

		if (!$this->user->IsBanned()) {
		 	SaveLog("Не удалось разбанить пользователя - не забанен.", $this->user->Id, ScheduledTask::SCHEDULER_LOGIN, AdminComment::SEVERITY_ERROR);
			return false;
		}
		$this->user->StopBan();
		$this->user->Save();
	 	LogBanEnd($this->user->Id, ScheduledTask::SCHEDULER_LOGIN);
		return true;
	}
}

// Setting "Oldbie" status to user
class StatusAction extends BaseAction {
	function Execute() {
		if (!$this->GetUser()) {
			return false;
		}
		$status = new Status();
		$status->GetStandardStatus(Status::RIGHTS_OLDBIE);
		if ($status->IsEmpty()) {
		 	SaveLog("Не удалось установить пользователю статус \"старожил\". Статус не найден.", $this->user->Id, self::SCHEDULER_LOGIN, AdminComment::SEVERITY_ERROR);
		 	return false;
		}
		$this->user->StatusId = $status->Id;
	 	SaveLog("Установлен статус \"старожил\".", $this->user->Id, ScheduledTask::SCHEDULER_LOGIN);
		$this->user->Save();
	}
}

?>