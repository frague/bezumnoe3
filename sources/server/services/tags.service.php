<?

	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty()) {
		die();	// TODO: Implement client functionality
	}

	$search = trim(substr(UTF8toWin1251($_POST["SEARCH_TAG"]), 0, 20));
	$recordId = round($_POST["RECORD_ID"]);

	$tag = new Tag();
	if ($search) {
		echo "this.data=[];this.found=[";
		$q = $tag->GetByCondition("t1.".Tag::TITLE." LIKE '%".SqlQuote($search)."%'", $tag->ReadExpression()." LIMIT 21");	// TODO: Select from user's records only
	} else if ($recordId > 0) {
		echo "this.found=[];this.data=[";
		$q = $tag->GetByRecordId($recordId);
	} else {
		echo "this.data=[];this.found=[];";
		die;
	}
	
	
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$tag->FillFromResult($q);
		echo ($i ? "," : "").$tag->ToJs($search);
	}
	
	echo "];";
	$q->Release();

?>