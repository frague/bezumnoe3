<?

	$root = "../";
	require_once $root."references.php";


	echo "<h2>Fix for journals:</h2>";
	echo "<h4>Creates non-existing <code>journal_settings</code> records for existing journals.</h4>";
	
	$q = $db->Query("SELECT t1.*, t3.LOGIN, t2.JOURNAL_SETTINGS_ID, t3.GUID  FROM forums AS t1
LEFT JOIN journal_settings AS t2 ON t2.FORUM_ID=t1.FORUM_ID
LEFT JOIN users AS t3 ON t3.USER_ID=t1.LINKED_ID
WHERE 
t1.TYPE = 'journal' AND t2.ALIAS IS NULL
ORDER BY `t2`.`ALIAS` ASC");

    echo "<ol>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$jId = $q->Get("FORUM_ID");
		$uId = $q->Get("LINKED_ID");
		$guid = $q->Get("GUID");
		$login = $q->Get("LOGIN");

		$jsId = $q->Get("JOURNAL_SETTINGS_ID");

		$login = $q->Get("LOGIN");

		$alias = $guid;
		if (preg_match("/^[a-z0-9\-\_]+$/i", $login)) {
			$alias = $login;
		}

		echo "<li>".$jId.": ".$uId."=".$login." > ".$guid." ::::: ".$alias.", settings=".$jsId;
		if (!$jsId) {
			$settings = new JournalSettings();
			$settings->ForumId = $jId;
			$settings->Alias = $alias;

			$qq = $db->Query($settings->CreateExpression());
			$settings->Id = $qq->GetLastId();

			echo $settings;
		}

	}



?>