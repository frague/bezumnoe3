<?

class User extends EntityBase {
	// Constants
	const table = "users";

	const USER_ID = "USER_ID";
	const LOGIN = "LOGIN";
	const PASSWORD = "PASSWORD";
	const PASSWORD_CONFIRM = "PASSWORD_CONFIRM";
	const ROOM_ID = "ROOM_ID";
	const ROOM_IS_PERMITTED = "ROOM_IS_PERMITTED";
	const STATUS_ID = "STATUS_ID";
	const SESSION = "SESSION";
	const SESSION_PONG = "SESSION_PONG";
	const SESSION_CHECK = "SESSION_CHECK";
	const SESSION_ADDRESS = "SESSION_ADDRESS";
	const AWAY_MESSAGE = "AWAY_MESSAGE";
	const AWAY_TIME = "AWAY_TIME";
	const BANNED_TILL = "BANNED_TILL";
	const BAN_REASON = "BAN_REASON";
	const BANNED_BY = "BANNED_BY";
	const KICK_MESSAGES = "KICK_MESSAGES";
	const GUID = "GUID";
	const CHECK_SUM = "CHECK_SUM";
	

	// Properties
	var $Id;
	var $Login;
	var $Password;
	var $PasswordBackup;
	var $RoomId;
	var $RoomIsPermitted;
	var $StatusId;
	var $Session;
	var $SessionPong;
	var $SessionCheck;
	var $SessionAddress;
	var $AwayMessage;
	var $AwayTime;
	var $BannedTill;
	var $BanReason;
	var $BannedBy;
	var $KickMessages;
	var $Guid;
	var $CheckSum;

	// Fields
	var $sessionKeyLength = 10;

	function User($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::USER_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->Login = "";
		$this->Password = "";
		$this->PasswordBackup = "";
		$this->RoomId = -1;
		$this->RoomIsPermitted = 0;
		$this->StatusId = -1;
		$this->KickMessages = "";
		$this->Guid = "";
		$this->CheckSum = -1;

		$this->BackFromAway();
		$this->StopBan();
		$this->ClearSession();
	}

	function CheckSum($extended = false) {
		$cs = 0;
		$cs += CheckSum($this->RoomId);
		$cs += CheckSum($this->RoomIsPermitted);
		$cs += CheckSum($this->IsAway() ? " ".$this->AwayMessage : "");
		$cs += CheckSum($this->BanReason);
		$cs += CheckSum($this->BannedBy);

		return $cs;
		
/*

-	var $Login;
-	var $Password;
-	var $PasswordBackup;
+	var $RoomId;
+	var $RoomIsPermitted;
-	var $StatusId;
-	var $Session;
-	var $SessionPong;
*	var $SessionAddress;
+	var $AwayMessage;
-	var $AwayTime;
-	var $BannedTill;
+	var $BanReason;
+	var $BannedBy;

*/		
	}

	function ClearSession() {
		$this->RoomId = -1;
		$this->Session = "";
		$this->SessionPong = "";
		$this->SessionCheck = "";
		$this->SessionAddress = "";
	}

	function CheckAmnesty() {
		if (!$this->IsEmpty() && $this->IsBanned() && $this->BannedTill) {
			if (time() > strtotime($this->BannedTill)) {
				$this->StopBan();

				//Remove scheduled unbans
				$task = new ScheduledTask();
				$task->DeleteUserUnbans($this->Id);

			 	// Write log
			 	LogBanEnd($this->Id, ScheduledTask::SCHEDULER_LOGIN);
				return true;
			}
		}
		return false;
	}

	function BackFromAway() {
		$this->AwayMessage = "";
		$this->AwayTime = "";
	}
	
	function CreateSession() {
		$this->Session = MakeGuid($this->sessionKeyLength);
		$this->SessionCheck = MakeGuid($this->sessionKeyLength);

		$this->SessionAddress = GetRequestAddress();
		$this->TouchSession();
		$this->BackFromAway();

		// Clear kick messages upon (re)logging.
		$this->KickMessages = "";
	}

