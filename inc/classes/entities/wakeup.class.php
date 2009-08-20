<?

class Wakeup extends EntityBase {
	// Constants
	const table = "wakeups";

	const WAKEUP_ID = "WAKEUP_ID";
	const FROM_USER_ID = "USER_ID";
	const FROM_USER_NAME = "USER_NAME";
	const TO_USER_ID = "TO_USER_ID";
	const TO_USER_NAME = "TO_USER_NAME";
	const DATE = "DATE";
	const MESSAGE = "MESSAGE";
	const IS_READ = "IS_READ";

	// Properties
	var $FromUserId;
	var $FromUserName;
	var $ToUserId;
	var $ToUserName;
	var $Date;
	var $Message;
	var $IsRead;

	function Wakeup($text="", $fromUserId = -1, $toUserId = -1) {
		$this->table = self::table;
		parent::__construct("", self::WAKEUP_ID);

		$this->Message = $text;
		$this->FromUserId = $fromUserId;
		$this->ToUserId = $toUserId;

		$this->SearchTemplate = "t1.".self::MESSAGE." LIKE '%#WORD#%' OR t4.".Nickname::TITLE." LIKE '%#WORD#%' OR t2.".User::LOGIN." LIKE '%#WORD#%' OR t5.".Nickname::TITLE." LIKE '%#WORD#%' OR t3.".User::LOGIN." LIKE '%#WORD#%'";
		$this->Order = "t1.".self::DATE." DESC";
	}

	function Clear() {
		$this->Id = -1;
		$this->FromUserId = "";
		$this->ToUserId = "";
		$this->Date = NowDateTime();
		$this->Message = "";
		$this->IsRead = 0;
	}

	function IsIncoming($userId) {
		return $this->ToUserId == $userId;
	}
	
	function FillFromResult($result) {
		$this->Id = $result->Get(self::WAKEUP_ID);
		$this->FromUserId = $result->Get(self::FROM_USER_ID);
		$this->FromUserName = $result->Get(self::FROM_USER_NAME);
		$this->ToUserId = $result->Get(self::TO_USER_ID);
		$this->ToUserName = $result->Get(self::TO_USER_NAME);
		$this->Date = $result->Get(self::DATE);
		$this->Message = $result->Get(self::MESSAGE);
		$this->IsRead = $result->Get(self::IS_READ);
	}

	
	/* Search */
	function CountForUser($userId, $search) {
		$q = $this->GetByCondition();
		return 0;
	}

	function GetForUser($userId, $from = 0, $limit, $condition) {
		$from = round($from);
		$limit = round($limit);
		if (!$condition) {
			$condition = "1=1";
		}

	  	return $this->GetByCondition(
	  		$this->ReadUserWakeupsExpression($userId).
	  		($condition ? " AND ".$condition : "").
	  		" ORDER BY ".$this->Order." LIMIT ".($from ? $from."," : "").$limit
	  	); 
	}
	/* ------ */

	function FillForUser($user, $wakeup_id = 0) {
		if (!$user->IsEmpty()) {
			$wakeup_id = round($wakeup_id);
			return $this->FillByCondition($this->ReadUserWakeupsExpression($user->Id).($wakeup_id > 0 ? " AND t1.".self::WAKEUP_ID."=".$wakeup_id : ""));
		}
	}

