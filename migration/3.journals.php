<style>
	li {
		font-size:6.5pt;
	}
</style>
<?
	$root = "../";
	require_once $root."references.php";
	require_once "step_tracking.php";

	set_time_limit(120);

	/* Functions */


	echo "<h2>Настройки журналов:</h2>";
	
	echo "<h3>Truncate tables</h3>";
	$q = $db->Query("TRUNCATE TABLE ".JournalTemplate::table);
 	$q = $db->Query("TRUNCATE TABLE ".JournalSkin::table);
	$q = $db->Query("TRUNCATE TABLE ".JournalSettings::table);
	$q = $db->Query("TRUNCATE TABLE ".JournalFriend::table);

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


	// Settings
	echo "<h3>Настройки (1/2):</h3>";
	$q = $db->Query("SELECT * FROM _journal ORDER BY login ASC, id ASC");
	echo "<ul type=square>";

	$usersIds = array();
	$lastUser = "";
	$lastUserId = 0;
	$userRecords = 0;


	//------------------------------	
	$userIdJournal = array();
	$userLoginJournal = array();

	$messageDate = "";

	// Migrating settings
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
					$f = new Journal();
					$f->GetByUserId($lastUserId);

					if (!$f->IsEmpty()) {
						$userIdJournal[$lastUserId] = $f->Id;
						$userLoginJournal[$lastUser] = $f->Id;

						$s = new JournalSettings();
						$s->ForumId = $f->Id;
						$s->Alias = eregi("^[0-9a-z\-\=\_]+$", $lastUser) ? $lastUser : $allUsersGUIDs[$lastUser];
						$s->LastMessageDate = $messageDate;
						$s->Save();
//						echo "<li> <b>".$lastUser."</b> journal settings have been saved.";
					}
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
	}

	echo "</ul>";

	/* Templates */

	echo "<h3>Шаблоны:</h3>";

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
			echo "У пользователя <b>".$user."</b> нет записей в журнале. Игнорирую.";
			AddError("У пользователя <b>".$user."</b> нет записей в журнале. Игнорирую.");
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

/*			echo "<li> <b>".str_replace("<", "&lt;", $lastUser)."</b> saved";
			if (!$t->UserId) {
				echo " (skin ".$t->Id.")";
			}*/

			$t = new JournalTemplate();
			$lastUser = $user;
		}


		$t->ForumId = $userIdJournal[$id];
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
		echo "<h3>Дополняем неполные шаблоны:</h3>";
		$q = $db->Query("UPDATE ".JournalTemplate::table." SET ".JournalTemplate::BODY."='".SqlQuote($defaultTemplate->Body)."' WHERE ".JournalTemplate::BODY."=''");
		$q = $db->Query("UPDATE ".JournalTemplate::table." SET ".JournalTemplate::MESSAGE."='".SqlQuote($defaultTemplate->Message)."' WHERE ".JournalTemplate::MESSAGE."=''");
		$q = $db->Query("UPDATE ".JournalTemplate::table." SET ".JournalTemplate::CSS."='".SqlQuote($defaultTemplate->Css)."' WHERE ".JournalTemplate::CSS."=''");
	}


	/* Skins */
	
	echo "<h3>Предустановленные шаблоны:</h3>";
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
			echo "<li> Сохранён шаблон: ".$skin;
		} else {
			echo "<li> Шаблон <b>".str_replace("<", "&lt;", $name)."</b> не найден.";
			AddError("Шаблон <b>".str_replace("<", "&lt;", $name)."</b> не найден.", 1);
		}
	}

	
	echo "</ul>";

	echo "<h3>Настройки (2/2):</h3>";
	echo "<ul>";
	$q = $db->Query("select * from _journal_settings order by id asc");
	
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
	
		$user = $q->Get("login");
		$id = $allUsersIds[$user];
		if ($id) {
			// Template
			$template = $q->Get("template_name");

			if ($template) {
				$template_id = $templateNames["<".$template.">"];
				if ($template_id) {
					$s = new JournalSettings();
					$s->GetByForumId($userIdJournal[$id]);
					if (!$s->IsEmpty()) {
						$s->ForumId = $userIdJournal[$id];
						$s->SkinTemplateId = $template_id;
						$s->Save();
//						echo "<li> <b>".$user."</b> skin '".$template."' (".$template_id.") info moved";
					}
				} else {
					echo "<li style='color:red'> <b>".$template."</b> (".$template_id.") template not found.";
					AddError("<b>".$template."</b> (".$template_id.") journal template not found.");
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
//					echo "<li> ".$user."'s avatar saved.";
				}
			}
		} else {
			echo "<li> В журнале <b>".str_replace("<", "&lt;", $user)."</b> нет записей. Игнорирую.";
			AddError("В журнале <b>".str_replace("<", "&lt;", $user)."</b> нет записей. Игнорирую.");
		}

	}

	echo "</ul>";

	echo "<h3>Френды:</h3>";


	// Journal friends
	$q2 = $db->Query("SELECT * FROM _journal_friends ORDER BY login");
	if ($q2->NumRows()) {
		for ($i = 0; $i < $q2->NumRows(); $i++) {
			$q2->NextResult();

			$user = $q2->Get("login");
			$friends = $q2->Get("friends");

			$journalId = round($userLoginJournal[$user]);

			if ($journalId > 0) {
//				AddError($i.". Друзья пользователя <b>".$user."</b>(".$journalId."): ".$friends);

				$friends = split(",", $friends);
				for ($k = 0; $k < sizeof($friends); $k++) {
					$friend = trim($friends[$k]);

					$friend_journal_id = round($userLoginJournal[$friend]);
					if ($friend_journal_id > 0) {
						$f = new JournalFriend($journalId, $friend_journal_id);
						$f->Save();
						// Friendly journal
					}
					$friend_id = $usersIds[$friend];
					if ($friend_id > 0) {
						$f2 = new ForumUser($friend_id, $journalId);
						$f2->Access = Journal::FRIENDLY_ACCESS;
						$f2->Save();
						// Friendly user
					}
				}
			} else {
				AddError("У пользователя <b>".$user."</b> нет нет журнала. Френды игнорируются.");
			}
		}
   	}

	Passed();
?>