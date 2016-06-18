<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();	// TODO: Implement client functionality
	}

	$id = round($_POST[News::OWNER_ID]);
	$news = new News();

	switch ($go) {
		case "save":
			$news->FillFromHash($_POST);
			$news->Save();
			echo JsAlert("��������� ���������.");
			break;
		case "delete":
			if ($id) {
				$news->Id = $id;
				$news->Retrieve();
				if (!$news->IsEmpty()) {
					if (!$news->Id < 0) {
						// Need to remove records also
						echo JsAlert("��������� ������ � ��� ��������� ������.");
						// TODO: Implement records deletion
					} else {
						echo JsAlert("��������� ������ �����.");
					}
					$news->Delete();
				}
			}
			break;
	}

	echo "this.data=[";
	$q = $news->GetByCondition("t1.".News::OWNER_ID." < 0");

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$news->FillFromResult($q);
		echo ($i ? "," : "").$news->ToJs();
	}
	echo "];";
	$q->Release();

?>