	function TouchSession() {
		if ($this->Session) {
			$this->SessionPong = NowDateTime();
		}
	}

	function GoOffline() {
		$this->ClearSession();
		$this->BackFromAway();
		if (!$this->IsEmpty() && $this->IsConnected()) {
			$this->Save();
		}
	}

	function Kick($reason, $admin_name) {
	  global $db;
		
		if (!$this->IsConnected()) {
			return false;
		}
		$message = "<li> <b>".$admin_name."</b>".($reason ? ", &laquo;".$reason."&raquo;" : "")."\n";
		$this->KickMessages .= $message;
		$db->Query($this->KickExpression($message));
	}

	function Ban($reason, $till, $admin_id) {
		$this->BanReason = $reason;
		$this->BannedTill = $till;
		$this->BannedBy = $admin_id;
	}

	function StopBan() {
		$this->BanReason = "";
		$this->BannedBy = "";
		$this->BannedTill = "";
	}

	function IsAway() {
		return ($this->AwayTime != "" ? 1 : 0);
	}

	function IsBanned() {
		return !IdIsNull($this->BannedBy);
	}

	function FillBanInfoFromHash($hash, $user) {
		if ($hash[BANNED]) {
			if (IdIsNull($hash[self::BANNED_BY])) {
				$this->Ban(UTF8toWin1251($hash[self::BAN_REASON]), $hash[self::BANNED_TILL], $user->Id);	// TODO: Parse date
				return true;
			}
		} else {
			if (!IdIsNull($hash[self::BANNED_BY])) {
				$this->StopBan();
				return true;
			}
		}
		return false;
	}

