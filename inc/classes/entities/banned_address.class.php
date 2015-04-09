<?
class Bans {
	const BAN_CHAT = "BAN_CHAT";
	const BAN_FORUM = "BAN_FORUM";
	const BAN_JOURNAL = "BAN_JOURNAL";

	var $bans = array("вход в чат", "форум", "журналы");

	var $Chat;
	var $Forum;
	var $Journal;

	function Bans($chat = 0, $forum = 0, $journal = 0) {
		$this->Chat = $chat;
		$this->Forum = $forum;
		$this->Journal = $journal;
	}

	function FillFromResult($result) {
		$this->Chat = Boolean($result->Get(self::BAN_CHAT));
		$this->Forum = Boolean($result->Get(self::BAN_FORUM));
		$this->Journal = Boolean($result->Get(self::BAN_JOURNAL));
	}
	
	function FillFromHash($hash) {
		$this->Chat = Boolean($hash[self::BAN_CHAT]);
		$this->Forum = Boolean($hash[self::BAN_FORUM]);
		$this->Journal = Boolean($hash[self::BAN_JOURNAL]);
	}

	function AllowsEverything() {
		return !$this->Chat && !$this->Forum && !$this->Journal;
	}

	function ToJs() {
		return JsQuote($this->Chat).","
.JsQuote($this->Forum).","
.JsQuote($this->Journal);
	}

	function __tostring() {
		$s = "<li>".self::BAN_CHAT.": ".$this->Chat."</li>\n";
		$s.= "<li>".self::BAN_FORUM.": ".$this->Forum."</li>\n";
		$s.= "<li>".self::BAN_JOURNAL.": ".$this->Journal."</li>\n";
		return $s;
	}

	function GetList() {
		$parts = array($this->Chat, $this->Forum, $this->Journal);
		$flag = false;
		$result = "";
		for ($i = 0; $i < sizeof($parts); $i++) {
			if ($parts[$i]) {
				$result .= ($flag ? ", " : "").$this->bans[$i];
				$flag = true;
			}
		}
		return $result;
	}

	function ToCondition($t = "t1") {
		$result = "";
		if ($this->Chat) {
			$result = $t.".".self::BAN_CHAT."=1";
		}
		if ($this->Forum) {
			$result .= ($result ? " OR " : "").$t.".".self::BAN_FORUM."=1";
		}
		if ($this->Journal) {
			$result .= ($result ? " OR " : "").$t.".".self::BAN_JOURNAL."=1";
		}
		return $result ? $result : "1";
	}

	function ReadExpression($t = "t1") {
		return $t.".".self::BAN_CHAT.",
".$t.".".self::BAN_FORUM.",
".$t.".".self::BAN_JOURNAL;
	}

	function InsertFieldsExpression() {
		return self::BAN_CHAT.",
".self::BAN_FORUM.",
".self::BAN_JOURNAL;
	}

	function InsertValuesExpression() {
		return Boolean($this->Chat).",
".Boolean($this->Forum).",
".Boolean($this->Journal);
	}

	function UpdateExpression() {
		return self::BAN_CHAT."=".Boolean($this->Chat).",
".self::BAN_FORUM."=".Boolean($this->Forum).",
".self::BAN_JOURNAL."=".Boolean($this->Journal);
	}
}

/* ---------------------------------------------------- */

class BannedAddress extends EntityBase {
	// Constants
	const table = "banned_addresses";

	const BAN_ID = "BAN_ID";
	const CONTENT = "CONTENT";
	const TYPE = "TYPE";
	const COMMENT = "COMMENT";
	const ADMIN_LOGIN = "ADMIN_LOGIN";
	const ADDED = "ADDED";
	const TILL = "TILL";

	const TYPE_IP = "ip";
	const TYPE_HOST = "host";

	// Properties
	var $Content;
	var $Type;
	var $Comment;
	var $AdminLogin;
	var $Added;
	var $Till;

	var $Bans;

	function BannedAddress($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::BAN_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->Content = "";
		$this->Type = "";
		$this->Comment = "";
		$this->AdminLogin = "";
		$this->Added = NowDateTime();
		$this->Added = "";
		$this->Till = "";

		$this->Bans = new Bans();
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::BAN_ID);
		$this->Content = $result->Get(self::CONTENT);
		$this->Type = $result->Get(self::TYPE);
		$this->Comment = $result->Get(self::COMMENT);
		$this->AdminLogin = $result->Get(self::ADMIN_LOGIN);
		$this->Added = $result->Get(self::ADDED);
		$this->Till = $result->Get(self::TILL);

