<?
	require_once "base.service.php";

	// Decode L&P
	$_POST[LOGIN_KEY] = UTF8toWin1251($_POST[LOGIN_KEY]);
	$_POST[PASSWORD_KEY] = UTF8toWin1251($_POST[PASSWORD_KEY]);

	$user = GetAuthorizedUser(true);

	$error = "";

	if (!$user || $user->IsEmpty()) {
		$error .= "������������ �� �����������!<br>";
	} else {
		SetUserSessionCookie($user->User);
	}

	$go = LookInRequest("go");

	$title = trim(UTF8toWin1251(strip_tags(LookInRequest("TITLE"))));
	$title = substr($title, 0, 1024);

	$content = trim(UTF8toWin1251(strip_tags(LookInRequest("CONTENT"))));
	$content = substr($content, 0, 4096);

	$record_id = round(LookInRequest("RECORD_ID"));	// rename later

	$type = LookInRequest("IS_PROTECTED") ? ForumRecord::TYPE_PROTECTED : ForumRecord::TYPE_PUBLIC;

	
	
	if (!$forum_id) {
			$error .= "�� ������ �����!<br>";
	} else if (!$user->IsEmpty()) {
		$forum = new ForumBase($forum_id);
		$forum->Retrieve();

		$forumAccess = 0;

		if ($forum->IsEmpty()) {
			$error .= "����� �� ������!<br>";
		} else {
			// Get user acces to forum
			if ($forum->IsHidden) {
				$forumAccess = GetForumAccess($forum);
			} else {
				$forumAccess = $user->IsAdmin() ? 1 : 0;
			}

			$isJournal = $forum->IsJournal() ? 1 : 0;
			if (AddressIsBanned(new Bans(0, 1 - $isJournal, $isJournal))) {
				$error .= "��� ������� � ���������� ���������!<br>";
			}
		}

		$oldRecord = new ForumRecord($record_id);
		$oldRecord->Retrieve();

		// Add / remove record
		if (!$error) {
			switch ($go) {
				case "delete":
					if ($oldRecord->IsEmpty()) {
						$error .= "��������� �� �������!<br>";
					} else {
						if ($forumAccess || $user->User->Id == $oldRecord->UserId) {
							$state = $oldRecord->IsDeleted ? 0 : 1;

							if (strpos($oldRecord->Index, "_") !== false) {
								$index = preg_replace("/_\d\d$/", "", $oldRecord->Index);
								$parentRecord = new ForumRecord();
								$q = $parentRecord->GetByIndex($oldRecord->ForumId, $user, $index, 0, 1);
								if ($q->NumRows()) {
									$q->NextResult();
									$parentRecord->FillFromResult($q);
									if ($parentRecord->IsEmpty() || $parentRecord->IsHidden) {
										$error .= "�� �� ������ ������� ������ ���������!<br>";
									}
								} else {
									$error .= "�� �� ������ ������� ������ ���������!<br>";
								}
							}

							if (!$error) {
								// Set record IS_DELETED to 1
								$oldRecord->GetByCondition(
									ForumRecord::INDEX." LIKE '".$oldRecord->Index."%' AND
									".ForumRecord::FORUM_ID."=".$forum->Id,
									$oldRecord->HideThreadExpression($state)
								);
								
								// Update actual & deleted answers count (review)
								$oldRecord->UpdateAnswersCount();

  								$forum->CountRecords();
								echo "className='".($state ? "Hidden" : "")."';";
							}
						} else {
							$error .= "�� �� ������ ������� ������ ���������!<br>";
						}
					}

					break;
				default:

					if (!$content) {
						$error .= "��������� ������!<br>";
					}

					if ($forum->IsHidden && !$forumAccess) {
						$error .= "� ��� ��� ������� � ���������� ���������!<br>";
					}

					if (!$error) {
						if (!$title) {
							$title = $oldRecord->IsEmpty() ? 
								"��� ��������" : 
								(preg_match("/^Re: /", $oldRecord->Title) ? "" : "Re: ").$oldRecord->Title;
						}

						$newRecord = new ForumRecord();
						$newRecord->ForumId = $forum_id;
						$newRecord->Author = $user->User->Login;
						$newRecord->UserId = $user->User->Id;
						$newRecord->Type = ($oldRecord->IsEmpty() && $oldRecord->Type > $type) ? $oldRecord->Type : $type;
						$newRecord->Title = $title;
						$newRecord->Content = $content;
						if ($newRecord->SaveAsReplyTo($record_id)) {
							if ($newRecord->IsTopic()) {
								echo "newRecord='".JsQuote($newRecord->ToPrint($forum))."';";
							} else {
								$q = $newRecord->GetAdditionalUserInfo();
								$q->NextResult();
	
								$avatar = $q->Get(Profile::AVATAR);
								$alias = $q->Get(JournalSettings::ALIAS);
								$lastMessageDate = $q->Get(JournalSettings::LAST_MESSAGE_DATE);

								echo "newRecord='".JsQuote($newRecord->ToExtendedString(
									substr_count($newRecord->Index, ForumRecord::INDEX_DIVIDER) - 1,
									$avatar,
									($lastMessageDate ? $alias : ""),
									$user,
									false,
									true
								))."';";
							}

							if ($newRecord->IsPublic() && !$forum->IsProtected && !$forum->IsJournal() && !$forum->IsGallery()) {
								$notify = new MessageNotification($forum, $newRecord);
								$notify->Save();
							}

  							$forum->CountRecords();
						} else {
							$error .= "������ ��� ���������� ���������!<br>";
						}
					}
			}
		}
   	}
	echo "error='".JsQuote($error)."';";
	echo "logged_user='".JsQuote($user->User->Login)."';";
?>
