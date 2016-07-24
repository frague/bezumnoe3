<?php

    function Upload($file, $path) {
        if (!is_uploaded_file($file['tmp_name'])) {
            return "Файл не загружен!";
        }
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return "Невозможно переместить файл ".$file['tmp_name']." в ".$path."!";
        }
    }

?>