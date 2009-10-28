<?

	$root = "../";
	require_once $root."server_references.php";
	require_once "journal.template.php";

	$id = round(LookInRequest("id"));

	$settings = new JournalSettings();
	if ($id) {
		$settings->GetByForumId($id);
	}

	if ($settings->IsEmpty()) {
		DieWith404();
	}

	$template = GetTemplateOrDie($settings);

    header("Content-type: text/css");
	AddEtagHeader(strtotime($template->Updated));


	// Pre-processing (?)
    echo $template->Css;

?>