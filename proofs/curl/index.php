<?php 

	$data = json_encode(array("a" =>1, "bb" => 2));

	print $data;

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, "http://bzmn.herokuapp.com/push");
	curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json", 'Content-Length: '.strlen($data)));
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_TIMEOUT, 1);
	curl_exec($curl);
	curl_close($curl);

?>