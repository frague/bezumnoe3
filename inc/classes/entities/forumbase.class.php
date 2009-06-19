<?

class ForumBase extends EntityBase {
	// Constants
	const table = "forums";

	const FORUM_ID = "FORUM_ID";
	const TYPE = "TYPE";
	const TITLE = "TITLE";
	const DESCRIPTION = "DESCRIPTION";
	const IS_PROTECTED = "IS_PROTECTED";
	const LINKED_ID = "LINKED_ID";
	const TOTAL_COUNT = "TOTAL_COUNT";

	const UNREAD_COUNT = "UNREAD_COUNT";

	const ID_PARAM = "id";

	const TYPE_FORUM	= "forum";
	const TYPE_JOURNAL	= "journal";
	const TYPE_GALLERY	= "gallery";

	// Type access
	const NO_ACCESS			= 0;
	const READ_ONLY_ACCESS	= 1;
	const READ_ADD_ACCESS	= 2;
	const FULL_ACCESS		= 3;

	// Properties
	var $Type;
	var $Title;
	var $Description;
	var $IsProtected;
	var $LinkedId;
	var $TotalCount;

	// Fields
	function ForumBase($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::FORUM_ID);
	}

	function IsJournal() {
		return $this->Type == self::TYPE_JOURNAL;
	}

	function Clear() {
		$this->Id			= -1;
		$this->Title		= "";
		$this->Type			= "";
		$this->Description	= "";
		$this->IsProtected	= 0;
		$this->LinkedId	= -1;
		$this->TotalCount	= 0;
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::FORUM_ID);
		$this->Type = $result->Get(self::TYPE);
		$this->Title = $result->Get(self::TITLE);
		$this->Description = $result->Get(self::DESCRIPTION);
		$this->IsProtected = $result->Get(self::IS_PROTECTED);
		$this->LinkedId = $result->Get(self::LINKED_ID);
		$this->TotalCount = $result->Get(self::TOTAL_COUNT);
	}

	function FillFromHash($hash) {
		$this->Title = $hash[self::TITLE];
		$this->Description = $hash[self::DESCRIPTION];
		$this->IsProtected = $result->Get(self::IS_PROTECTED);
		$this->TotalCount = $result->Get(self::TOTAL_COUNT);
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::FORUM_ID." = ".$this->Id."</li>\n";
		$s.= "<li>".self::TYPE." = ".$this->Type."</li>\n";
		$s.= "<li>".self::TITLE." = ".$this->Title."</li>\n";
		$s.= "<li>".self::DESCRIPTION." = ".$this->Description."</li>\n";
		$s.= "<li>".self::IS_PROTECTED." = ".$this->IsProtected."</li>\n";
		$s.= "<li>".self::LINKED_ID." = ".$this->LinkedId."</li>\n";
		$s.= "<li>".self::TOTAL_COUNT." = ".$this->TotalCount."</li>\n";

		if ($this->IsEmpty()) {
			$s.= "<li> <b>Forum is not saved!</b>";
		}
		$s.= "</ul>";
		return $s;
	}

	function GetUnreadCount($visitDate) {
	  global $db;

		if (!$this->IsConnected()) {
			return 0;
		}

		$q = $db->Query($this->NewRecordsExpression($visitDate));
		$q->NextResult();
		return $q->Get(self::UNREAD_COUNT);
	}

	// Gets forum(s) by condition
	// joined with access data for given user
	function GetByConditionWithUserAccess($condition, $userId) {
		$user_id = round($user_id);
		if (!$userId) {
			return $this->GetByCondition($condition);
		}

		$expression = str_replace("FROM", 
",
t2.".ForumUser::USER_ID.",
t2.".ForumUser::IS_MODERATOR."
FROM", 
$this->ReadExpression());
		$expression = str_replace(
"WHERE", 
"	LEFT JOIN ".ForumUser::table." AS t2 ON t2.".ForumUser::USER_ID."=".$userId." AND t2.".ForumUser::FORUM_ID."=t1.".self::FORUM_ID."
WHERE", 
$expression);
		return $this->GetByCondition($condition, $expression);
	}

	// Gets forum access for given user or anonymous one
	function GetAccess($userId = 0) {
		$userId = round($userId);
		if ($this->IsEmpty()) {
			return self::NO_ACCESS;
		}

		if ($userId == 0) {
			if (!$this->IsProtected) {
				return self::READ_ONLY_ACCESS;
			}
			return self::NO_ACCESS;
		} else {
			if ($this->LinkedId == $userId) {
		   		return self::FULL_ACCESS;
			}
			$forumUser = new ForumUser();
			$forumUser->GetFor($userId, $this->Id);
			return $this->LoggedUsersAccess($forumUser);
		}
	}

	// Returns access level for logged user with given
	// relation to forum
	function LoggedUsersAccess($forumUser) {
		if ($forumUser->IsFull()) {
			if ($forumUser->IsModerator) {
				return self::FULL_ACCESS;
			} else {
				return self::READ_ADD_ACCESS;
			}
		} else {
			if ($this->IsProtected) {
				return self::NO_ACCESS;
			} else {
				return self::READ_ADD_ACCESS;
			}
		}
	}

	/*********** SQL ***********/

	function DeleteForumRecords() {
	// Could be overridden for different types of forums
	  global $db;

		$db->Query(str_replace("##CONDITION##", ForumRecord::FORUM_ID."=".round($this->Id), $record->DeleteThreadExpression()));
	}

	function Delete() {
		$result = false;
		if (!$this->IsEmpty()) {
		    $record = new ForumRecord();		// ForumRecordBase
		    $this->DeleteForumRecords();
			$result = $this->DeleteById($this->Id);
		}
		return $result;
	}
	
	function SaveAndCount() {
	  global $db;

		if (!$this->IsConnected()) {
			return false;
		}
		$this->Save();
		$db->Query($this->UpdateThreadsCountExpression());
	}

	// SQL Expressions
	function ReadExpression() {
		return "SELECT 
	t1.".self::FORUM_ID.",
	t1.".self::TYPE.",
	t1.".self::TITLE.",
	t1.".self::DESCRIPTION.",
	t1.".self::IS_PROTECTED.",
	t1.".self::LINKED_ID.",
	t1.".self::TOTAL_COUNT."
FROM 
	".$this->table." AS t1 
WHERE 
	".($this->Type ? "t1.".self::TYPE."='".$this->Type."' AND " : "")."
	(##CONDITION##)";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::TYPE.",
".self::TITLE.",
".self::DESCRIPTION.",
".self::IS_PROTECTED.",
".self::LINKED_ID.",
".self::TOTAL_COUNT."
)
VALUES
('".SqlQuote($this->Type)."',
'".SqlQuote($this->Title)."',
'".SqlQuote($this->Description)."',
".Boolean($this->IsProtected).",
".NullableId($this->LinkedId).",
".round($this->TotalCount).")";
	}
	
	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::TYPE."='".SqlQuote($this->Type)."', 
