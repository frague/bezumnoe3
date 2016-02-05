<?

class UserComplete extends EntityBase {

    // Properties
    var $Id;
    var $User;
    var $Nickname;
    var $Settings;
    var $Status;
    var $IIgnore;
    var $IgnoresMe;

    const I_IGNORE = "I_IGNORE";
    const IGNORES_ME = "IGNORES_ME";

    const IsIgnoredDefault = "IsIgnoredDefault";
    const IgnoresYouDefault = "IgnoresYouDefault";


    function UserComplete($id = -1) {
        $this->Id = $id;
        $this->User = new User($id);
        $this->Settings = new Settings();
        $this->Status = new Status();
        $this->Nickname = new Nickname();

        $this->table = User::table;
        parent::__construct($id, User::USER_ID);
    }

    function __destruct() {
        destroy($this->User);
        destroy($this->Settings);
        destroy($this->Status);
        destroy($this->Nickname);
    }

    function Clear() {
        $this->User->Clear();
        $this->Nickname->Clear();
        $this->Settings->Clear();
        $this->Status->Clear();
        $this->Id = $this->User->Id;
        $this->IIgnore = 0;
        $this->IgnoresMe = 0;
    }

    function IsEmpty() {
        return $this->User->IsEmpty();
    }

    function IsAdmin() {
        return $this->Status->IsAdmin();
    }

    function IsSuperAdmin() {
        return $this->Status->IsSuperAdmin();
    }

    function CheckSum($extended = false) {
        $cs = 0;
        $cs += $this->User->CheckSum($extended);
        $cs += $this->Settings->CheckSum($extended);
        $cs += $this->Nickname->CheckSum($extended);
        $cs += $this->Status->CheckSum($extended);

//		$cs += CheckSum($this->IIgnore);
//		$cs += CheckSum($this->IgnoresMe);

        DebugLine("User CS: " + $cs);
        return $cs;
    }

    function FillFromResult($result) {
        $this->User->FillFromResult($result);
        $this->Nickname->FillFromResult($result);
        $this->Status->FillFromResult($result);
        $this->Settings->FillFromResult($result);
        $this->Id = $this->User->Id;

        $this->IIgnore = Boolean($result->Get(self::I_IGNORE));
        $this->IgnoresMe = Boolean($result->Get(self::IGNORES_ME));
    }

    function GetByPassword($login, $password) {
        if ($login && $password) {
            $this->FillByCondition("t1.".User::LOGIN."='".SqlQuote($login)."' AND t1.".User::PASSWORD."='".SqlQuote(Encode($password))."' AND t1.".User::GUID." NOT LIKE '\\_%'", $this->db);
        } else {
            $this->Clear();
        }
    }

/*	function GetBySession($sessionId, $sessionAddress) {
        if ($sessionId && strlen($sessionId) == $this->User->sessionKeyLength && $sessionAddress) {
            $this->FillByCondition("t1.".User::SESSION."='".SqlQuote($sessionId)."' AND t1.".User::SESSION_ADDRESS."='".SqlQuote($sessionAddress)."'", $this->db);
        } else {
            $this->Clear();
        }
    }*/

    function SessionIsCorrect($sessionId) {
        return $sessionId && strlen($sessionId) == $this->User->sessionKeyLength;
    }

    function GetBySession($sessionId, $sessionAddress, $sessionCheck = "") {
        if ($this->SessionIsCorrect($sessionId) && $sessionAddress) {
            $this->FillByCondition("t1.".User::SESSION."='".SqlQuote($sessionId)."' AND t1.".User::SESSION_ADDRESS."='".SqlQuote($sessionAddress)."'");
            // If not found, but sessionCheck provided,
            if ($this->IsEmpty() && $this->SessionIsCorrect($sessionCheck)) {
                // .. trying to find by two session keys
                $this->FillByCondition("t1.".User::SESSION."='".SqlQuote($sessionId)."' AND t1.".User::SESSION_CHECK."='".SqlQuote($sessionCheck)."'");
                // If match found and sessionAddress is not empty
                if (!$this->IsEmpty() && $sessionAddress) {
                    // .. updating session address
                     SaveLog("Пользователь <strong>".$this->User->Login."</strong>:<ul class=\"ListChanges\"><li><b>Адрес сессии:</b> ".$this->User->SessionAddress." <b>&rarr;</b> ".$sessionAddress."</li></ul>", -1, "", AdminComment::SEVERITY_NORMAL);
                    $this->User->UpdateSessionAddress($sessionAddress);
                }
            }
        } else {
            $this->Clear();
        }
    }

    function GetByLoginGuid($login_guid) {
        if ($login_guid and strlen($login_guid) == 19) {	# TODO: Get rid of magic numbers
            $this->FillByCondition("t1.".User::LOGIN_GUID."='".SqlQuote($login_guid)."'");
        }
    }

