<?php

class JournalForbiddenCommenter extends EntityBase {
	// Constants
	const table = "journal_forbidden_commenters";

	const USER_ID = "USER_ID";
	const COMMENTER_ID = "COMMENTER_ID";

	// Properties
	var $UserId;
	var $CommenterId;

	function JournalForbiddenCommenter($user_id = -1, $commenter_id = -1) {
		$this->table = self::table;
		parent::__construct($user_id, self::USER_ID);

		$this->UserId = $user_id;
		$this->CommenterId = $commenter_id;
	}

	function Clear() {
		$this->UserId = -1;
		$this->CommenterId = -1;
	}

	function IsFull() {
		return $this->UserId > 0 && $this->CommenterId > 0;
	}

	function FillFromResult($result) {
		$this->UserId = $result->Get(self::USER_ID);
		$this->CommenterId = $result->Get(self::COMMENTER_ID);
	}

	function GetByUserId($userId, $onlyMine = false) {
		return $this->GetByCondition(
			"t1.".self::USER_ID."=".SqlQuote($userId).($onlyMine ? "" : " OR t1.".self::COMMENTER_ID."=".SqlQuote($userId))." ORDER BY t2.".User::LOGIN, 
			$this->ReadWithLoginExpression()
		);
	}

	function ToJs($login) {
		return "new jfсdto(".$this->CommenterId.",\"".JsQuote($login)."\")";
	}


	
	function Save($by_query = "") {
	 global $db;
		if ($this->IsConnected() && $this->IsFull() && $this->UserId != $this->CommenterId) {
			// Check duplicates
			$q = $db->Query("SELECT 
   ".self::COMMENTER_ID."
FROM 
  ".$this->table."
WHERE
  ".self::USER_ID." = '".SqlQuote($this->UserId)."' AND
  ".self::COMMENTER_ID." = '".SqlQuote($this->CommenterId)."' LIMIT 1");

			if ($q->NumRows()) {
				return false;
			}

			$q = $db->Query($this->CreateExpression());
			$this->Id = $q->GetLastId();
			return true;
		}
	}

	function Delete() {
	 global $db;

		if ($this->IsConnected() && $this->IsFull()) {
			$q = $db->Query($this->DeleteExpression());
			$this->Clear();
			return true;
		}
		return false;
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
		$s.= "<li>".self::COMMENTER_ID.": ".$this->CommenterId."</li>\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Journal forbidden commenter is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	// SQL

	function ReadExpression() {
		return "SELECT 
	t1.".self::USER_ID.",
	t1.".self::COMMENTER_ID."
FROM
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function ReadWithLoginExpression() {
		return "SELECT 
	t1.".self::COMMENTER_ID.",
	t2.".User::LOGIN."
FROM
	".$this->table." AS t1 
	LEFT JOIN ".User::table." AS t2 ON t2.".User::USER_ID."=t1.".self::COMMENTER_ID."
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::USER_ID.", 
".self::COMMENTER_ID."
)
VALUES
('".SqlQuote($this->UserId)."', 
'".SqlQuote($this->CommenterId)."'
)";
	}

	function UpdateExpression() {
		return "";
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table."
WHERE
	".self::USER_ID."=".SqlQuote($this->UserId)." AND
	".self::COMMENTER_ID."=".SqlQuote($this->CommenterId);
	}
}

?>