<?php

  $root = "./";
  require_once $root."server_references.php";

  $user = GetAuthorizedUser(true, true);

  if ($user->IsEmpty()) {
    echo "Пользователь не авторизован. До свидания...";
    exit;
  }

  /* Check room */
  $room = new Room();
  // TODO: Entering room logic (select room upon entering)
  $room->FillByCondition("t1.".Room::IS_INVITATION_REQUIRED."=0 AND t1.".Room::IS_DELETED."=0 ORDER BY ".Room::TITLE." LIMIT 1");

  if ($room->IsEmpty()) {
    $room->Title = "Oсновная";
    $room->IsLocked = 1;
    $room->Save();

    $room = new Room();
    $room->Title = "Можно всё";
    $room->IsLocked = 1;
    $room->Save();
  }

  if (IdIsNull($user->User->RoomId))  {
    $text = $user->Settings->EnterMessage;
    if (!$text) {
      $text = "В чат входит %name";
    }
    $message = new EnterMessage(str_replace("%name", Clickable($user->DisplayedName()), $text), $room->Id);
    $message->Save();
    $user->User->RoomId = $room->Id;
    $user->User->Save();
  }

  $user->User->TouchSession();
  SetUserSessionCookie($user->User);


?><!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8" />
    <title>Безумное ЧАепиТие у Мартовского Зайца</title>
    <link rel="icon" href="/img/icons/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="/img/icons/favicon.ico" type="image/x-icon">
    <!--?php include $root."/inc/ui_parts/google_analythics.php"; ?-->
<!-- inject:css -->
<link rel="stylesheet" href="/css/vendor.css">
<link rel="stylesheet" href="/css/custom.css">
<!-- endinject -->
  </head>

  <body id="inside">
<!--     <div id="AlertContainer">
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
      <form name="messageForm" onsubmit="chat.send();return false">
      <table>
        <tr>
          <td></td>
          <td id="CurrentName" colspan="2"><?php echo $user->DisplayedName() ?></td></tr>
        <tr>
          <td></td>
          <td colspan="2"><ul id="RecepientsContainer" /></td></tr>
        <tr>
          <td><a onclick="chat.setMessageType('me')">me</a></td>
          <td width="100%">
            <div id="Smiles"><input id="Message" style="width:100%;" autocomplete="off"></div>
          </td><td>
            <input type="image" alt="Отправить сообщение" src="/img/send_button.gif">
          </td></tr>
        <tr>
          <td></td>
          <td class="ServiceLinks">
            <a href="javascript:void(0)" onclick="SwitchSmiles()">:)</a>
            <a href="javascript:void(0)" onclick="Translit()">qwe&harr;йцу</a>
            <a href="javascript:void(0)" onclick="HistoryGo(-1)">x</a>
            <a href="javascript:void(0)" onclick="HistoryGo(historyPointer+1)">&laquo;</a>
            <span id="History">История сообщений (0/0)</span>
            <a href="javascript:void(0)" onclick="HistoryGo(historyPointer-1)">&raquo;</a>
            </td>
          <td></td></tr></table></form>
    </div>

    <div id="Status">
      <img id="pong" style="float:right" src="/img/pong.gif">
      <ul class="StatusLinks">
        <li> <a href="/forum/" target="forum">Форум</a>
        <li> <a href="/journal/" target="journal">Журналы</a>
        <li> <a href="/gallery/" target="gallery">Фотогалерея</a>
        <li> <a href="javascript:void(0);" onclick="chat.setMessageType('quit')" class="Red">Выход</a>
      </ul>
      <div id="MenuContainer" />
    </div>

 -->
  </body>
  <!-- inject:js -->
  <script src="/scripts/build.js"></script>
  <!-- endinject -->
  <script>
    initChat();
    // initLayout({
    //   currentRoomId: <?php print $user->User->RoomId ?>,
    //   myId: <?php print $user->Id ?>,
    //   session: '<?php print $user->User->Session ?>',
    //   sessionCheck: '<?php print $user->User->SessionCheck ?>',
    //   sessionKey: '<?php print SESSION_KEY ?>'
    // });
  </script>
  <script src="/js1/smiles.php"></script>
</html>
