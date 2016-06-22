<?php

    /* -------- Constants -------- */

    $MonthsNames = array("", "январь","февраль","март","апрель","май","июнь","июль","август","сентябрь","октябрь","ноябрь","декабрь");
    $MonthsNamesForDate = array("", "января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря");
    $DayNames = array("пн.", "вт.", "ср.", "чт.", "пт.", "сб.", "вс.");
    $DaysInMonth = array(0,31,28,31,30,31,30,31,31,30,31,30,31);

    $AdminRights = 75;
    $KeeperRights = 20;
    $TopicRights = 10;
    $BotRights = 10;

    /* -------- Settings -------- */

    $debug = 0;

    $db_name = "bezumnoe_bezumnoe";
    $host = "127.0.0.1";
    $db_user = "bezumnoe_chat";
    $db_pass = "TM2eyKpD";

    $SessionLifetime = 60 * 5;      // Session lifetime = 5 mins
    $mysqlSessionLifetime = "5";    // Session lifetime = 5 mins
    $daySeconds = 60 * 60 * 24;

    $SiteHost = "bezumnoe.ru";

    $PathToUsers = "../recover/users/";
    $PathToPhotos = "/img/photos/";
    $PathToThumbs = $PathToPhotos."thumbs/";
    $PathToAvatars = "/img/avatars/";
    $PathToGalleries = "/gallery/images/";

    $ServerPathToUsers = "recover/users/";
    $ServerPathToPhotos = "img/photos/";
    $ServerPathToThumbs = $PathToPhotos."thumbs/";
    $ServerPathToAvatars = "img/avatars/";
    $ServerPathToGalleries = "gallery/images/";


    $MaxNicknames = 5;
    $MaxNicknameLength = 20;

    /* -------- Initial values -------- */


    $ps = $_SERVER["PHP_SELF"];

    $db = new SQL($db_name, $host, $db_user, $db_pass);
    $connection = $db->DB;

    mb_internal_encoding("UTF-8");

    date_default_timezone_set("Europe/Moscow");

?>