    function GetByGuid($guid) {
        if ($guid) {
            $this->FillByCondition("t1.".User::GUID."='".SqlQuote($guid)."'");
        }
    }

    function GetNotActivatedBefore($deadline) {
        return $this->GetByCondition("", $this->ReadInactivatedForPeriodExpression($deadline));
    }

    function __tostring() {
        $s = $this->User->__tostring();
        $s .= $this->Nickname->__tostring();
        $s .= $this->Settings->__tostring();
        $s .= $this->Status->__tostring();
        $s .= "<strong>Checksum: ".$this->CheckSum()."</strong>\n";
        return $s;
    }

    function ToJs($status) {
      global $AdminRights;

        return "new User(\"".
JsQuote($this->User->Id)."\",\"".
JsQuote($this->User->Login)."\",\"".
JsQuote($this->User->RoomId)."\",\"".
JsQuote($this->User->RoomIsPermitted)."\",\"".
($status->Rights > $AdminRights ? JsQuote($this->User->SessionAddress) : "")."\",\"".
($this->User->IsAway() ? " ".JsQuote($this->User->AwayMessage) : "")."\",\"".
JsQuote($this->User->BanReason)."\",\"".
JsQuote($this->User->BannedBy)."\",\"".
JsQuote($this->Nickname->Title)."\",".
$this->Settings->ToJs().",\"".
JsQuote($this->Status->Rights)."\",\"".
JsQuote($this->Status->Title)."\",\"".
JsQuote($this->Status->Color)."\",".self::IsIgnoredDefault.",".self::IgnoresYouDefault.")";
    }

    function ToJSON($status) {
      global $AdminRights;

        return array(
            "id" => $this->User->Id,
            "login" => $this->User->Login,
            "room_id" => $this->User->RoomId,
            "room_is_permitted" => Boolean($this->User->RoomIsPermitted),
            "address" => $status->Rights > $AdminRights ? $this->User->SessionAddress : "",
            "away" => $this->User->IsAway() ? " ".$this->User->AwayMessage : "",
            "ban_reason" => $this->User->BanReason,
            "banned_by" => $this->User->BannedBy,
            "nickname" => $this->Nickname->Title,
            "settings" => $this->Settings->ToJSON(),
            "rights" => $this->Status->Rights,
            "status_title" => $this->Status->Title,
            "status_color" => $this->Status->Color,
            "is_ignored" => Boolean(self::IsIgnoredDefault),
            "ignores_you" => Boolean(self::IgnoresYouDefault)
         );
    }

    function DisplayedName() {
        if ($this->Nickname->Title) {
            return $this->Nickname->Title;
        }
        return $this->User->Login;
    }

    function UpdateChecksum($check_sum = -1) {
        if ($this->User->IsEmpty() || $this->User->CheckSum == $this->CheckSum()) {
            return;
        }

        $this->GetByCondition(
            "",
            $this->UpdateChecksumExpression($this->User->Id, $check_sum < 0 ? $this->CheckSum() : $check_sum)
        );
    }

    function Delete() {
      global $PathToPhotos, $PathToAvatars;

        if ($this->User->IsEmpty() || $this->User->IsDeleted) {
            return false;
        }

        $p = new Profile();
        $data = $p->DeleteByUserId($this->User->Id);			// Delete user profile
        if ($data["Result"]) {
            if ($data["Photo"]) {
                unlink($PathToPhotos.$data["Photo"]);
            }
            if ($data["Avatar"]) {
                unlink($PathToAvatars.$data["Avatar"]);
            }
        }


        $this->Settings->DeleteByUserId($this->User->Id);		// Delete user settings
        $this->Nickname->DeleteByUserId($this->User->Id);		// Delete user nicknames

        $fu = new ForumUser();
        $fu->DeleteByUserId($this->User->Id);					// Delete forum users

        $ig = new Ignore();
        $ig->DeleteByUserId($this->User->Id);					// Delete ignores

        $ru = new RoomUser();
        $ru->DeleteByUserId($this->User->Id);					// Delete room users

        // Delete personal blogs
        $journal = new Journal();
        $q = $journal->GetByUserId($this->User->Id);
        for ($i = 0; $i < $q->NumRows(); $i++) {
            $q->NextResult();
            $journal->FillFromResult($q);
            if (!$journal->IsEmpty()) {
                $jf = new JournalFriend();
                $jf->GetByCondition("", $jf->DeleteForumExpression($journal->Id));

                $js = new JournalSettings();
                $js->GetByCondition("", $js->DeleteForForumExpression($journal->Id));

                $jt = new JournalTemplate();
                $jt->GetByCondition("", $js->DeleteForForumExpression($journal->Id));

                $journal->Delete();								// Delete each of personal blogs along with records
            }
        }

        $this->User->Delete();									// Mark user as deleted
    }

