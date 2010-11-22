<?

class Permissions extends EntityBase {
	// Constants
	const table = "permissions";

	const USER_ID = "USER_ID";
	const POSTING = "POSTING";
	const BOLD = "BOLD";
	const ITALIC = "ITALIC";
	const UNDERLINE = "UNDERLINE";
	const TOPIC = "TOPIC";
	const TOPIC_LOCK = "TOPIC_LOCK";
	const KICK = "KICK";
	const BAN = "BAN";
	const TREE = "TREE";
	const BOTS = "BOTS";
	const ADMIN = "ADMIN";
	const SUPERADMIN = "SUPERADMIN";

	// Properties
	var $Posting;
	var $Bold;
	var $Italic;
	var $Underline;
	var $Topic;
	var $TopicLock;
	var $Kick;
	var $Ban;
	var $Tree;
	var $Bots;
	var $Admin;
	var $SuperAdmin;

	// Fields

	function Permissions($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::USER_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->Posting = 1;
		$this->Bold = 0;
		$this->Italic = 0;
		$this->Underline = 0;
		$this->Topic = 0;
		$this->TopicLock = 0;
		$this->Kick = 0;
		$this->Ban = 0;
		$this->Tree = 0;
		$this->Bots = 0;
		$this->Admin = 0;
		$this->SuperAdmin = 0;
	}

	function CheckSum($extended = false) {
		$cs = CheckSum($this->Posting);
		$cs+= CheckSum($this->Bold);
		$cs+= CheckSum($this->Italic);
		$cs+= CheckSum($this->Underline);
		$cs+= CheckSum($this->Topic);
		$cs+= CheckSum($this->TopicLock);
		$cs+= CheckSum($this->Kick);
		$cs+= CheckSum($this->Ban);
		$cs+= CheckSum($this->Tree);
		$cs+= CheckSum($this->Bots);
		$cs+= CheckSum($this->Admin);
		$cs+= CheckSum($this->SuperAdmin);
		DebugLine("Permissions: ".$this->Id." sum: ".$cs);
		return $cs;
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::USER_ID);
		$this->Posting = Boolean($result->Get(self::POSTING));
		$this->Bold = Boolean($result->Get(self::BOLD));
		$this->Italic = Boolean($result->Get(self::ITALIC));
		$this->Underline = Boolean($result->Get(self::UNDERLINE));
		$this->Topic = Boolean($result->Get(self::TOPIC));
		$this->TopicLock = Boolean($result->Get(self::TOPIC_LOCK));
		$this->Kick = Boolean($result->Get(self::KICK));
		$this->Ban = Boolean($result->Get(self::BAN));
		$this->Tree = Boolean($result->Get(self::TREE));
		$this->Bots = Boolean($result->Get(self::BOTS));
		$this->Admin = Boolean($result->Get(self::ADMIN));
		$this->SuperAdmin = Boolean($result->Get(self::SUPER_ADMIN));
	}

	function FillFromHash($hash) {
		$this->Posting = Boolean($hash[self::POSTING]);
		$this->Bold = Boolean($hash[self::BOLD]);
		$this->Italic = Boolean($hash[self::ITALIC]);
		$this->Underline = Boolean($hash[self::UNDERLINE]);
		$this->Topic = Boolean($hash[self::TOPIC]);
		$this->TopicLock = Boolean($hash[self::TOPIC_LOCK]);
		$this->Kick = Boolean($hash[self::KICK]);
		$this->Ban = Boolean($hash[self::BAN]);
		$this->Tree = Boolean($hash[self::TREE]);
		$this->Bots = Boolean($hash[self::BOTS]);
		$this->Admin = Boolean($hash[self::ADMIN]);
		$this->SuperAdmin = Boolean($hash[self::SUPER_ADMIN]);
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::USER_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::POSTING.": ".$this->Posting."</li>\n";
		$s.= "<li>".self::BOLD.": ".$this->Bold."</li>\n";
		$s.= "<li>".self::ITALIC.": ".$this->Italic."</li>\n";
		$s.= "<li>".self::UNDERLINE.": ".$this->Underline."</li>\n";
		$s.= "<li>".self::TOPIC.": ".$this->Topic."</li>\n";
		$s.= "<li>".self::TOPIC_LOCK.": ".$this->TopicLock."</li>\n";
		$s.= "<li>".self::KICK.": ".$this->Kick."</li>\n";
		$s.= "<li>".self::BAN.": ".$this->Ban."</li>\n";
		$s.= "<li>".self::TREE.": ".$this->Tree."</li>\n";
		$s.= "<li>".self::BOTS.": ".$this->Bots."</li>\n";
		$s.= "<li>".self::ADMIN.": ".$this->Admin."</li>\n";
		$s.= "<li>".self::SUPER_ADMIN.": ".$this->SuperAdmin."</li>\n";
		$s.= "<li>Checksum: ".$this->CheckSum()."\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Permissions are not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

/*	function ToJs() {
		$topic = JsQuote(str_replace("\\\\", "<br>", $this->Topic));
		return "new Room(\"".JsQuote($this->Id)."\",\"".JsQuote($this->Title)."\",\"".$topic."\",\"".JsQuote($this->TopicLock)."\",\"".JsQuote($this->TopicAuthorId)."\",\"".JsQuote($this->TopicAuthorName)."\",".Boolean($this->IsLocked).",".Boolean($this->IsInvitationRequired).",".round($this->OwnerId).")";
	}

	function ToDTO() {
		return "new rdto(".round($this->Id).",\"".JsQuote($this->Title)."\",".Boolean($this->IsDeleted).",".Boolean($this->IsLocked).",".Boolean($this->IsInvitationRequired).")";
	}*/

	// SQL
	function ReadExpression() {
		return "SELECT *
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::USER_ID.", 
".self::POSTING.", 
".self::BOLD.", 
".self::ITALIC.", 
".self::UNDERLINE.", 
".self::TOPIC.", 
".self::TOPIC_LOCK.", 
".self::KICK.", 
".self::BAN.", 
".self::TREE.", 
".self::BOTS.", 
".self::ADMIN.", 
".self::SUPER_ADMIN."
)
VALUES
(".round($this->Id).", 
".Boolean($this->Posting).", 
".Boolean($this->Bold).", 
".Boolean($this->Italic).", 
".Boolean($this->Underline).", 
".Boolean($this->Topic).", 
".Boolean($this->TopicLock).", 
".Boolean($this->Kick).", 
".Boolean($this->Ban).", 
".Boolean($this->Tree).", 
".Boolean($this->Bots).", 
".Boolean($this->Admin).", 
".Boolean($this->SuperAdmin)."
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::USER_ID."=".round($this->Id).", 
".self::POSTING."=".Boolean($this->).", 
".self::BOLD."=".Boolean($this->).", 
".self::ITALIC."=".Boolean($this->).", 
".self::UNDERLINE."=".Boolean($this->).", 
".self::TOPIC."=".Boolean($this->).", 
".self::TOPIC_LOCK."=".Boolean($this->).", 
".self::KICK."=".Boolean($this->).", 
".self::BAN."=".Boolean($this->).", 
".self::TREE."=".Boolean($this->).", 
".self::BOTS."=".Boolean($this->).", 
".self::ADMIN."=".Boolean($this->).", 
".self::SUPER_ADMIN."=".Boolean($this->)."
WHERE 
	".self::USER_ID."=".round($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::USER_ID."=".round($this->Id);
	}
}


?>