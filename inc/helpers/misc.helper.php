<?

	function Upload($file, $path) {
		if (!is_uploaded_file($file['tmp_name'])) {
			return "���� �� ��������!";
		}
		if (!move_uploaded_file($file['tmp_name'], $path)) {
			return "���������� ����������� ���� ".$file['tmp_name']." � ".$path."!";
		}
	}

?>