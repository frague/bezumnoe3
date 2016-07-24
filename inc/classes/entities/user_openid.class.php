<?php

class UserOpenId extends EntityBase {
    // Constants
    const table = "user_open_ids";

    const USER_OPENID_ID = "USER_OPENID_ID";
    const USER_ID = "USER_ID";
    const OPENID_PROVIDER_ID = "OPENID_PROVIDER_ID";
    const LOGIN = "LOGIN";

    // Properties
    var $UserId;
    var $OpenIdProviderId;
    var $Login;


    function UserOpenId() {
        $this->table = self::table;
        parent::__construct(-1, self::USER_OPENID_ID);
    }

    function Clear() {
        $this->Id = -1;
        $this->UserId = -1;
        $this->OpenIdProviderId = -1;
        $this->Login = "";
    }

    function IsFull() {
        return ($this->UserId > 0 && $this->OpenIdProviderId > 0 && $this->Login);
    }

    function GetForUser($userId) {
        return $this->GetByCondition(self::USER_ID."=".round($userId));
    }

    function FillFromResult($result) {
        $this->Id = $result->Get(self::USER_OPENID_ID);
        $this->UserId = $result->Get(self::USER_ID);
        $this->OpenIdProviderId = $result->Get(self::OPENID_PROVIDER_ID);
        $this->Login = $result->Get(self::LOGIN);
    }

    function FillFromHash($hash) {
        $this->Id = round($hash[self::USER_OPENID_ID]);
        $this->UserId = round($hash[self::USER_ID]);
        $this->OpenIdProviderId = round($hash[self::OPENID_PROVIDER_ID]);
        $this->Login = UTF8toWin1251($hash[self::LOGIN]);
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li>".self::USER_OPENID_ID.": ".$this->Id."</li>\n";
        $s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
        $s.= "<li>".self::OPENID_PROVIDER_ID.": ".$this->OpenIdProviderId."</li>\n";
        $s.= "<li>".self::LOGIN.": ".$this->Login."</li>\n";
        if (!$this->IsFull()) {
            $s.= "<li> <b>User OpenId is not saved!</b>";
        }

        $s.= "</ul>";
        return $s;
    }

    function ToJs() {
        return "new oidto("
.round($this->Id).","
.round($this->OpenIdProviderId).",'"
.JsQuote($this->Login)."')";
    }

    // SQL
    function ReadExpression() {
        return "SELECT 
    t1.".self::USER_OPENID_ID.", 
    t1.".self::USER_ID.", 
    t1.".self::OPENID_PROVIDER_ID.",
    t1.".self::LOGIN."
FROM 
    ".$this->table." AS t1 
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table." 
(".self::USER_ID.",
".self::OPENID_PROVIDER_ID.",
".self::LOGIN.")
VALUES
(".round($this->UserId).",
".round($this->OpenIdProviderId).",
'".SqlQuote($this->Login)."')";
    }

    function UpdateExpression() {
        return "UPDATE ".$this->table." SET
    ".self::USER_ID."=".round($this->UserId).",
    ".self::OPENID_PROVIDER_ID."=".round($this->OpenIdProviderId).",
    ".self::LOGIN."='".SqlQuote($this->Login)."'
WHERE
    ".self::USER_OPENID_ID."=".round($this->Id);
    }

    function DeleteExpression() {
        return $this->BaseDeleteExpression();
    }

    function DeleteUserOpenIdsExpression() {
        return "DELETE FROM ".$this->table." WHERE ".self::USER_ID."=".round($this->UserId);
    }
}

?>