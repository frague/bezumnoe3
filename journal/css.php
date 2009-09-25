<?

	$root = "../";
	require_once $root."server_references.php";
	require_once "journal.template.php";

	$alias = substr(LookInRequest(JournalSettings::PARAMETER), 0, 20);

	$settings = new JournalSettings();

	if ($alias) {
		if ($settings->IsEmpty()) {
			$settings->GetByAlias($alias);
		}
		if ($settings->Alias != $alias) {
			DieWith404();
		}
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