    // SQL
    function ReadExpression() {
        return "SELECT
    t1.".User::USER_ID.",
    t1.".User::LOGIN.",
    t1.".User::PASSWORD.",
    t1.".User::ROOM_ID.",
    (".$this->User->UserRoomAccessExpression().") AS ".User::ROOM_IS_PERMITTED.",
    t1.".User::STATUS_ID.",
    t1.".User::SESSION.",
    t1.".User::SESSION_PONG.",
    t1.".User::SESSION_CHECK.",
    t1.".User::SESSION_ADDRESS.",
    t1.".User::AWAY_MESSAGE.",
    t1.".User::AWAY_TIME.",
    t1.".User::BANNED_TILL.",
    t1.".User::BAN_REASON.",
    t1.".User::BANNED_BY.",
    t1.".User::KICK_MESSAGES.",
    t1.".User::GUID.",

    t2.".Nickname::TITLE.",

    t3.".Settings::STATUS.",
    t3.".Settings::FONT_COLOR.",
    t3.".Settings::FONT_SIZE.",
    t3.".Settings::FONT_FACE.",
    t3.".Settings::FONT_BOLD.",
    t3.".Settings::FONT_ITALIC.",
    t3.".Settings::FONT_UNDERLINED.",
    t3.".Settings::IGNORE_FONTS.",
    t3.".Settings::IGNORE_COLORS.",
    t3.".Settings::IGNORE_FONT_SIZE.",
    t3.".Settings::IGNORE_FONT_STYLE.",
    t3.".Settings::RECEIVE_WAKEUPS.",
    t3.".Settings::CONFIRM_PRIVATES.",
    t3.".Settings::FRAMESET.",
    t3.".Settings::ENTER_MESSAGE.",
    t3.".Settings::QUIT_MESSAGE.",

    t4.".Status::RIGHTS.",
    t4.".Status::TITLE.",
    t4.".Status::COLOR."

FROM ".User::table." AS t1
    LEFT JOIN ".Nickname::table." AS t2
        ON t2.".Nickname::USER_ID."=t1.".User::USER_ID." AND t2.".Nickname::IS_SELECTED."=1
    LEFT JOIN ".Settings::table." AS t3
        ON t3.".Settings::USER_ID."=t1.".User::USER_ID."
    LEFT JOIN ".Status::table." AS t4
        ON t4.".Status::STATUS_ID."=t1.".User::STATUS_ID."
WHERE ##CONDITION##";
    }

    function ReadWithIgnoreDataExpression($userId) {
        $result = str_replace("FROM ".User::table, ",
    t5.".Ignore::IGNORE_ID." AS ".self::I_IGNORE.",
    t6.".Ignore::IGNORE_ID." AS ".self::IGNORES_ME."
FROM ".User::table, $this->ReadExpression());

        $result = str_replace("WHERE ##CONDITION##", "
    LEFT JOIN ".Ignore::table." AS t5
        ON t5.".Ignore::USER_ID."=".$userId." AND t5.".Ignore::IGNORANT_ID."=t1.".User::USER_ID."
    LEFT JOIN ".Ignore::table." AS t6
        ON t6.".Ignore::USER_ID."=t1.".User::USER_ID." AND t6.".Ignore::IGNORANT_ID."=".$userId."
WHERE ##CONDITION##", $result);

        return $result;
    }

    function ReadChecksumsWithIgnoreDataExpression($userId) {
        return "SELECT
    t1.".User::USER_ID.",
    t1.".User::CHECK_SUM."
FROM ".User::table." t1
WHERE ##CONDITION##";
    }

    function ReadInactivatedForPeriodExpression($deadline) {
        return "SELECT
    t1.".User::USER_ID.",
    t1.".User::LOGIN."
FROM ".User::table." t1
    JOIN ".Profile::table." AS t2
        ON t2.".Profile::USER_ID."=t1.".User::USER_ID."
WHERE
    t1.".User::GUID." LIKE '\\_%' AND
    t2.".Profile::REGISTERED."<'".SqlQuote($deadline)."'";
    }

    function CreateExpression() {
        error("Record cannot be created directly!");
        return false;
    }

    function UpdateExpression() {
        error("Record cannot be updated directly!");
        return false;
    }

    function DeleteExpression() {
        error("Record cannot be deleted directly!");
        return false;
    }

    function UpdateChecksumExpression($userId, $checkSum) {
        return "UPDATE ".User::table."
SET ".User::CHECK_SUM."=".Nullable(round($checkSum))."
WHERE ".User::USER_ID."=".round($userId);
    }
}

?>
