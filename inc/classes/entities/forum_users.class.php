<?

class ForumUser extends EntityBase {
	// Constants
	const table = "forum_users";

	const FORUM_ID = "FORUM_ID";
	const USER_ID = "USER_ID";
	const ACCESS = "ACCESS";

	// Properties
	var $ForumId;
	var $UserId;
	var $Access;

	function ForumUser($userId = -1, $forumId = -1) {
		$this->table = self::table;
		$this->Clear();

		$this->ForumId = $forumId;
		$this->UserId = $userId;
	}

	function Clear() {
		$this->UserId = -1;
		$this->ForumId = -1;
		$this->Access = 0;
		
		$this->Login = "";
	}

	function IsFull() {
		return $this->UserId > 0 && $this->ForumId > 0;
	}

	// Specifies if current access is moderatorial
	function IsModerator() {
		return $this->IsFull() && $this->Access == ForumBase::FULL_ACCESS;
	}

	function FillFromResult($result) {
		$this->UserId = $result->Get(self::USER_ID);
		$this->ForumId = $result->Get(self::FORUM_ID);
		$this->Access = $result->Get(self::ACCESS);

		$this->Login = $result->Get(User::LOGIN);
	}
	
	function FillByForumId($forumId) {
		return $this->FillByCondition("t1.".self::FORUM_ID."=".round($forumId));
	}

	function GetByUserId($userId) {
		return $this->FillByCondition("t1.".self::USER_ID."=".round($userId));
	}

	function GetByForumId($forumId) {
		return $this->GetByCondition("t1.".self::FORUM_ID."=".round($forumId));
	}

	function GetFor($userId, $forumId) {
		$this->FillByCondition("t1.".self::USER_ID."=".round($userId)." AND t1.".self::FORUM_ID."=".round($forumId)." LIMIT 1");
	}

	function GetUserForums($userId) {
		return $this->GetByCondition("1", $this->UserForumsExpression($userId));
	}

	function ToJs() {
		return "new fadto(\"".
JsQuote($this->ForumId)."\",\"".
JsQuote($this->UserId)."\",\"".
JsQuote($this->Login)."\",".
JsQuote($this->Access).")";
	}

	function Save() {
	 global $db;
		if ($this->IsConnected() && $this->IsFull()) {
			// Check duplicates
			$q = $db->Query("SELECT 
   ".self::USER_ID."
FROM 
  ".$this->table."
WHERE
  ".self::USER_ID." = ".round($this->UserId)." AND
  ".self::FORUM_ID." = ".round($this->ForumId)." LIMIT 1");

			if ($q->NumRows()) {
				return false;
			}

			$q = $db->Query($this->CreateExpression());
			$this->Id = $q->GetLastId();
			return true;
		}
	}

	function Delete() {
		if ($this->IsFull()) {
			$this->GetByCondition("", $this->DeleteExpression());
			$this->Clear();
			return true;
		}
		return false;
	}

	function DeleteForForum($forum_id) {
		return $this->GetByCondition("", $this->DeleteForForumExpression($forum_id));
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
		$s.= "<li>".self::FORUM_ID.": ".$this->ForumId."</li>\n";
		$s.= "<li>".self::ACCESS.": ".$this->Access."</li>\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>ForumUser is not saved!</b>";
		}
		$s.= "</ul>";
		return $s;
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::USER_ID.",
	t1.".self::FORUM_ID.",
	t1.".self::ACCESS.",
	t2.".User::LOGIN."
FROM
	".$this->table." AS t1 
	LEFT JOIN ".User::table." AS t2 ON t2.".User::USER_ID."=t1.".self::USER_ID."
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::USER_ID.", 
".self::FORUM_ID.",
".self::ACCESS."
)
VALUES
(".round($this->UserId).", 
".round($this->ForumId).",
".round($this->Access)."
)";
	}

	function UpdateExpression() {
		return "UPDATE ".$this->table." SET
	".self::USER_ID."=".round($this->UserId).", 
	".self::FORUM_ID."=".round($this->ForumId).",
	".self::ACCESS."=".round($this->Access)."
WHERE ##CONDITION##";
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table."
WHERE
	".self::USER_ID."=".SqlQuote($this->UserId)." AND
	".self::FORUM_ID."=".SqlQuote($this->ForumId);
	}

	function DeleteForForumExpression($forumId) {
		return "DELETE FROM ".$this->table."
WHERE
	".self::FORUM_ID."=".round($forumId);
	}

	// Expression to get user forums
	function UserForumsExpression($userId) {
		$user_id = round($userId);

		return "SELECT DISTINCT
	t2.".Forum::FORUM_ID.",
	t2.".Forum::TITLE.",
	t2.".Forum::TYPE.",
	t1.".self::ACCESS.",
	t3.".User::LOGIN."
FROM
	".Forum::table." AS t2
	LEFT JOIN ".self::table." AS t1 ON t2.".Forum::FORUM_ID."=t1.".self::FORUM_ID."
	LEFT JOIN ".User::table." AS t3 ON t3.".User::USER_ID."=t2.".Forum::LINKED_ID."
WHERE 
	(t2.".Forum::LINKED_ID."=".$userId.") OR 
	(t1.".self::USER_ID."=".$userId." AND (t1.".self::ACCESS."=".Forum::FULL_ACCESS." OR 
	t1.".self::ACCESS."=".Forum::READ_ADD_ACCESS."))
ORDER BY t2.".Forum::TYPE.", t2.".Forum::FORUM_ID;
	}
}

?>
                                       	