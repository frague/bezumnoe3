<html>
    <head>
        <link rel="stylesheet" type="text/css" href="/css/global.css">
        <link rel="stylesheet" type="text/css" href="/css/menu_layout.css">
        <script src="/js1/jquery/jquery.js"></script>
        <script src="/js1/jquery/jquery-ui.js"></script>
        <?php

    $root = "../";
    require_once $root."server_references.php";

    $user = GetAuthorizedUser(true);

    require_once $root."references.php"; 
    

    ?>      <title>Меню пользователя</title>
        <script language="javascript" src="/js1/wysiwyg/tinymce.min.js"></script>
    </head>

    <body onload="OnLoad()">
        <div id="AlertContainer">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
                <tr><td align="center" valign="middle">
                    <div id="AlertBlock">
                    </div>
                </td></tr></table></div>

        <div id="OptionsContainer">
            <div id="OptionsContent">
        <?php 
            if ($user->IsEmpty()) {
        ?>
                <div class="Error">Пользователь не авторизован!</div>
        <?php
            } else {
                echo "<h1>Меню пользователя ".$user->User->Login."</h1>";
            }
        ?>
            </div>
        </div>
        <script src="/js1/menu_layout.js"></script>
    </body>
</html>