	function FillPasswordFromHash($hash) {
		$password = UTF8toWin1251($hash[self::PASSWORD]);
		$confirmPassword = UTF8toWin1251($hash[self::PASSWORD_CONFIRM]);
		if (!$confirmPassword || ereg("^[\*]+$", $password)) {
			return -1;
		}

		if ($password != $confirmPassword) {
			return "Введённые пароли не совпадают. Пароль не был изменён!";
		} else if (strlen($password) < 8) {
			return "Пароль короче 8 символов. Пароль не был изменён!";
		} else {
			$this->Password = $password;
			$this->PasswordBackup = $this->Password."notequal";	// To set new pwd encoded upon saving
		}
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::USER_ID);
		$this->Login = $result->Get(self::LOGIN);
		$this->Password = $result->Get(self::PASSWORD);
		$this->PasswordBackup = $this->Password;
		$this->RoomId = $result->GetNullableId(self::ROOM_ID);
		$this->RoomIsPermitted = $result->Get(self::ROOM_IS_PERMITTED);
		$this->StatusId = $result->GetNullableId(self::STATUS_ID);
		$this->Session = $result->Get(self::SESSION);
		$this->SessionPong = $result->Get(self::SESSION_PONG);
		$this->SessionCheck = $result->Get(self::SESSION_CHECK);
		$this->SessionAddress = $result->Get(self::SESSION_ADDRESS);
		$this->AwayMessage = $result->Get(self::AWAY_MESSAGE);
		$this->AwayTime = $result->Get(self::AWAY_TIME);
		$this->BanReason = $result->Get(self::BAN_REASON);
		$this->BannedTill = $result->Get(self::BANNED_TILL);
		$this->BannedBy = $result->GetNullableId(self::BANNED_BY);
		$this->KickMessages = $result->Get(self::KICK_MESSAGES);
		$this->Guid = $result->Get(self::GUID);
		$this->CheckSum = $result->Get(self::CHECK_SUM);
	}

	function GetByPassword($login, $password) {
		if ($login && $password) {
			$this->FillByCondition("t1.".self::LOGIN."='".SqlQuote($login)."' AND t1.".self::PASSWORD."='".SqlQuote(Encode($password))."'");
		} else {
			$this->Clear();
		}
	}

	function SessionIsCorrect($sessionId) {
		return $sessionId && strlen($sessionId) == $this->sessionKeyLength;
	}
	
	function GetBySession($sessionId, $sessionAddress, $sessionCheck = "") {
		if ($this->SessionIsCorrect($sessionId) && $sessionAddress) {
			$this->FillByCondition("t1.".self::SESSION."='".SqlQuote($sessionId)."' AND t1.".self::SESSION_ADDRESS."='".SqlQuote($sessionAddress)."'");
			// If not found, but sessionCheck provided,
			if ($this->IsEmpty() && $this->SessionIsCorrect($sessionCheck)) {
				// .. trying to find by two session keys
				$this->FillByCondition("t1.".self::SESSION."='".SqlQuote($sessionId)."' AND t1.".self::SESSION_CHECK."='".SqlQuote($sessionCheck)."'");
				// If match found and sessionAddress is not empty
				if (!$this->IsEmpty() && $sessionAddress) {
					// .. updating session address
					$this->SessionAddress = $sessionAddress;
					$this->GetByCondition("", $this->UpdateSessionAddressExpression());
				}
			}
		} else {
			$this->Clear();
		}
	}

	function GetUserName($id, $expression) {
		if (!$id) {
			$id = $this->Id;
		}
		$q = $this->GetByCondition("t1.".self::USER_ID."=".$id, $expression);
		if ($q && $q->NumRows() > 0) {
			$q->NextResult();
			return $q->Get(self::LOGIN);
		}
		return "";
	}

	function GetUserLogin($id = 0) {
		return $this->GetUserName($id, $this->GetUserLoginExpression());
	}

	function GetUserCurrentName($id = 0) {
		return $this->GetUserName($id, $this->GetUserActualNameExpression());
	}

	function UserDoesExist($id) {
	  global $db;
		if ($id && $this->IsConnected()) {
			$q = $db->Query("SELECT 1 FROM ".$this->table." WHERE ".self::USER_ID."=".SqlQuote($id)." LIMIT 1");
			return $q->NumRows() > 0;
		}
	}

	function __tostring() {
		$s = "<ul type=square><li>".self::USER_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::LOGIN.": ".$this->Login."</li>\n";
		$s.= "<li>".self::PASSWORD.": ".($this->Password ? "********" : "not set")."</li>\n";
		$s.= "<li>".self::ROOM_ID.": ".$this->RoomId."</li>\n";
		$s.= "<li>".self::ROOM_IS_PERMITTED.": ".$this->RoomIsPermitted."</li>\n";
		$s.= "<li>".self::STATUS_ID.": ".$this->StatusId."</li>\n";
		$s.= "<li>".self::SESSION.": ".$this->Session."</li>\n";
		$s.= "<li>".self::SESSION_PONG.": ".$this->SessionPong."</li>\n";
		$s.= "<li>".self::SESSION_CHECK.": ".$this->SessionCheck."</li>\n";
		$s.= "<li>".self::SESSION_ADDRESS.": ".$this->SessionAddress."</li>\n";
		$s.= "<li>".self::AWAY_MESSAGE.": ".$this->AwayMessage."</li>\n";
		$s.= "<li>".self::AWAY_TIME.": ".$this->AwayTime."</li>\n";
		$s.= "<li>".self::BAN_REASON.": ".$this->BanReason."</li>\n";
		$s.= "<li>".self::BANNED_TILL.": ".$this->BannedTill."</li>\n";
		$s.= "<li>".self::BANNED_BY.": ".$this->BannedBy."</li>\n";
		$s.= "<li>".self::KICK_MESSAGES.": ".$this->KickMessages."</li>\n";
		$s.= "<li>".self::GUID.": ".$this->Guid."</li>\n";
		$s.= "<li>Checksum: ".$this->CheckSum()."\n";
		if ($this->IsEmpty()) {
			$s .= "<li> <b>User is unsaved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	function ToJsAdminFields() {
		$s = "[\"".
self::SESSION_ADDRESS."\",\"".
self::STATUS_ID."\",\"".
self::BANNED_BY."\",\"".
self::BAN_REASON."\",\"".
self::BANNED_TILL."\",\"ADMIN\"]";
		return $s;
	}

	function ToJs() {
		$admin = "";
		if ($this->BannedBy) {
			$admin = $this->GetUserLogin($this->BannedBy);
		}
		$s = "[\"".
JsQuote($this->SessionAddress)."\",\"".
JsQuote($this->StatusId)."\",\"".
JsQuote($this->BannedBy)."\",\"".
($this->IsAway() ? " ".JsQuote($this->BanReason) : "")."\",\"".
JsQuote($this->BannedTill)."\",\"".
JsQuote($admin)."\"]";
		return $s;
	}

	function InfoLink($id, $text) {
		return "<a href=\"javascript:void(0)\" onclick=\"Info(".round($id).")\">".$text."</a>";
	}

	function ToInfoLink($text = "") {
		return $this->InfoLink($this->Id, $text ? $text : $this->Login);
	}

	function BannedInfo($admin = "") {
		if (!$this->IsBanned()) {
			return;
		}
		return ($this->BanReason ? "Формулировка - &laquo;".$this->BanReason."&raquo;. " : "").($this->BannedTill ? "Бан до ".PrintableShortDate($this->BannedTill) : "Бессрочный бан").".".($admin ? " Админ - ".$this->InfoLink($this->BannedBy, $admin)."." : "");
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::USER_ID.",
	t1.".self::LOGIN.",
	t1.".self::PASSWORD.",
	t1.".self::ROOM_ID.",
	(".$this->UserRoomAccessExpression().") AS ".self::ROOM_IS_PERMITTED.",
	t1.".self::STATUS_ID.",
	t1.".self::SESSION.",
	t1.".self::SESSION_PONG.",
	t1.".self::SESSION_CHECK.",
	t1.".self::SESSION_ADDRESS.",
	t1.".self::AWAY_MESSAGE.",
	t1.".self::AWAY_TIME.",
	t1.".self::BANNED_TILL.",
	t1.".self::BAN_REASON.",
	t1.".self::BANNED_BY.",
	t1.".self::KICK_MESSAGES.",
	t1.".self::GUID."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
	(".self::LOGIN.", 
".self::PASSWORD.", 
".self::ROOM_ID.", 
".self::STATUS_ID.", 
".self::SESSION.", 
".self::SESSION_PONG.", 
".self::SESSION_CHECK.", 
".self::SESSION_ADDRESS.", 
".self::AWAY_MESSAGE.", 
".self::AWAY_TIME.",
".self::BANNED_TILL.",
".self::BAN_REASON.",
".self::BANNED_BY.",
".self::KICK_MESSAGES.",
".self::GUID."
)
VALUES
(
'".SqlQuote($this->Login)."', 
'".SqlQuote(Encode($this->Password))."', 
".NullableId($this->RoomId).", 
'".SqlQuote($this->StatusId)."', 
'".SqlQuote($this->Session)."', 
".Nullable(SqlQuote($this->SessionPong)).", 
'".SqlQuote($this->SessionCheck)."', 
'".SqlQuote($this->SessionAddress)."', 
'".SqlQuote($this->AwayMessage)."', 
".Nullable(SqlQuote($this->AwayTime)).",
".Nullable(SqlQuote($this->BannedTill)).",
".Nullable(SqlQuote($this->BanReason)).",
".NullableId($this->BannedBy).",
'".SqlQuote($this->KickMessages)."',
'".SqlQuote($this->Guid)."'
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
	".self::LOGIN."='".SqlQuote($this->Login)."',";
		if ($this->Password != $this->PasswordBackup) {
			$result .= self::PASSWORD."='".SqlQuote(Encode($this->Password))."',";
		}
		$result .= "
	".self::ROOM_ID."=".NullableId($this->RoomId).",
	".self::STATUS_ID."='".$this->StatusId."',
	".self::SESSION."='".SqlQuote($this->Session)."',
	".self::SESSION_PONG."=".Nullable(SqlQuote($this->SessionPong)).", 
	".self::SESSION_CHECK."=".Nullable(SqlQuote($this->SessionCheck)).", 
	".self::SESSION_ADDRESS."='".SqlQuote($this->SessionAddress)."', 
	".self::AWAY_MESSAGE."='".SqlQuote($this->AwayMessage)."', 
	".self::AWAY_TIME."=".Nullable(SqlQuote($this->AwayTime)).",
	".self::BANNED_TILL."=".Nullable(SqlQuote($this->BannedTill)).",
	".self::BAN_REASON."=".Nullable($this->BanReason).",
	".self::BANNED_BY."=".NullableId($this->BannedBy).",
	".self::KICK_MESSAGES."='".SqlQuote($this->KickMessages)."',
	".self::GUID."='".SqlQuote($this->Guid)."'
WHERE 
	".self::USER_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function UpdateSessionAddressExpression() {
		$result = "UPDATE ".$this->table." SET 
	".self::SESSION_ADDRESS."='".SqlQuote($this->SessionAddress)."'
WHERE 
	".self::USER_ID."=".SqlQuote($this->Id);
		return $result;
	}

	
	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::USER_ID."=".SqlQuote($this->Id);
	}

	function PongExpression($doClear) {
		return "UPDATE ".$this->table." SET ".self::SESSION_PONG."='".($doClear ? "" : date())."' WHERE ".self::USER_ID."=".$this->Id;
	}

	function KickExpression($message) {
		return "UPDATE ".$this->table." SET ".self::KICK_MESSAGES."=CONCAT(".self::KICK_MESSAGES.", '".SqlQuote($message)."') WHERE ".self::USER_ID."=".$this->Id;
	}

	function ExpireCondition() {
	  global $SessionLifetime;

		return "t1.".self::SESSION_PONG." IS NOT NULL AND t1.".self::SESSION_PONG."<'".DateFromTime(mktime()-$SessionLifetime)."'";
	}
	
	function GetExpiredUsers() {
	  global $SessionLifetime;

		return $this->GetByCondition(
			$this->ExpireCondition(), 
			$this->GetUserActualNameExpression());
	}

	function GetUserActualNameExpression() {
		return "SELECT 
	t1.".self::USER_ID.",
	COALESCE(t2.".Nickname::TITLE.",t1.".self::LOGIN.") AS ".self::LOGIN.", 
	t1.".self::ROOM_ID." 
FROM ".$this->table." t1
	LEFT JOIN ".Nickname::table." t2 ON (t2.".Nickname::USER_ID."=t1.".self::USER_ID." AND t2.".Nickname::IS_SELECTED."=1)
WHERE
	##CONDITION##";
	}

	function GetUserLoginExpression() {
		return "SELECT 
	t1.".self::LOGIN."
FROM ".$this->table." t1
WHERE
	##CONDITION##";
	}

	function FindUserExpression() {
		return "SELECT 
	t1.".self::USER_ID.",
	t1.".self::LOGIN.",
	t2.".Nickname::TITLE."
FROM 
	".$this->table." t1
	LEFT JOIN ".Nickname::table." t2 ON t2.".Nickname::USER_ID."=t1.".self::USER_ID."
	LEFT JOIN ".Profile::table." t3 ON t3.".Profile::USER_ID."=t1.".self::USER_ID."
WHERE
	##CONDITION##
GROUP BY t1.".self::USER_ID."
ORDER BY ".self::LOGIN." ASC";
	}

	function FindUserWithJournalsExpression() {
		return "SELECT 
	t1.".self::USER_ID.",
	t1.".self::LOGIN.",
	t2.".Nickname::TITLE.",
	t3.".Forum::FORUM_ID.",
	t3.".Forum::TITLE."
FROM 
	".$this->table." t1
	LEFT JOIN ".Nickname::table." t2 ON t2.".Nickname::USER_ID."=t1.".self::USER_ID."
	LEFT JOIN ".Forum::table." t3 ON t3.".Forum::LINKED_ID."=t1.".self::USER_ID." AND t3.".Forum::TYPE."='".Forum::TYPE_JOURNAL."'
WHERE
	##CONDITION##
GROUP BY t1.".self::USER_ID."
ORDER BY t1.".self::LOGIN." ASC, t3.".Forum::TITLE." ASC";
	}

	function UserRoomAccessExpression() {
		return "SELECT COUNT(*) FROM ".RoomUser::table." AS t0 
WHERE 
	t0.".RoomUser::USER_ID."=t1.".self::USER_ID." AND 
	t0.".RoomUser::ROOM_ID."=t1.".self::ROOM_ID." LIMIT 1";
	}

	function GetOnlineUsersExpression() {
		return "SELECT
t1.".self::USER_ID.",
COALESCE(t2.".Nickname::TITLE.",t1.".self::LOGIN.") AS ".self::LOGIN.",
t1.".self::ROOM_ID.",
t1.".self::SESSION_PONG."
	FROM ".self::table." AS t1
	LEFT JOIN ".Nickname::table." AS t2 ON t2.".Nickname::USER_ID."=t1.".self::USER_ID." AND t2.".Nickname::IS_SELECTED."=1
WHERE
	t1.".self::SESSION_PONG." IS NOT NULL AND
	t1.".self::ROOM_ID." IS NOT NULL
ORDER BY
	".self::ROOM_ID;
	}

	function BirthdaysExpression() {
		return "SELECT
t1.".self::USER_ID.",
t1.".self::LOGIN.",
t2.".Profile::BIRTHDAY."
	FROM ".self::table." AS t1
	LEFT JOIN ".Profile::table." AS t2 ON t2.".Profile::USER_ID."=t1.".self::USER_ID."
WHERE
	##CONDITION##
ORDER BY DAY(".Profile::BIRTHDAY.") ASC, ".Profile::BIRTHDAY." ASC";
	}

	function PhotosExpression() {
		return "SELECT
t1.".self::USER_ID.",
t1.".self::LOGIN.",
t2.".Profile::PHOTO."
	FROM ".self::table." AS t1
	LEFT JOIN ".Profile::table." AS t2 ON t2.".Profile::USER_ID."=t1.".self::USER_ID."
	WHERE 
		t2.".Profile::PHOTO." IS NOT NULL AND 
		t2.".Profile::PHOTO_UPLOAD_DATE." IS NOT NULL
	ORDER BY ".Profile::PHOTO_UPLOAD_DATE." DESC 
	LIMIT 10";
	}

	function WithProfileExpression() {
		return "SELECT
t1.".self::USER_ID.",
t1.".self::LOGIN."
	FROM ".self::table." AS t1
	LEFT JOIN ".Profile::table." AS t2 ON t2.".Profile::USER_ID."=t1.".self::USER_ID."
	WHERE 
		##CONDITION##";
	}

	function BannedExpression() {
		return "SELECT 
	t1.".self::USER_ID.",
	t1.".self::LOGIN.",
	t1.".self::BANNED_TILL.",
	t1.".self::BAN_REASON.",
	t1.".self::BANNED_BY.",
	t2.".self::LOGIN." AS ADMIN
FROM 
	".$this->table." AS t1 
	LEFT JOIN ".$this->table." AS t2 ON t2.".User::USER_ID."=t1.".User::BANNED_BY."
WHERE
	##CONDITION##";
	}
}

?>