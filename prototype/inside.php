<?

	$root = "../";
	require_once $root."server_references.php";

	$user = GetAuthorizedUser(true, true);

	if ($user->IsEmpty()) {
		echo "Not logged. Bye!";
		exit;
	}

	/* Check room */
	$room = new Room();
	// TODO: Entering room logic (select room upon entering)
	$room->FillByCondition("t1.".Room::IS_INVITATION_REQUIRED."=0 AND t1.".Room::IS_DELETED."=0 ORDER BY ".Room::TITLE." LIMIT 1");

	if ($room->IsEmpty()) {
		$room->Title = "Alternative";
		$room->IsLocked = 1;
		$room->Save();

		$room = new Room();
		$room->Title = "Default";
		$room->IsLocked = 1;
		$room->Save();
	}

	if (IdIsNull($user->User->RoomId))  {
		$text = $user->Settings->EnterMessage;
		if (!$text) {
			$text = "В чат входит %name";
		}
		$message = new SystemMessage(str_replace("%name", $user->DisplayedName(), $text), $room->Id);
		$message->Save();
		$user->User->RoomId = $room->Id;
		$user->User->Save();
	}

	$user->User->TouchSession();
	SetUserSessionCookie($user->User);

	require_once $root."references.php";

?><html>
	<head>
		<title>v3 prototype</title>
		<link rel="stylesheet" type="text/css" href="/3/css/global.css">
		<link rel="stylesheet" type="text/css" href="/3/css/layout.css">
	</head>

	<body onload="OnLoad()">
		<div id="AlertContainer">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
				<tr><td align="center" valign="middle">
					<div id="AlertBlock">
					</div>
				</td></tr></table></div>
		<div id='Users'>
			<div id="Wakeups"></div>
			<ul id="UsersContainer"></ul>
			<div id="NewRoom" style="display:none"></div>
		</div>

		<div id="Messages">
			<div id="MessagesContainer"></div>
		</div>

		<div id="MessageForm">
			<form onsubmit="Send();return false;">
			<table>
				<tr>
					<td></td>
					<td id="CurrentName" colspan="2"><? echo $user->DisplayedName() ?></td></tr>
				<tr>
					<td></td>
					<td id="RecepientsContainer" colspan="2"></td></tr>
				<tr>
					<td><a href="javascript:void(0)" onclick="MI('me')">me</a></td>
					<td width="100%">
						<input id="Message" style="width:100%;" autocomplete="off">
					</td><td>
						<input type="image" alt="Отправить сообщение" src="/3/img/send_button.gif" onclick="Send();return false;">
					</td></tr></table></form>
		</div>

		<div id="Status">
			<img id="pong" src="/3/img/pong.gif" style="float:right">
			<div id="MenuContainer"></div>
		</div>

		<script src="/3/js1/layout.js"></script>
		<script src="/3/js1/prototype.js"></script>
		<script>
			CurrentRoomId = '<?php echo $user->User->RoomId ?>';
			Session = '<?php echo $user->User->Session ?>';
			SessionKey = '<?php echo SESSION_KEY ?>';

			/* Tabs */
			var tabs = new Tabs($("Messages"), $("MessagesContainer"));
			var MainTab = new Tab(1, "Чат", 1)
			tabs.Add(MainTab);
			CurrentTab = MainTab;

			tabs.Print();
		</script>
	</body>
</html>