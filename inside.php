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

    require_once $root."references.php";

?><!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8" />
        <title>Безумное ЧАепиТие у Мартовского Зайца</title>
        <link rel="icon" href="/img/icons/favicon.ico" type="image/x-icon">
        <link rel="shortcut icon" href="/img/icons/favicon.ico" type="image/x-icon">
        <?php include $root."/inc/ui_parts/google_analythics.php"; ?>
<!-- inject:css -->
<link rel="stylesheet" href="/css/vendor-4083f5d376.css">
<link rel="stylesheet" href="/css/styles-055a3aad16.css">
<link rel="stylesheet" href="/css/styles-818091c75a.css">
<!-- endinject -->
    </head>

    <body onload="OnLoad()" id="inside">
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
                    <td id="CurrentName" colspan="2"><?php echo $user->DisplayedName() ?></td></tr>
                <tr>
                    <td></td>
                    <td id="RecepientsContainer" colspan="2"></td></tr>
                <tr>
                    <td><a href="javascript:void(0)" onclick="MI('me')">me</a></td>
                    <td width="100%">
                        <div id="Smiles"><input id="Message" style="width:100%;" autocomplete="off"></div>
                    </td><td>
                        <input type="image" alt="Отправить сообщение" src="/img/send_button.gif" onclick="Send();return false;">
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
                <li> <a href="javascript:void(0);" onclick="MI('quit')" class="Red">Выход</a>
            </ul>
            <div id="MenuContainer"></div>
        </div>

<!--         <script src="/js1/chat_layout.js"></script>
        <script src="/js1/prototype.js"></script>
        <script src="/js1/smiles.js"></script>
        <script src="/js1/smiles.php"></script>
 -->
<!-- inject:js -->
<script src="/scripts/vendor-f2a2d54a62.js"></script>
<script src="/scripts/custom-067ae920d4.js"></script>
<!-- endinject -->
        <script>
            CurrentRoomId = '<?php echo $user->User->RoomId ?>';
            Session = '<?php echo $user->User->Session ?>';
            SessionCheck = '<?php echo $user->User->SessionCheck ?>';
            SessionKey = '<?php echo SESSION_KEY ?>';

            /* Tabs */
            var tabs = new Tabs($("#Messages")[0], $("#MessagesContainer")[0]);
            var MainTab = new Tab(1, "Чат", 1);
            tabs.Add(MainTab);
            CurrentTab = MainTab;

            tabs.Print();

            HistoryGo(0);
        </script>
    </body>
</html>
