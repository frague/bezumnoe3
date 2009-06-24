<?php

	$root = "../";
	require_once $root."references.php";
	require_once "step_tracking.php";

	set_time_limit(120);

	$mz = new User();
	$mz->FillByCondition(User::LOGIN."='Мартовский Заяц'");

	echo "<h2>Копирование форумов:</h2>";

	echo "<h3>Очистка таблиц:</h3>";

	$q = $db->Query("TRUNCATE TABLE ".Forum::table);
	$q = $db->Query("TRUNCATE TABLE ".ForumRecord::table);
	$q = $db->Query("TRUNCATE TABLE ".ForumUser::table);


	// Reading all users

	$allUsersIds = array();

	$q = $db->Query("SELECT USER_ID, LOGIN, GUID FROM users");
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$login = $q->Get("LOGIN");
		$allUsersIds[$login] = $q->Get("USER_ID");
	}
	
	
	echo "<h3>Форумы:</h3>";

	$q = $db->Query("SELECT * FROM _forum_caths ORDER BY id ASC");
	echo "<ol>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$forum = new Forum();
		$forum->Id = $q->Get("id");
		$forum->Title = $q->Get("name");
		$forum->Description = $q->Get("description");
		$forum->IsProtected = $q->Get("is_closed");
		$forum->TotalCount = $q->Get("TOTAL_COUNT");
		if (!$mz->IsEmpty()) {
			$forum->LinkedId = $mz->Id;
		}
		$forum->GetByCondition("", $forum->MigrateExpression());

		echo "<li value='".$forum->Id."'>".$forum->Title;

		$q1 = $db->Query("SELECT login FROM _forum_access WHERE cath_id='".$forum->Id."'");
		for ($k = 0; $k < $q1->NumRows(); $k++) {
			$q1->NextResult();
			$friend_id = $allUsersIds[$q1->Get("login")];
			if ($friend_id) {
				$f = new ForumUser($friend_id, $forum->Id);
				$f->Access = FORUM::READ_ADD_ACCESS;
				$f->Save();
			}
		}
	}
	echo "</ol>";

	echo "<h3>Записи в форумах:</h3>";

	$q = $db->Query("SELECT * FROM _forum ORDER BY id ASC");
	echo "<ol>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$record = new ForumRecord();
//		$record->Id = $q->Get("id");
		$record->ForumId = $q->Get("cath");
		$record->Index = $q->Get("ind");
		$record->Author = $q->Get("author");

		$user_id = $allUsersIds[$record->Author];
		$record->UserId = $user_id;

		$record->Title = $q->Get("subject");
		$record->Content = $q->Get("body");
		$record->Date = $q->Get("moment");
		$record->Address = $q->Get("ip");
		$record->Clicks = $q->Get("clicks");
		$record->Guid = $q->Get("uni");

		$record->Type = round($q->Get("closed")) ? ForumRecord::TYPE_PROTECTED : ForumRecord::TYPE_PUBLIC;
		$record->IsDeleted = $q->Get("hidden");
		$record->UpdateDate = $q->Get("topic_update");
		$record->AnswersCount = $q->Get("TOPIC_ANSWERS");
		$record->Save();
		if (!($i%1000)) {
//			echo ".";
		}
	}
	echo "<li>".$i;
	AddError("Перенесено записей: ".$i);
	echo "</ol>";

	echo "<h3>Записи и комментарии в журналах:</h3>";

	$q = $db->Query("SELECT 
		t1.login,
		t2.USER_ID, 
		COUNT(1) AS RECORDS 
	FROM _journal t1 
		LEFT JOIN users t2 ON t2.LOGIN=t1.login 
	GROUP BY t1.login 
	ORDER BY t1.login");

	echo "<ol>";
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$id = $q->Get("USER_ID");
		if ($id) {
			$login = $q->Get("login");
			$journal = new Journal();
			$journal->Title = $login;
			$journal->LinkedId = $id;
			$journal->Description = "Персональный журнал";
			$journal->TotalCount = $q->Get("RECORDS");
			$journal->Save();
//			echo "<li value='".$journal->Id."'>".$journal->Title." (".$journal->TotalCount.")<br>";

			// Journal friends
			$q2 = $db->Query("SELECT * FROM _journal_friends WHERE login='".SqlQuote($login)."'");
			if ($q2->NumRows()) {
				$q2->NextResult();

				$friends = $q2->Get("friends");
				$friends = split(",", $friends);
				for ($k = 0; $k < sizeof($friends); $k++) {
					$friend_id = $allUsersIds[$friends[$k]];
					if ($friend_id) {
						$f = new JournalFriend($friend_id, $journal->Id);
						$f->Access = Journal::FRIENDLY_ACCESS;
						$f->Save();
					}
				}
			}

			// Records
			$q1 = $db->Query("SELECT * FROM _journal WHERE login='".$login."' ORDER BY id ASC");
			for ($k = 0; $k < $q1->NumRows(); $k++) {
				$q1->NextResult();
				$record_id = $q1->Get("id");

				$index = sprintf("%04d", $k);
				$record = new ForumRecord();
				$record->ForumId = $journal->Id;

				$type = $q1->Get("kind");
				$record->Type = ($type=="pr" ? ForumRecord::TYPE_PRIVATE : ($type=="fr" ? ForumRecord::TYPE_FRIENDS_ONLY : ForumRecord::TYPE_PUBLIC));

				$record->Index = $index;
				$record->Author = $login;
				$record->UserId = $id;
				$record->Title = $q1->Get("title");
				$record->Content = $q1->Get("content");
				$d = DateFromTime($q1->Get("moment"));
				$record->Date = $d;
				$record->Clicks = 0;
				$record->UpdateDate = $d;

				// Comments
				$q2 = $db->Query("SELECT t1.*, t2.USER_ID FROM _journal_comments t1 
				LEFT JOIN users t2 ON t2.LOGIN = t1.author
				WHERE post_id=".$record_id." ORDER BY id ASC");

				$answers = $q2->NumRows();
				$record->AnswersCount = $answers;
				$record->Save();

				for ($l = 0; $l < $answers; $l++) {
					$q2->NextResult();
					$record1 = new ForumRecord();
					$record1->ForumId = $journal->Id;
					$record1->Index = $index."_".sprintf("%02d", $l+1);
					$record1->Author = $q2->Get("author");
					$record1->UserId = $q2->Get("USER_ID");
					$record1->Title = " ";
					$record1->Type = $record->Type;
					$record1->Content = $q2->Get("comment");
					$record1->Date = DateFromTime($q2->Get("date"));
					$record1->Save();
				}

//				echo "|";
			}
		}
	}
	echo "</ol>";
	
//	echo "Update completed!";
	Passed();
	

?>