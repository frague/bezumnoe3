<?php

    define("LOGIN_KEY", "login");
    define("PASSWORD_KEY", "password");
    define("OPENID_LOGIN_KEY", "openid_login");
    define("OPENID_KEY", "openid");
    define("REFERER_KEY", "referer");
    define("SESSION_KEY", "sdjfhk_session");
    define("LOGIN_GUID_KEY", "f");

    function LookInRequest($keyName) {
        if (isset($_POST[$keyName])) {
            return $_POST[$keyName];
        } else if (isset($_GET[$keyName])) {
            return $_GET[$keyName];
        } else if (isset($_COOKIE[$keyName])) {
            return $_COOKIE[$keyName];
        }
        return "";
    }
    
    function SessionIsAlive($sessionPongTime) {
      global $SessionLifetime;

        $pongTime = @strtotime($sessionPongTime);
        $serverTime = time();
        return ($serverTime - $pongTime < $SessionLifetime);
    }

    function SetUserSessionCookie($user, $inHeader = false) {
      global $SessionLifetime;

        if ($inHeader) {
            header("Set-Cookie: ".SESSION_KEY."=".$user->Session.";path=/;domain=.bezumnoe.ru;");
        } else {
            setcookie(SESSION_KEY, $user->Session, 0, "/", ".bezumnoe.ru");
        }
    }
    
    function GetUserByOpenId($openIdUrl) {
      global $db;
        
        $openIdUrl = trim(substr($openIdUrl, 0, 1024));
        if (!$openIdUrl) {
            return 0;
        }

        $q = $db->Query("SELECT t1.".UserOpenId::USER_ID." 
FROM ".UserOpenId::table." AS t1
JOIN ".OpenIdProvider::table." AS t2 ON t2.".OpenIdProvider::OPENID_PROVIDER_ID." = t1.".UserOpenId::OPENID_PROVIDER_ID."
WHERE REPLACE(t2.URL, \"##LOGIN##\", t1.".UserOpenId::LOGIN.") = '".SqlQuote($openIdUrl)."'");
        if ($q->NumRows()) {
            $q->NextResult();
            return $q->Get(UserOpenId::USER_ID);
        }
        return 0;
    }

    function DoPong($user) {
        global $db;

        // Check forbidden ip/host
        $addrBan = new BannedAddress();
        if (AddressIsBanned(new Bans(1,0,0))) {
            DebugLine("Address is banned!");
            $user->User->ClearSession();
            $user->User->Save();
            $user->Clear();
        } else {
            $user->User->CreateSession();
            $user->User->Save();
        }

        // Update LastVisited
        $profile = new Profile();
        $profile->GetByUserId($user->User->Id);
        if (!$profile->IsEmpty()) {
            $profile->LastVisit = NowDateTime();
            $profile->Save();
        }
    }

    
    function GetAuthorizedUser($doPong = false, $debug = false) {
      global $db;

        if (!$db) {
            DebugLine("No DB connection available!");
            return new UserComplete();
        }
        
        $authType = LookInRequest("AUTH");
        $login = LookInRequest(LOGIN_KEY);
        $password = LookInRequest(PASSWORD_KEY);
        $session = LookInRequest(SESSION_KEY);
        $sessionCheck = LookInRequest(User::SESSION_CHECK);
        $login_guid = LookInRequest(LOGIN_GUID_KEY);

        $user = new UserComplete();

        switch ($authType) {
            case "1":
                # Login & password
                if ($login && $password) {
                    $user->GetByPassword($login, $password);
                    if (!$user->IsEmpty()) {
                        if ($user->User->Login != $login) {
                            $user = new UserComplete();
                        } else {
                            if ($doPong) {
                                DoPong($user);
                            }
                            echo "<!-- ".$user."-->";
                        }
                    }
                }
                break;
            case "2":
                # OpenID
                break;
            default:
                if ($login_guid) {
                    $user->GetByLoginGuid($login_guid);
                    if (!$user->IsEmpty()) {
                        # Clean up login guid
                        $user->User->SaveLoginGuid();
                        DoPong($user);
                    }
                    return $user;
                }   

                # Session ID exists
                if ($session && !$login && !$password) {
                    $user->GetBySession($session, GetRequestAddress(), $sessionCheck);
                } else {
                    DebugLine("No session ID found!");
                }
                break;
        }
        return $user;
    }

    function GetForumAccess($forum) {
      global $user;

        if (!$user || !$forum || $user->IsEmpty() || $forum->IsEmpty()) {
            return 0;
        }
        if ($user->IsSuperAdmin()) {
            return 2;
        }

        if ($forum->IsJournal() && $forum->LinkedId == $user->User->Id) {
            return 2;
        }

        $access = new ForumUser();
        $access->GetFor($user->Id, $forum->Id);
        return ($access->IsFull() ? ($access->IsModerator() ? 2 : 1) : 0);
    }

    
    /* Net Address functionality */

    class NetAddress {
        function getServerValue($name) {
            if (isset($_SERVER[$name])) return $_SERVER[$name];
            return "";
        }

        function NetAddress() {
            $this->ShownIp = $this->ClearUnknown($this->getServerValue("REMOTE_ADDR"));
            $this->ShowHost = $this->ClearUnknown(@gethostbyaddr($this->ShownIp));

            $this->ProxyIp = $this->ClearUnknown($this->getServerValue("HTTP_X_FORWARDED_FOR"));
            $this->ProxyHost = $this->ClearUnknown(@gethostbyaddr($this->ProxyIp));

            $this->UnderProxy = ($this->ProxyIp && $this->ProxyIp != $this->ShownIp) ? 1 : 0;
        }

        function ClearUnknown($value) {
            return $value != "unknown" ? $value : "";
        }

        function ToString() {
            return ($this->UnderProxy ? ($this->ProxyHost != "" ? $this->ProxyHost : $this->ProxyIp)." (" : "").
                ($this->ShowHost != "" ? $this->ShowHost : $this->ShownIp).
                ($this->UnderProxy ? ")" : "");
        }

        function __tostring() {
            return "<ul>
    <li> IP: ".$this->ShownIp."
    <li> Host: ".$this->ShowHost."

    <li> Proxy IP: ".$this->ProxyIp."
    <li> Proxy Host: ".$this->ProxyHost."

    <li> Is under Proxy: ".$this->UnderProxy."
</ul>";
        }
    }
    
    function GetRequestAddress() {
        $addr = new NetAddress();
        return $addr->ToString();
    }

    function AddressMatches($address, $pattern) {
//      echo "/* $address $pattern */\n";
        return ($address && preg_match($pattern, $address));
    }
    
    function AddressIsBanned($bans = "") {
        $addr = new NetAddress();
        $ban = new BannedAddress();
        $q = $ban->GetByCondition($bans ? $bans->ToCondition() : "1");
        for ($i = 0; $i < $q->NumRows(); $i++) {
            $q->NextResult();
            $ban->FillFromResult($q);

            $pattern = str_replace(".", "\\.", $ban->Content);
            $pattern = "^".str_replace("*", ".*", $pattern)."$";
            $pattern = "/".$pattern."/i";

            if (BannedAddress::TYPE_IP == $ban->Type) {
                if (AddressMatches($addr->ShownIp, $pattern) || AddressMatches($addr->ProxyIp, $pattern)) {
                    return $ban->Comment ? $ban->Comment : " ";
                }
            } else {
                if (AddressMatches($addr->ShownHost, $pattern) || AddressMatches($addr->ProxyHost, $pattern)) {
                    return $ban->Comment ? $ban->Comment : " ";
                }
            }

        }
        return false;
    }
?>