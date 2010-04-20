<?

class Room extends EntityBase {
	// Constants
	const table = "rooms";

	const ROOM_ID = "ROOM_ID";
	const OWNER_ID = "OWNER_ID";
	const TITLE = "TITLE";
	const TOPIC = "TOPIC";
	const TOPIC_LOCK = "TOPIC_LOCK";
	const TOPIC_AUTHOR_ID = "TOPIC_AUTHOR_ID";
	const TOPIC_AUTHOR_NAME = "TOPIC_AUTHOR_NAME";
	const IS_LOCKED = "IS_LOCKED";
	const IS_INVITATION_REQUIRED = "IS_INVITATION_REQUIRED";

	const IS_DELETED = "IS_DELETED";
	const BEEN_VISITED = "BEEN_VISITED";

	// Properties
	var $OwnerId;
	var $Title;
	var $Topic;
	var $TopicLock;
	var $TopicAuthorId;
	var $TopicAuthorName;
	var $IsLocked;
	var $IsInvitationRequired;
	var $IsDeleted;
	var $BeenVisited;

	// Fields

	function Room($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::ROOM_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->OwnerId = -1;
		$this->Title = "Новая комната";
		$this->Topic = "";
		$this->TopicLock = 0;
		$this->TopicAuthorId = 0;
		$this->TopicAuthorName = "";
		$this->IsLocked = 0;
		$this->IsInvitationRequired = 0;
		$this->IsDeleted = 0;
		$this->BeenVisited = 0;
	}

