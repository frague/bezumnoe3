<style>
	li {
		font-size:6.5pt;
	}
</style>
<?
	$root = "../";
	require_once $root."references.php";

	set_time_limit(120);

	/* Functions */


	echo "<h2>Journal records migration:</h2>";
	
	echo "<h3>Truncate tables</h3>";
	$q = $db->Query("TRUNCATE TABLE ".JournalTemplate::table);
 	$q = $db->Query("TRUNCATE TABLE ".JournalSkin::table);
	$q = $db->Query("TRUNCATE TABLE ".JournalSettings::table);

	// Getting all users into hash
	$allUsersIds = array();
	$allUsersGUIDs = array();
	$lastMessages = array();

	$q = $db->Query("SELECT USER_ID, LOGIN, GUID FROM users");
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$login = $q->Get("LOGIN");
		$allUsersIds[$login] = $q->Get("USER_ID");
		$allUsersGUIDs[$login] = $q->Get("GUID");
	}

/*	// Friends
	echo "<h3>Moving friends:</h3>";
	$q = $db->Query("SELECT * FROM _journal_friends");
	echo "<ul type=square>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();

		$login = $q->Get("login");
		$friends = $q->Get("friends");
		$user_id = $allUsersIds[$login];
		if ($user_id) {
			$friends = split(",", $friends);
			for ($k = 0; $k < sizeof($friends); $k++) {
				$friend_id = $allUsersIds[$friends[$k]];
				if ($friend_id) {
					$f = new JournalFriend($user_id, $friend_id);
					$f->Save();
				}
			}
			echo "<li> User <b>".$login."</b> saved";
		} else {
			echo "<li style='color:red'> User <b>".$login."</b> does not exist. Skipping...";
		}
	}
	echo "</ul>";*/


	// Records
	echo "<h3>Journal Settings:</h3>";
	$q = $db->Query("SELECT * FROM _journal ORDER BY login ASC, id ASC");
	echo "<ul type=square>";

	$usersIds = array();
	$lastUser = "";
	$lastUserId = 0;
	$userRecords = 0;

	$messageDate = "";

	// Migrating records
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$user = $q->Get("login");
		if ($lastUser == $user) {
			if ($lastUserId == -1) {
				continue;	// User not found
			}
		} else {
			if ($lastUser) {
				//echo "<li> <b>".$userRecords."</b> records of <b>".$lastUser."</b> have been imported";

				if ($lastUserId > 0) {
					$s = new JournalSettings();
					$s->UserId = $lastUserId;
					$s->Alias = eregi("^[0-9a-z\-\=\_]+$", $lastUser) ? $lastUser : $allUsersGUIDs[$lastUser];
					$s->LastMessageDate = $messageDate;
					$s->Save();
					echo "<li> <b>".$lastUser."</b> journal settings have been saved.";
				}
			}
			$lastUser = $user;
			$u = new User();
			$u->FillByCondition("t1.".User::LOGIN."='".SqlQuote($user)."'");
			if ($u->IsEmpty()) {
				$lastUserId = -1;
				//echo "<li style='color:red'> User <b>".$user."</b> does not exist. Skipping...";
				continue;
			} else {
				$lastUserId = $u->Id;
				$usersIds[$user] = $u->Id;
			}
			$userRecords = 0;
		}
		$userRecords++;

		$PostId = $q->Get("id");

/*		$record = new JournalRecord();
		$record->Id = $PostId;
		$record->UserId = $lastUserId;
		$record->Title = $q->Get("title");
		$record->Content = $q->Get("content");

		$messageDate = DateFromTime($q->Get("moment"));
		$record->Date = $messageDate;


		$type = $q->Get("kind");
		$record->Type = ($type == "pu" ? 0 : ($type == "fr" ? 1 : 2));
		$record->CommentsAllowed = 1;*/


		/* Migrate post comments */

