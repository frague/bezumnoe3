<?php

	function SendMail($to, $subject, $message) {
		$charset = "windows-1251";

		$header = "Return-Path: �������� �������� <info@bezumnoe.ru>\n";
		$header .= "MIME-Version: 1.0\n";
		$header .= "From: �������� �������� <info@bezumnoe.ru>\n";
		$header .= "X-Accept-Language: ru\n";
		$header .= "Content-Type: text/plain; charset={$charset}\n";

		return mail($to, $subject, $message, $header);
	}

?>