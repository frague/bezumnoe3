<?

	define("LOGIN_KEY", "login");
	define("PASSWORD_KEY", "password");
	define("SESSION_KEY", "sdjfhk_session");

	function LookInRequest($keyName) {
		if ($_POST[$keyName]) {
			return $_POST[$keyName];
		} elseif ($_GET[$keyName]) {
			return $_GET[$keyName];
		} else {
			return $_COOKIE[$keyName];
		}
	}
	
	function SessionIsAlive($sessionPongTime) {
	  global $SessionLifetime;

		$pongTime = @strtotime($sessionPongTime);
		return (time() - $pongTime < $SessionLifetime);
	}

	function SetUserSessionCookie($user, $inHeader = false) {
	  global $SessionLifetime;

		if ($inHeader) {
			header("Set-Cookie: ".SESSION_KEY."=".$user->Session.";path=/;domain=.bezumnoe.ru;");
		} else {
			setcookie(SESSION_KEY, $user->Session, 0, "/", ".bezumnoe.ru");
		}
	}
	
	function GetAuthorizedUser($doPong = false, $debug = false) {
	  global $db;

	  	if (!$db) {
  			DebugLine("No DB connection available!");
	  		return new UserComplete();
	  	}
		
		$login = LookInRequest(LOGIN_KEY);
		$password = LookInRequest(PASSWORD_KEY);
		$session = LookInRequest(SESSION_KEY);

		$user = new UserComplete();

		if ($login && $password) {
			$user->GetByPassword($login, $password);
			if (!$user->IsEmpty()) {
				if ($user->User->Login != $login) {
					$user = new UserComplete();
				} else {
					if ($doPong) {
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
				}
				return $user;
			}
		}

		if ($session && !$login && !$password) {
			$user->GetBySession($session, GetRequestAddress());
//			if (!$user->IsEmpty() && !SessionIsAlive($user->User->SessionPong)) {
//			if (!$user->IsEmpty()) {
//	  			DebugLine("Session expired!");
//				$user->User->ClearSession();
//				$user->User->Save();
//				$user->Clear();
//			}
		} else {
  			DebugLine($_COOKIE[SESSION_KEY]."-");
  			DebugLine("No session ID found!");
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
		function NetAddress() {
			$this->ShownIp = $this->ClearUnknown($_SERVER["REMOTE_ADDR"]);
			$this->ShowHost = $this->ClearUnknown(@gethostbyaddr($this->ShownIp));

			$this->ProxyIp = $this->ClearUnknown($_SERVER["HTTP_X_FORWARDED_FOR"]);
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
//		echo "/* $address $pattern */\n";
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