/*		$q1 = $db->Query("SELECT * FROM _journal_comments WHERE post_id=".$PostId." ORDER BY date ASC");
		$comments = $q1->NumRows();
		$last_comment_date = "";
		for ($k = 0; $k < $comments; $k++) {
			$q1->NextResult();
			$comment = new JournalComment();

			$comment->RecordId = $PostId;
			$commenter = $q1->Get("author");
			if ($allUsersIds[$commenter]) {
				$comment->UserId = $allUsersIds[$commenter];
			} else {
				$comment->NotLoggedName = $commenter;
			}
			$last_comment_date = DateFromTime($q1->Get("date"));
			$comment->Date = $last_comment_date;
			$comment->Content = $q1->Get("comment");
			//$comment->Save();
		}*/

		//$record->Save($record->MigrateExpression($comments, $last_comment_date));
	}

	echo "</ul>";

	/* Templates */

	echo "<h3>Moving templates:</h3>";

	$q = $db->Query("SELECT * FROM _journal_templates ORDER BY login");
	echo "<ul type=square>";

	$lastUser = "";
	$t = "";
	$templateNames = array();

	$defaultTemplate = "";

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();

		$user = $q->Get("login");
		$id = $usersIds[$user];

		$isSkin = (strpos($user, "<") !== false);

		if (!$id && !$isSkin) {
			echo "<li style='color:red'> <b>".$user."</b> has no records in journal";
			continue;
		}
		
		if (!$lastUser && $id) {
			$lastUser = $user;
			$t = new JournalTemplate();
		}

		if ($lastUser != $user) {
			if ($lastUser == "<default>") {
				$defaultTemplate = $t;
			}
			$t->Save();
			$templateNames[$lastUser] = $t->Id;

			echo "<li> <b>".str_replace("<", "&lt;", $lastUser)."</b> saved";
			if (!$t->UserId) {
				echo " (skin ".$t->Id.")";
			}

			$t = new JournalTemplate();
			$lastUser = $user;
		}


		$t->UserId = $id;
		$c = $q->Get("content");
		$type = $q->Get("element");
		switch ($type) {
			case "body":
				$t->Body = $c;
				break;
			case "message":
				$t->Message = $c;
				break;
			case "styles":
				$t->Css = $c;
				break;
		}
		
	}

	if (!$t->IsEmpty()) {
		$t->Save();
	}

	echo "</ul>";

	if ($defaultTemplate && !$defaultTemplate->IsEmpty()) {
		echo "<h3>Update partially empty templates:</h3>";
		$q = $db->Query("UPDATE ".JournalTemplate::table." SET ".JournalTemplate::BODY."='".SqlQuote($defaultTemplate->Body)."' WHERE ".JournalTemplate::BODY."=''");
		$q = $db->Query("UPDATE ".JournalTemplate::table." SET ".JournalTemplate::MESSAGE."='".SqlQuote($defaultTemplate->Message)."' WHERE ".JournalTemplate::MESSAGE."=''");
		$q = $db->Query("UPDATE ".JournalTemplate::table." SET ".JournalTemplate::CSS."='".SqlQuote($defaultTemplate->Css)."' WHERE ".JournalTemplate::CSS."=''");
	}


	/* Skins */
	
	echo "<h3>Moving skins:</h3>";
	echo "<ul>";
	$q = $db->Query("select * from _journal_skins order by id asc");

	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$name = $q->Get("template_name");
		$id = $templateNames["<".$name.">"];

		if ($id) {
			$skin = new JournalSkin();
			$skin->Created = $q->Get("updated");
			$skin->TemplateId = $id;
			$skin->Title = $q->Get("title");
			$skin->Author = $q->Get("author");
			$skin->Screenshot = $q->Get("screenshot");
			$skin->IsDefault = ($name == "default" ? 1 : 0);
			$skin->IsFriendly = $skin->IsDefault;
			$skin->Save();
			echo "<li> Skin saved:".$skin;

		} else {
			echo "<li style='color:red'> <b>".str_replace("<", "&lt;", $name)."</b> skin not found";
		}
	}

	
	echo "</ul>";

	echo "<h3>Moving settings:</h3>";
	echo "<ul>";
	$q = $db->Query("select * from _journal_settings order by id asc");
	
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
	
		$user = $q->Get("login");
		$id = $usersIds[$user];
		if ($id) {
			// Template
			$template = $q->Get("template_name");

			if ($template) {
				$template_id = $templateNames["<".$template.">"];
				if ($template_id) {
					$s = new JournalSettings();
					$s->GetByUserId($id);
					if (!$s->IsEmpty()) {
						$s->SkinTemplateId = $template_id;
						$s->Save();
						echo "<li> <b>".$user."</b> skin '".$template."' (".$template_id.") info moved";
					}
				} else {
					echo "<li style='color:red'> <b>".$template."</b> (".$template_id.") template not found.";
				}
			}

			// Avatar
			$avatar = $q->Get("avatar");
			if ($avatar) {
				$p = new Profile($id);
				$p->Retrieve();
				if (!$p->IsEmpty()) {
					$p->Avatar = $avatar;
					$p->Save();
					echo "<li> ".$user."'s avatar saved.";
				}
			}
		} else {
			echo "<li style='color:red'> <b>".str_replace("<", "&lt;", $user)."</b> has no records - ignored.";
		}

	}

	echo "</ul>";

	echo "Migration completed!";


?>