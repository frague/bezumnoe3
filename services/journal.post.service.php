<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	$post_id = round($_POST["RECORD_ID"]);

	if (!$user || $user->IsEmpty() || !$user_id) {
		exit;
	}

	$forum = new ForumBase();
	$record = new ForumRecordBase();
	if ($post_id) {
		$record->GetById($post_id);
		if (!$record->IsEmpty()) {
			$forum->GetById($record->ForumId);
		} else {
			echo JsAlert("���� �� ������!", 1);
			die;
		}
	} elseif ($forum_id) {
		$forum->GetById($forum_id);
	} else {
		$forum->GetByUserId($user->User->Id);
	}

	if ($forum->IsEmpty()) {
		echo JsAlert("������ �� ������!", 1);
		die;
	}

	$access = $forum->GetAccess($user->User->Id);
	if ($access != Forum::FULL_ACCESS && $access != Forum::READ_ADD_ACCESS) {
		echo JsAlert("� ��� ��� ������� � ���������� �������!", 1);
		die;
	}
	
	if (!$post_id && $go != "save") {
		exit;
	}

	switch ($go) {
		case "save":
			if (!$user->IsSuperAdmin() && ($access != Forum::FULL_ACCESS || (!$record->IsEmpty() && $record->UserId != $user->User->Id))) {
				echo JsAlert("��� ������� � ���������� ���������!", 1);
				die;
			}

			$oldType = $record->Type;
			$record->FillFromHash($_POST);
			$record->ForumId = $forum->Id;
			if ($record->IsEmpty()) {
				$record->Author = $user->User->Login;
				$record->SaveAsTopic();
				echo JsAlert("��������� ���������.");

				if ($forum->IsJournal()) {
					$journal = new Journal($forum->Id);
					$journal->Title = $forum->Title;
					
					$settings = new JournalSettings();
					$settings->GetByForumId($forum->Id);

					if ($record->IsPublic() && !$settings->IsEmpty() && $settings->Alias) {
						$notify = new MessageNotification($journal, $record, $settings->Alias);
						$notify->Save();
					}
				}
  				$forum->CountRecords();
			} else {
				$record->Save();
				echo JsAlert("��������� ���������.");
				if ($record->Type != $oldType) {
					$record->SetChildType();
				}
			}
			echo "this.data=".$record->ToFullJs();

			// Post tags (labels)
			$tags = trim(substr(UTF8toWin1251($_POST["TAGS"]), 0, 1010));
			if ($tags && !$record->IsEmpty()) {
				$tags = ereg_replace("[^a-zA-Z�-���-ߨ0-9\-_\ |]", "", strip_tags($tags));

				// Create tags
				$tagsArray = split("\|", $tags);
				$tag = new Tag();
				$tag->BulkCreate($tagsArray);

				// Create links to record
				$recordTag = new RecordTag();
				$recordTag->BulkCreate($tagsArray, $record->Id);
			}

			break;
		case "delete":
			if (!$user->IsSuperAdmin() && $access != Forum::FULL_ACCESS && $record->UserId != $user->User->Id) { 
				echo JsAlert("������������ ���� ��� ��������!", 1);
				die;
			}
			echo JsAlert("��������� &laquo;".$record->Title."&raquo; �������.");
			$record->GetByCondition(
					ForumRecord::INDEX." LIKE '".substr($record->Index, 0, 4)."%' AND
					".ForumRecord::FORUM_ID."=".$forum->Id,
					$record->DeleteThreadExpression()
				);
  			
  			$forum->CountRecords();

			// Remove references to inexisting records
			$recordTag = new RecordTag();
			$recordTag->GetByCondition("", $recordTag->DeleteUnlinkedExpression());
		default:
			echo "this.data=".$record->ToFullJs();
			break;
	}

?>