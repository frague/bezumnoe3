<?php

	function SendMail($recepient, $subject, $body) {
		$Name = "��� Bezumnoe.ru";
		$email = "noreply@bezumnoe.ru";

		$header = "From: ". $Name . " <" . $email . ">\r\n";

		mail($recipient, $subject, $body, $header); //mail command :) 	}
	}

?>