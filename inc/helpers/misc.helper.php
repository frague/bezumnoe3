<?php 
    function Upload($file, $path) {
        if (!is_uploaded_file($file['tmp_name'])) {
            $temp_file = tempnam("/www/bezumnoe/data/mod-tmp", 'Tux');
			echo "/* ".$file['error']." */";
            return "Файл не загружен!";
        }
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return "Невозможно переместить файл ".$file['tmp_name']." в ".$path."!";
        }
    }

?>