	function GetUserUnreadWakeupsIds($user) {
	 global $db;

		$result = array();
		if ($this->IsConnected()) {
			$q = $db->Query("SELECT
	t1.".self::WAKEUP_ID.",
	COALESCE(t3.".Nickname::TITLE.",t2.".User::LOGIN.") AS ".self::FROM_USER_NAME."
FROM ".$this->table." AS t1 
	LEFT JOIN ".User::table." t2 ON t2.".User::USER_ID."=t1.".self::FROM_USER_ID."
	LEFT JOIN ".Nickname::table." t3 ON t3.".Nickname::USER_ID."=t1.".self::FROM_USER_ID." AND t3.".Nickname::IS_SELECTED."=1
WHERE t1.".self::TO_USER_ID."=".$user->Id." AND t1.".self::IS_READ."<>1 ORDER BY ".self::WAKEUP_ID." ASC");
			for ($i = 0; $i < $q->NumRows(); $i++) {
				$q->NextResult();
				$result[$q->Get(self::WAKEUP_ID)] = $q->Get(self::FROM_USER_NAME);
			}
		}
		return $result;
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::WAKEUP_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::FROM_USER_ID.": ".$this->FromUserId."</li>\n";
		$s.= "<li>".self::FROM_USER_NAME.": ".$this->FromUserName."</li>\n";
		$s.= "<li>".self::TO_USER_ID.": ".$this->ToUserId."</li>\n";
		$s.= "<li>".self::TO_USER_NAME.": ".$this->ToUserName."</li>\n";
		$s.= "<li>".self::DATE.": ".$this->Date."</li>\n";
		$s.= "<li>".self::MESSAGE.": ".$this->Message."</li>\n";
		$s.= "<li>".self::IS_READ.": ".$this->IsRead."</li>\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Wakeup is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	function ToJs($userId = 0) {
		$isIncoming = $this->IsIncoming($userId);
		$id = $isIncoming ? $this->FromUserId : $this->ToUserId;
		$name = $isIncoming ? $this->FromUserName : $this->ToUserName;

		$s = "new wdto(".
round($this->Id).",".
round($id).",\"".
JsQuote($name)."\",".
Boolean($isIncoming).",\"".
JsQuote($this->Date)."\",\"".
JsQuote($this->Message)."\",".
Boolean($this->IsRead).")";
		return $s;
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::WAKEUP_ID.",
	t1.".self::FROM_USER_ID.",
	t1.".self::TO_USER_ID.",
	t1.".self::DATE.",
	t1.".self::MESSAGE.",
	t1.".self::IS_READ.",
	COALESCE(t4.".Nickname::TITLE.",t2.".User::LOGIN.") AS ".self::FROM_USER_NAME.",
	COALESCE(t5.".Nickname::TITLE.",t3.".User::LOGIN.") AS ".self::TO_USER_NAME."
FROM 
	".$this->table." AS t1 
LEFT JOIN ".User::table." AS t2
	ON t2.".User::USER_ID."=t1.".self::FROM_USER_ID."
LEFT JOIN ".User::table." AS t3
	ON t3.".User::USER_ID."=t1.".self::TO_USER_ID."
LEFT JOIN ".Nickname::table." AS t4
	ON (t4.".Nickname::USER_ID."=t1.".self::FROM_USER_ID." AND t4.".Nickname::IS_SELECTED."=1)
LEFT JOIN ".Nickname::table." AS t5
	ON (t5.".Nickname::USER_ID."=t1.".self::TO_USER_ID." AND t5.".Nickname::IS_SELECTED."=1)
WHERE
	##CONDITION##";
	}

	function ReadUserWakeupsExpression($user_id) {
		return 	"
(t1.".self::FROM_USER_ID."=".$user_id." OR 
t1.".self::TO_USER_ID."=".$user_id.")";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." (
	".self::FROM_USER_ID.", 
	".self::TO_USER_ID.", 
	".self::DATE.", 
	".self::MESSAGE.", 
	".self::IS_READ." 
) VALUES (
	'".SqlQuote($this->FromUserId)."', 
	'".SqlQuote($this->ToUserId)."', 
	'".NowDateTime()."',
	'".SqlQuote($this->Message)."', 
	'".Boolean($this->IsRead)."'
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::FROM_USER_ID."=".SqlQuote($this->FromUserId).", 
".self::TO_USER_ID."=".SqlQuote($this->ToUserId).", 
".self::DATE."='".SqlQuote($this->Date)."', 
".self::MESSAGE."='".SqlQuote($this->Message)."', 
".self::IS_READ."='".SqlQuote($this->IsRead)."'
WHERE 
	".self::WAKEUP_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::WAKEUP_ID."=".SqlQuote($this->Id);
	}

	function SearchCountExpression() {
		return "SELECT COUNT(1)
FROM 
	".$this->table." AS t1 
LEFT JOIN ".User::table." AS t2
	ON t2.".User::USER_ID."=t1.".self::FROM_USER_ID."
LEFT JOIN ".User::table." AS t3
	ON t3.".User::USER_ID."=t1.".self::TO_USER_ID."
LEFT JOIN ".Nickname::table." AS t4
	ON (t4.".Nickname::USER_ID."=t1.".self::FROM_USER_ID." AND t4.".Nickname::IS_SELECTED."=1)
LEFT JOIN ".Nickname::table." AS t5
	ON (t5.".Nickname::USER_ID."=t1.".self::TO_USER_ID." AND t5.".Nickname::IS_SELECTED."=1)
WHERE
	##CONDITION##
";
	}
}

?>