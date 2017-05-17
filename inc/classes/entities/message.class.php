<?

class Message extends EntityBase {
    // Constants
    const table = "messages";

    const MESSAGE_ID = "MESSAGE_ID";
    const ROOM_ID = "ROOM_ID";
    const USER_ID = "USER_ID";
    const USER_NAME = "USER_NAME";
    const TO_USER_ID = "TO_USER_ID";
    const TO_USER_NAME = "TO_USER_NAME";
    const TEXT = "TEXT";
    const DATE = "DATE";

    // Properties
    var $RoomId;
    var $UserId;
    var $UserName;
    var $ToUserId;
    var $ToUserName;
    var $Text;
    var $Date;

    // Fields
    private $messageUser;

    function Message($text = "", $user = 0) {
        $this->table = self::table;
        parent::__construct("", self::MESSAGE_ID);

        $this->SearchTemplate = "t1.".self::TEXT." LIKE '%#WORD#%' OR t4.".Nickname::TITLE." LIKE '%#WORD#%' OR t2.".User::LOGIN." LIKE '%#WORD#%' OR t5.".Nickname::TITLE." LIKE '%#WORD#%' OR t3.".User::LOGIN." LIKE '%#WORD#%'";

        $this->messageUser = new User();

        $this->Text = $text;
        if ($user && $user->Id) {
            $this->UserId = $user->Id;
            if ($user->RoomId) {
                $this->RoomId = $user->RoomId;
            }
        }
    }

    function IsPrivate() {
        return $this->ToUserId > 0 && $this->UserId != $this->ToUserId;
    }

    function IsVisibleTo($user) {
        return !$user->IsEmpty && (
            !$this->IsPrivate ||
            $user->Id == $this->UserId ||
            $user->Id == $this->ToUserId
        );
    }

    function Clear() {
        $this->Id = -1;
        $this->RoomId = "";
        $this->UserId = "";
        $this->UserName = "";
        $this->ToUserId = "";
        $this->ToUserName = "";
        $this->Text = "";
        $this->Date = NowDateTime();
    }

    function FillFromResult($result) {
        $this->Id = $result->Get(self::MESSAGE_ID);
        $this->RoomId = $result->Get(self::ROOM_ID);
        $this->UserId = $result->GetNullableIdExt(self::USER_ID);
        $this->UserName = $result->Get(self::USER_NAME);
        $this->ToUserId = $result->GetNullableId(self::TO_USER_ID);
        $this->ToUserName = $result->Get(self::TO_USER_NAME);
        $this->Text = $result->Get(self::TEXT);
        $this->Date = $result->Get(self::DATE);
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li> Message type: <strong>".($this->UserId ? ($this->ToUserId ? ($this->ToUserId == $this->UserId ? "/me" : "Private") : "Usual") : "System")."</strong></li>\n";
        $s.= "<li>".self::MESSAGE_ID.": ".$this->Id."</li>\n";
        $s.= "<li>".self::ROOM_ID.": ".$this->RoomId."</li>\n";
        $s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
        $s.= "<li>".self::USER_NAME.": ".$this->UserName."</li>\n";
        $s.= "<li>".self::TO_USER_ID.": ".$this->ToUserId."</li>\n";
        $s.= "<li>".self::TO_USER_NAME.": ".$this->ToUserName."</li>\n";
        $s.= "<li>".self::TEXT.": ".$this->Text."</li>\n";
        $s.= "<li>".self::DATE.": ".$this->Date."</li>\n";
        if ($this->IsEmpty()) {
            $s.= "<li> <b>Message is not saved!</b>";
        }

        $s.= "</ul>";
        return $s;
    }

    function ToJs($color = "", $remove_tags = false) {
        if ($this->IsEmpty()) {
            return "";
        }
        $text = $remove_tags ? strip_tags($this->Text) : $this->Text;
        return "new mdto('".JsQuote($this->Date)."','".JsQuote($this->UserName)."','".JsQuote($this->ToUserName)."','".JsQuote($text)."','".JsQuote($color)."')";
    }

    function ToPrint($highlight = "", $userId = "") {
        if ($this->IsEmpty()) {
            return "";
        }

        $text = $this->Text;
        if ($highlight) {
            $text = str_replace($highlight, "<strong>".$highlight."</strong>", $text);
            $text = preg_replace("/(href=\")([^\"]+)(\")/ie", "\"$1\".strip_tags(\"$2\").\"$3\"", $text);
        }

        $text = str_replace("\\\\", "<br>", $text);

        $moment = date("H:i", strtotime($this->Date));
        if (!$this->UserId || $this->UserId < 0) {      // System
            if ($this->ToUserId > 0) {
                return "<p class='SystemPrivate'><span class='Time'>".$moment."</span> ".$text."</p>";
            } else {
                switch ($this->UserId) {
                    case -2:
                        $timeAdd = " Green";
                        break;
                    case -3:
                        $timeAdd = " Red";
                        break;
                    case -100:
                        $me = "";
                        if (preg_match("/\/me /", $text)) {
                            $me = " me";
                            $text = preg_replace("/^\/me\s*/", "", $text);
                        }
                        return "<p class='Telegram".$me."'><i></i>".$text."</p>";
                } 
                return "<p class='System'><span class='Time".$timeAdd."'>".$moment."</span> ".$text."</p>";
            }
        } else {
            $settings = new Settings();
            $settings->GetByUserId($this->UserId);

            $linkName = "#add#";
            $privateLink = "#pvt# ";

            if ($this->ToUserId > 0) {
                if ($this->UserId == $this->ToUserId) {     // me
                    return "<p class='me' #style#>".$linkName." ".$text."</p>";
                } else {            // Private
                    $s = "<p class='Private' #style#><a>";
                    /*if ($userId) {
                        $s .= $userId == $this->UserId ? "для ".$this->ToUserName : $this->UserName;
                    } else {
                        $s .= $this->UserName." &raquo; ".$this->ToUserName."]";
                    }
                    $s .= "]</a> ".$text."</p>";*/
                    $s .= $this->UserName.":</a> ".$text."</p>";
                    return $s;
                }
            }
        }

        return "<p #style#>".$privateLink.$linkName.": ".$text."</p>";
    }

