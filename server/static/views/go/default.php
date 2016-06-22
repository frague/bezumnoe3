<?
    $url = $_GET["url"];
    $proto = $_GET["proto"];

    if (!$url) {
        $url = "www.bezumnoe.ru";
    } else {
        $url = str_replace("&amp;", "&", $url);
    }

    if (!$proto) {
        $proto = "http";
    }

    header("Location: ".$proto."://".$url);
?>
