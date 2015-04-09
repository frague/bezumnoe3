<?
    // Base methods

    function SaveLog($content, $userId = -1, $adminLogin = "", $severity = AdminComment::SEVERITY_NORMAL) {
        $comment = new AdminComment();
        $comment->UserId = $userId;
        $comment->Content = $content;
        $comment->AdminLogin = $adminLogin;
        $comment->Severity = $severity;
        $comment->Save();
    }

    function SaveSystemLog($comment, $severity) {
        SaveLog($comment, -1, "", $severity);
    }

    function Compare($obj, $fieldset) {
        $result = "";
        $newFieldset = $obj->GetFieldset();
        for ($i = 0; $i < sizeof($obj->FieldsNames); $i++) {
            if ($newFieldset[$i] != $fieldset[$i]) {
                $result .= "<li> <b>".$obj->FieldsNames[$i].":</b> ".$fieldset[$i]." <b>&rarr;</b> ".$newFieldset[$i];
            }
        }
        return ($result ? "<ul class='ListChanges'>".$result."</ul>" : "");
    }

    // User-related logs

    function LogRegistration($userId, $login) {
        SaveLog("Регистрация пользователя <b>".$login."</b>.", $userId);
    };

    function LogProfileChanges($userId, $obj, $fieldset, $adminLogin) {
        $changes = Compare($obj, $fieldset);
        if ($changes) {
            SaveLog("Изменения в профиле:".$changes, $userId, $adminLogin);
        }
    };

    function LogBan($userId, $reason, $adminLogin, $till = "") {
         SaveLog("<i>Бан ".($reason ? "с формулировкой &laquo;".$reason."&raquo;" : "без указания причины").($till ? " до ".PrintableDate($till) : "")."</i>", $userId, $adminLogin, AdminComment::SEVERITY_ERROR);
    }

    function LogBanEnd($userId, $adminLogin) {
        SaveLog("<i>Пользователь разбанен</i>", $userId, $adminLogin, AdminComment::SEVERITY_WARNING);
    }

    function LogStatusChange($userId, $oldStatus, $newStatus, $adminLogin) {
        SaveLog("<i>Смена статуса:<br>с ".$oldStatus->ToLog()." на ".$newStatus->ToLog()."</i>", $userId, $adminLogin);
    }

    function LogAddressBan($userId, $adminLogin, $content, $parts, $reason = "") {
        SaveLog("<i>Адрес ".$content." забанен (".$parts.")".($reason ? "<br>&laquo;".$reason."&raquo;" : "")."</i>", $userId, $adminLogin, AdminComment::SEVERITY_WARNING);
    }

    function LogAddressBanEnd($userId, $adminLogin, $content, $parts) {
        SaveLog("<i>Адрес ".$content." разбанен (".$parts.")</i>", $userId, $adminLogin);
    }

    // Common admin logs (no AdminLogin & UserId defined)

?>