    function ToJSON() {
        if ($this->IsEmpty()) {
            return "";
        }
        return array(
            "id" => $this->Id,
            "room_id" => $this->RoomId,
            "user_id" => $this->UserId,
            "user_name" => $this->UserName,
            "to_user_id" => $this->ToUserId,
            "to_user_name" => $this->ToUserName,
            "text" => $this->Text,
            "moment" => $moment
        );
    }

    // SQL
    function ReadExpression() {
        return "SELECT
    t1.".self::MESSAGE_ID.",
    t1.".self::ROOM_ID.",
    t1.".self::USER_ID.",
    t1.".self::TO_USER_ID.",
    t1.".self::TEXT.",
    t1.".self::DATE.",
    COALESCE(t4.".Nickname::TITLE.",t2.".User::LOGIN.") AS ".self::USER_NAME.",
    COALESCE(t5.".Nickname::TITLE.",t3.".User::LOGIN.") AS ".self::TO_USER_NAME."
FROM
    ".$this->table." AS t1
LEFT JOIN ".User::table." AS t2
    ON t2.".User::USER_ID."=t1.".self::USER_ID."
LEFT JOIN ".User::table." AS t3
    ON t3.".User::USER_ID."=t1.".self::TO_USER_ID."
LEFT JOIN ".Nickname::table." AS t4
    ON t4.".Nickname::USER_ID."=t1.".self::USER_ID." AND t4.".Nickname::IS_SELECTED."=1
LEFT JOIN ".Nickname::table." AS t5
    ON t5.".Nickname::USER_ID."=t1.".self::TO_USER_ID." AND t5.".Nickname::IS_SELECTED."=1
WHERE
    ##CONDITION##
";
    }

    function ReadWithIgnoresExpression($reader_id) {
        $s = str_replace("WHERE", "
LEFT JOIN ".Ignore::table." AS t6
    on t6.".Ignore::USER_ID."=".SqlQuote($reader_id)." AND t6.".Ignore::IGNORANT_ID."=t1.".self::USER_ID."
WHERE
    (t6.".Ignore::IGNORE_ID." IS NULL) AND ", $this->ReadExpression());
        return $s;
    }

    function ReadShortExpression() {
        return "SELECT
    ".self::USER_ID.",
    ".self::TEXT."
FROM
    ".$this->table."
WHERE
    ##CONDITION##
";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table."
(".self::ROOM_ID.",
".self::USER_ID.",
".self::TO_USER_ID.",
".self::TEXT.",
".self::DATE."
)
VALUES
('".SqlQuote($this->RoomId)."',
".NullableIdExt($this->UserId).",
".NullableId($this->ToUserId).",
'".SqlQuote($this->Text)."',
'".NowDateTime()."'
)";
    }

    function UpdateExpression() {
        $result = "UPDATE ".$this->table." SET
".self::ROOM_ID."='".SqlQuote($this->RoomId)."',
".self::USER_ID."=".NullableId($this->UserId).",
".self::TO_USER_ID."=".NullableId($this->ToUserId).",
".self::TEXT."='".SqlQuote($this->Text)."',
".self::DATE."='".SqlQuote($this->Date)."'
WHERE
    ".self::MESSAGE_ID."=".SqlQuote($this->Id);
        return $result;
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table." WHERE ".self::MESSAGE_ID."=".SqlQuote($this->Id);
    }
}

/* /me message */

class MeMessage extends Message {
    function MeMessage($text, $user) {
        parent::__construct($text, $user);
        $this->ToUserId = $user->Id;
    }
}

/* Private message to some other user */

class PrivateMessage extends Message {
    function PrivateMessage($text, $user, $toUserId) {
        parent::__construct($text, $user);
        $this->ToUserId = $toUserId;
        $this->RoomId = -1;
    }
}

/* System message */

class SystemMessage extends Message {
    function SystemMessage($text, $roomId) {
        parent::__construct($text, "");
        $this->RoomId = $roomId;
    }
}

/* Chat entering message */

class EnterMessage extends SystemMessage {
    function EnterMessage($text, $roomId) {
        parent::__construct($text, $roomId);
        $this->UserId = -2;
    }
}

/* Chat quiting message */

class QuitMessage extends SystemMessage {
    function QuitMessage($text, $roomId) {
        parent::__construct($text, $roomId);
        $this->UserId = -3;
    }
}

/* Private system message */

class PrivateSystemMessage extends Message {
    function PrivateSystemMessage($text, $roomId, $toUserId) {
        parent::__construct($text, "");
        $this->ToUserId = $toUserId;
        $this->RoomId = $roomId;
    }
}

/* Message notification */

class MessageNotification extends SystemMessage {
    function MessageNotification($forum, $message, $alias = "") {
        $text = "В ".$forum->SpellType." &laquo;".$forum->GetLink($alias, $message->Id)."&raquo; добавлено новое сообщение.";
//      SystemMessage($text, $roomId);
        parent::__construct($text, -1);
    }
}

/* Telegram message */

class TelegramMessage extends Message {
    function TelegramMessage($name, $text, $roomId, $isMe) {
        if ($isMe) {
            $message = "/me ".$name." ".$text;
        } else {
            $message = $name.": ".$text;
        }
        parent::__construct($message, "");
        $this->UserId = -100;
        $this->RoomId = $roomId;
    }
}

?>