	function CheckSum($extended = false) {
		$cs = CheckSum($this->OwnerId);
		$cs+= CheckSum($this->Title);
		$cs+= CheckSum($this->Topic);
		$cs+= CheckSum($this->TopicLock);
		$cs+= CheckSum($this->TopicAuthorId);
		$cs+= CheckSum($this->IsLocked);
		$cs+= CheckSum($this->IsInvitationRequired);
		DebugLine("Room: ".$this->Id." sum: ".$cs);
		return $cs;

/*	

+	var $Title;
+	var $Topic;
+	var $TopicLock;
+	var $TopicAuthorId
+	var $IsLocked;
+	var $IsInvitationRequired;
+	var $Access;

*/
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::ROOM_ID);
		$this->OwnerId = $result->GetNullableId(self::OWNER_ID);
		$this->Title = $result->Get(self::TITLE);
		$this->Topic = $result->Get(self::TOPIC);
		$this->TopicLock = $result->Get(self::TOPIC_LOCK);
		$this->TopicAuthorId = $result->Get(self::TOPIC_AUTHOR_ID);
		$this->TopicAuthorName = $result->Get(self::TOPIC_AUTHOR_NAME);
		$this->IsLocked = $result->Get(self::IS_LOCKED);
		$this->IsInvitationRequired = $result->Get(self::IS_INVITATION_REQUIRED);
		$this->IsDeleted = $result->Get(self::IS_DELETED);
		$this->BeenVisited = $result->Get(self::BEEN_VISITED);
	}

	function FillByTitle($title) {
		return $this->FillByCondition("t1.".self::TITLE."='".SqlQuote($title)."' AND t1.".IS_DELETED."=0");
	}

	function GetAllAlive($expression = "") {
		return $this->GetByCondition("t1.".self::IS_DELETED."=0", $expression);
	}

	function DeleteEmptyRooms() {
		$q = $this->GetByCondition(
"t2.".User::USER_ID." IS NULL AND 
t1.".self::IS_LOCKED."=0 AND
t1.".self::BEEN_VISITED."=1",
$this->GetEmptyExpression());
		$e = $q->NumRows();
		if ($e) {
			$roomUser = new RoomUser();
			for ($i = 0; $i < $e; $i++) {
				$q->NextResult();
				$this->FillFromResult($q);
				if (!$this->IsEmpty()) {
					$roomUser->DeleteForRoom($this->Id);
					$this->Delete();
				}
			}
		}
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::ROOM_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::OWNER_ID.": ".$this->OwnerId."</li>\n";
		$s.= "<li>".self::TITLE.": ".$this->Title."</li>\n";
		$s.= "<li>".self::TOPIC.": ".$this->Topic."</li>\n";
		$s.= "<li>".self::TOPIC_LOCK.": ".$this->TopicLock."</li>\n";
		$s.= "<li>".self::TOPIC_AUTHOR_ID.": ".$this->TopicAuthorId."</li>\n";
		$s.= "<li>".self::TOPIC_AUTHOR_NAME.": ".$this->TopicAuthorName."</li>\n";
		$s.= "<li>".self::IS_LOCKED.": ".$this->IsLocked."</li>\n";
		$s.= "<li>".self::IS_INVITATION_REQUIRED.": ".$this->IsInvitationRequired."</li>\n";
		$s.= "<li>".self::IS_DELETED.": ".$this->IsDeleted."</li>\n";
		$s.= "<li>".self::BEEN_VISITED.": ".$this->BeenVisited."</li>\n";
		$s.= "<li>Checksum: ".$this->CheckSum()."\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Room is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	function ToPrint() {
		if ($this->IsEmpty()) {
			return "";
		}
		return "<div color='".$this->Color."'>".$this->Title."</div>";
	}

	function ToJs() {
		$topic = JsQuote(str_replace("\\\\", "<br>", $this->Topic));
		return "new Room(\"".JsQuote($this->Id)."\",\"".JsQuote($this->Title)."\",\"".$topic."\",\"".JsQuote($this->TopicLock)."\",\"".JsQuote($this->TopicAuthorId)."\",\"".JsQuote($this->TopicAuthorName)."\",".Boolean($this->IsLocked).",".Boolean($this->IsInvitationRequired).",".round($this->OwnerId).")";
	}

	function ToDTO() {
		return "new rdto(".round($this->Id).",\"".JsQuote($this->Title)."\",".Boolean($this->IsDeleted).",".Boolean($this->IsLocked).")";
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::ROOM_ID.",
	t1.".self::OWNER_ID.",
	t1.".self::TITLE.",
	t1.".self::TOPIC.",
	t1.".self::TOPIC_LOCK.",
	t1.".self::TOPIC_AUTHOR_ID.",
	COALESCE(t3.".Nickname::TITLE.", t2.".User::LOGIN.") AS ".self::TOPIC_AUTHOR_NAME.",
	t1.".self::IS_LOCKED.",
	t1.".self::IS_INVITATION_REQUIRED.",
	t1.".self::IS_DELETED.",
	t1.".self::BEEN_VISITED."
FROM 
	".$this->table." AS t1 
	LEFT JOIN ".User::table." AS t2 ON t2.".User::USER_ID."=t1.".self::TOPIC_AUTHOR_ID." AND t1.".self::TOPIC_AUTHOR_ID.">0
	LEFT JOIN ".Nickname::table." AS t3 ON t3.".Nickname::USER_ID."=t1.".self::TOPIC_AUTHOR_ID." AND t3.".Nickname::IS_SELECTED."=1 AND t1.".self::TOPIC_AUTHOR_ID.">0
WHERE
	##CONDITION##";
	}

	function ListRoomsExpression() {
		return "SELECT DISTINCT
	t1.".self::ROOM_ID.",
	t1.".self::TITLE.",
	t1.".self::IS_DELETED.",
	t1.".self::IS_LOCKED."
FROM 
	".$this->table." AS t1
WHERE
	##CONDITION##
ORDER BY 
	t1.".self::IS_DELETED." ASC, 
	t1.".self::TITLE." ASC";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::OWNER_ID.", 
".self::TITLE.", 
".self::TOPIC.", 
".self::TOPIC_LOCK.", 
".self::TOPIC_AUTHOR_ID.", 
".self::IS_LOCKED.", 
".self::IS_INVITATION_REQUIRED.", 
".self::IS_DELETED.", 
".self::BEEN_VISITED."
)
VALUES
(".NullableId($this->OwnerId).", 
'".SqlQuote($this->Title)."', 
'".SqlQuote($this->Topic)."', 
'".SqlQuote($this->TopicLock)."', 
'".round($this->TopicAuthorId)."', 
'".SqlQuote($this->IsLocked)."', 
'".SqlQuote($this->IsInvitationRequired)."', 
".Boolean($this->IsDeleted).", 
".Boolean($this->BeenVisited)."
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::OWNER_ID."=".NullableId($this->OwnerId).", 
".self::TITLE."='".SqlQuote($this->Title)."', 
".self::TOPIC."='".SqlQuote($this->Topic)."', 
".self::TOPIC_LOCK."='".SqlQuote($this->TopicLock)."', 
".self::TOPIC_AUTHOR_ID."='".round($this->TopicAuthorId)."', 
".self::IS_LOCKED."='".SqlQuote($this->IsLocked)."', 
".self::IS_INVITATION_REQUIRED."='".SqlQuote($this->IsInvitationRequired)."', 
".self::IS_DELETED."=".Boolean($this->IsDeleted).", 
".self::BEEN_VISITED."=".Boolean($this->BeenVisited)."
WHERE 
	".self::ROOM_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "UPDATE ".$this->table." SET ".self::IS_DELETED."=1 WHERE ".self::ROOM_ID."=".SqlQuote($this->Id);
	}

	function GetEmptyExpression() {
		return "SELECT 
	t1.".self::ROOM_ID."
FROM 
	".$this->table." AS t1 
	LEFT JOIN ".User::table." AS t2 ON t2.".User::ROOM_ID."=t1.".self::ROOM_ID."
WHERE 
	##CONDITION##";
	}

	function GetTitleExpression() {
		return "SELECT 
	t1.".self::ROOM_ID.",
	t1.".self::TITLE."
FROM
	".$this->table." AS t1
WHERE
	##CONDITION##";
	}
}


?>