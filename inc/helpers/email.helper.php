<?php

    function SendMail($to, $subject, $message) {
        $charset = "utf-8";

        $header = "Return-Path: Безумное ЧАепиТие <info@bezumnoe.ru>\n";
        $header .= "MIME-Version: 1.0\n";
        $header .= "From: Безумное ЧАепиТие <info@bezumnoe.ru>\n";
        $header .= "X-Accept-Language: ru\n";
        $header .= "Content-Type: text/plain; charset={$charset}\n";

        return mail($to, $subject, $message, $header);
    }

?>