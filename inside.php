<?

	$root = "./";
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
		$room->Title = "O�������";
		$room->IsLocked = 1;
		$room->Save();

		$room = new Room();
		$room->Title = "����� ��";
		$room->IsLocked = 1;
		$room->Save();
	}

	if (IdIsNull($user->User->RoomId))  {
		$text = $user->Settings->EnterMessage;
		if (!$text) {
			$text = "� ��� ������ %name";
		}
		$message = new EnterMessage(str_replace("%name", $user->DisplayedName(), $text), $room->Id);
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
		<link rel="stylesheet" type="text/css" href="/css/global.css">
		<link rel="stylesheet" type="text/css" href="/css/layout.css">
		<link rel="icon" href="/img/icons/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="/img/icons/favicon.ico" type="image/x-icon">
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
						<input type="image" alt="��������� ���������" src="/img/send_button.gif" onclick="Send();return false;">
					</td></tr>
				<tr>
					<td></td>
					<td align="right">
						<a href="javascript:void(0)" onclick="HistoryGo(-1)">x</a>&nbsp;
						<a href="javascript:void(0)" onclick="HistoryGo(historyPointer+1)">&laquo;</a>&nbsp;
						<span id="History">������� ��������� (0/0)</span>&nbsp;
						<a href="javascript:void(0)" onclick="HistoryGo(historyPointer-1)">&raquo;</a> 
						</td>
					<td></td></tr></table></form>
		</div>

		<div id="Status">
			<img id="pong" style="float:right" src="/img/pong.gif">
			<ul class="StatusLinks">
				<li> <a href="/forum/" target="forum">�����</a>
				<li> <a href="/journal/" target="journal">�������</a>
				<li> <a href="/gallery/" target="gallery">�����������</a>
			</ul>
			<div id="MenuContainer"></div>
		</div>

		<script src="/js1/layout.js"></script>
		<script src="/js1/prototype.js"></script>
		<script>
			CurrentRoomId = '<?php echo $user->User->RoomId ?>';
			Session = '<?php echo $user->User->Session ?>';
			SessionKey = '<?php echo SESSION_KEY ?>';

			/* Tabs */
			var tabs = new Tabs($("Messages"), $("MessagesContainer"));
			var MainTab = new Tab(1, "���", 1)
			tabs.Add(MainTab);
			CurrentTab = MainTab;

			tabs.Print();

			HistoryGo(0);
		</script>
	</body>
</html>