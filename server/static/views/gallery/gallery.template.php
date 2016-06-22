<?php

    require "../inc/ui_parts/templates.php";
    require "../inc/base_template.php";

    function IsPostingAllowed() {
        return !AddressIsBanned(new Bans(0, 0, 1));
    }

?>
