<?php
	
	$root = "./";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";
	require_once $root."references.php";


	$template = new JournalTemplate();
	$q = $template->GetByCondition("1=1");

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$template->FillFromResult($q);
		$result = trim(preg_replace("/^([^<]|<[^b])*<body[^>]*>/i", "", $template->Body));
		$result = trim(preg_replace("/<\/body>[^\$]*/i", "", $result));


		$template->Body2 = $result;
		$template->Save();
		print "<li>".HtmlQuote(substr($template->Body2, 0, 100));
	}


?>