".self::TITLE."='".SqlQuote($this->Title)."', 
".self::DESCRIPTION."='".SqlQuote($this->Description)."', 
".self::IS_PROTECTED."=".Boolean($this->IsProtected).",
".self::LINKED_ID."=".NullableId($this->LinkedId).",
".self::TOTAL_COUNT."=".round($this->TotalCount)."
WHERE 
	".self::FORUM_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::FORUM_ID."=".SqlQuote($this->Id);
	}


	// !!!!!! Review
	function UpdateThreadsCountExpression() {
		if ($this->IsEmpty()) {
			return "";
		}
		return "UPDATE ".$this->table."
SET ".self::TOTAL_COUNT."=(
	SELECT 
		COUNT(".ForumRecord::RECORD_ID.") 
	FROM 
		".ForumRecord::table." 
	WHERE 
		".ForumRecord::FORUM_ID."=".$this->Id." AND
		LENGTH(".ForumRecord::INDEX.")=4 AND
		".self::IS_PROTECTED."<>1
	)
WHERE
	".self::FORUM_ID."=".$this->Id;
	}

	function NewRecordsExpression($visitDate) {
		if ($this->IsEmpty()) {
			return "";
		}
		return "SELECT COUNT(1) AS ".self::UNREAD_COUNT."
FROM ".ForumRecord::table."
WHERE 
	".ForumRecord::FORUM_ID."=".$this->Id." AND
	".ForumRecord::TYPE."='".ForumRecord::TYPE_PUBLIC."' AND
	".ForumRecord::DATE.">'".SqlQuote($visitDate)."'";
	}

	function MigrateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::FORUM_ID.",
".self::TITLE.",
".self::DESCRIPTION.",
".self::IS_PROTECTED.",
".self::LINKED_ID.",
".self::TOTAL_COUNT."
)
VALUES
(".round($this->Id).",
'".SqlQuote($this->Title)."',
'".SqlQuote($this->Description)."',
".Boolean($this->IsProtected).",
".NullableId($this->LinkedId).",
".round($this->TotalCount).")";
	}
}

?>