		$this->Bans->FillFromResult($result);
	}

	function FillFromHash($hash, $adminLogin = "") {
		$this->Id = NullableId($hash[self::BAN_ID]);
		$this->Type = $hash[self::TYPE] == self::TYPE_IP ? self::TYPE_IP : self::TYPE_HOST;
		$this->Content = $hash[self::CONTENT];

		$this->Comment = strip_tags(UTF8toWin1251($hash[self::COMMENT]));
		$this->AdminLogin = strip_tags(substr(($adminLogin ? $adminLogin : $hash[self::ADMIN_LOGIN]), 0, 20));

		$this->Till = $hash[self::TILL];

		$this->Bans->FillFromHash($hash);
	}

	function HasErrors() {
	  global $db;

		$errors = "";
		$ipPart = "(2(5[0-5]|[0-4][0-9])|[0-1]{0,1}[0-9]{1,2}|\*)";

		if ($this->Type == "ip") {
			if (!ereg("^".$ipPart."\.".$ipPart."\.".$ipPart."\.".$ipPart."$", $this->Content)) {
				$errors .= "Неверный формат IP-адреса - ".$this->Content."!<br>";
			}
		} else {
			if (!eregi("^[\.0-9a-z\_\*\-]+$", $this->Content)) {
				$errors .= "Неверный формат хоста - ".$this->Content."!<br>";
			}
		}
		if ($this->Bans->AllowsEverything()) {
			$errors .= "Не указана область запрета (чат, форум, журналы)!<br>";
		}

		$q = $db->Query($this->CheckDuplicatesExpression());
		if ($q->NumRows() > 0) {
			$q->NextResult();
			$id = $q->Get(self::BAN_ID);
			if ($id && $sthis->Id && $id != $sthis->Id) {
				$errors .= "Адрес уже присутствует в списке!";
			}
		}
		return $errors;
	}

	function ToJs() {
		return "new badto("
.$this->Id.",\""
.JsQuote($this->Content)."\",\""
.JsQuote($this->Type)."\",\""
.JsQuote($this->Comment)."\",\""
.JsQuote($this->AdminLogin)."\",\""
.JsQuote($this->Added)."\",\""
.JsQuote($this->Till)."\","
.$this->Bans->ToJs().")";
	}

	function ToJsProperties($user) {
		if (!$user || $user->IsEmpty()) {
			return;
		}
		// "BAN_ID", "BAN_CHAT", "BAN_FORUM", "BAN_JOURNAL", "TYPE", "CONTENT", "COMMENT", "TILL"
		return "["
.$this->Id.","
.$this->Bans->ToJs().",\""
.JsQuote($this->Type)."\",\""
.JsQuote($this->Content)."\",\""
.JsQuote($this->Comment)."\",\""
.JsQuote(substr($this->Till, 0, 10))."\"]";
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::BAN_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::CONTENT.": ".$this->Content."</li>\n";
		$s.= "<li>".self::TYPE.": ".$this->Type."</li>\n";
		$s.= "<li>".self::COMMENT.": ".$this->Comment."</li>\n";
		$s.= "<li>".self::ADMIN_LOGIN.": ".$this->AdminLogin."</li>\n";
		$s.= "<li>".self::ADDED.": ".$this->Added."</li>\n";
		$s.= "<li>".self::TILL.": ".$this->Till."</li>\n";
		$s.= $this->Bans;
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Banned address is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}


	// SQL

	function ReadExpression() {
		return "SELECT 
	t1.".self::BAN_ID.",
	t1.".self::CONTENT.",
	t1.".self::TYPE.",
	t1.".self::COMMENT.",
	t1.".self::ADMIN_LOGIN.",
	t1.".self::ADDED.",
	t1.".self::TILL.",
	".$this->Bans->ReadExpression("t1")."
FROM
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::CONTENT.",
".self::TYPE.",
".self::COMMENT.",
".self::ADMIN_LOGIN.",
".self::ADDED.",
".self::TILL.",
".$this->Bans->InsertFieldsExpression()."
)
VALUES
('".SqlQuote($this->Content)."',
'".($this->Type == self::TYPE_HOST ? self::TYPE_HOST : self::TYPE_IP)."',
".Nullable(SqlQuote($this->Comment)).",
".Nullable(SqlQuote($this->AdminLogin)).",
'".SqlQuote($this->Added)."',
".Nullable(SqlQuote($this->Till)).",
".$this->Bans->InsertValuesExpression()."
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::CONTENT."='".SqlQuote($this->Content)."', 
".self::TYPE."='".($this->Type == self::TYPE_HOST ? self::TYPE_HOST : self::TYPE_IP)."', 
".self::COMMENT."=".Nullable(SqlQuote($this->Comment)).", 
".self::ADMIN_LOGIN."=".Nullable(SqlQuote($this->AdminLogin)).", 
".self::ADDED."='".SqlQuote($this->Added)."',
".self::TILL."=".Nullable(SqlQuote($this->Till)).",
".$this->Bans->UpdateExpression()."
WHERE 
	".self::BAN_ID."=".round($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::BAN_ID."=".round($this->Id);
	}

	function CheckDuplicatesExpression() {
		return "SELECT 
	t1.".self::BAN_ID."
FROM
	".$this->table." AS t1 
WHERE
	t1.".self::CONTENT." LIKE '".SqlQuote($this->Content)."'
LIMIT 1";
	}
}

?>