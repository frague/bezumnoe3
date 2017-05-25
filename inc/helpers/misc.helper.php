<?

    function Upload($file, $path) {
        if (!is_uploaded_file($file['tmp_name'])) {
        	$t = sys_get_temp_dir();
        	$temp_file = tempnam($t, 'Tux');
			echo "/* ".$t." ".$temp_file." */";
            return "Файл не загружен!";
        }
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return "Невозможно переместить файл ".$file['tmp_name']." в ".$path."!";
        }
    }

?>
