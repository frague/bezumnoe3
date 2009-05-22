<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}

	$go = $_POST["go"];

	$title = trim(UTF8toWin1251(strip_tags($_POST["TITLE"])));
	$title = substr($title, 0, 1024);

	$content = trim(UTF8toWin1251(strip_tags($_POST["CONTENT"])));
	$content = substr($content, 0, 4096);

	$record_id = round($_POST["RECORD_ID"]);	// rename later
	$forum_id = round($_POST["FORUM_ID"]);	// rename later

	$type = $_POST["IS_PROTECTED"] ? ForumRecord::TYPE_PROTECTED : ForumRecord::TYPE_PUBLIC;

	$error = "";

	if (!$forum_id) {
			$error .= "Не указан форум!<br>";
	} else {
		$forum = new ForumBase($forum_id);
		$forum->Retrieve();

		$forumAccess = 0;

		if ($forum->IsEmpty()) {
			$error .= "Форум не найден!<br>";
		} else {
			// Get user acces to forum
			if ($forum->IsHidden) {
				$forumAccess = GetForumAccess($forum);
			} else {
				$forumAccess = $user->IsAdmin() ? 1 : 0;
			}

			$isJournal = $forum->IsJournal() ? 1 : 0;
			if (AddressIsBanned(new Bans(0, 1 - $isJournal, $isJournal))) {
				$error .= "Нет доступа к публикации сообщений!<br>";
			}
		}

		// Add / remove record
		if (!$error) {
			switch ($go) {
				case "delete":
					$record = new ForumRecord($record_id);
					$record->Retrieve();
					if ($record->IsEmpty()) {
						$error .= "Сообщение не найдено!<br>";
					} else {
						if ($forumAccess || $user->User->Id == $record->UserId) {
							$state = $record->IsDeleted ? 0 : 1;

							if (strpos($record->Index, "_") !== false) {
								$index = preg_replace("/_\d\d$/", "", $record->Index);
								$parentRecord = new ForumRecord();
								$q = $parentRecord->GetByIndex($record->ForumId, $user, $index, 0, 1);
								if ($q->NumRows()) {
									$q->NextResult();
									$parentRecord->FillFromResult($q);
									if ($parentRecord->IsEmpty() || $parentRecord->IsHidden) {
										$error .= "Вы не можете удалить данное сообщение!<br>";
									}
								} else {
									$error .= "Вы не можете удалить данное сообщение!<br>";
								}
							}

							if (!$error) {
								$record->GetByCondition(
									ForumRecord::INDEX." LIKE '".$record->Index."%' AND
									".ForumRecord::FORUM_ID."=".$forum->Id,
									$record->HideThreadExpression($state)
								);
  								$record->UpdateAnswersCount();
								echo "className='".($state ? "Hidden" : "")."';";
							}
						} else {
							$error .= "Вы не можете удалить данное сообщение!<br>";
						}
					}

					break;
				default:

					if (!$title) {
						$error .= "Не указано название!<br>";
					}

					if (!$content) {
						$error .= "Сообщение пустое!<br>";
					}

					if ($forum->IsHidden && !$forumAccess) {
						$error .= "У вас нет доступа к публикации сообщений!<br>";
					}

					if (!$error) {
						$newRecord = new ForumRecord();
						$newRecord->ForumId = $forum_id;
						$newRecord->Author = $user->User->Login;
						$newRecord->UserId = $user->User->Id;
						$newRecord->Type = $type;
						$newRecord->Title = $title;
						$newRecord->Content = $content;
						if ($newRecord->SaveAsReplyTo($record_id)) {
							if ($newRecord->IsTopic()) {
								echo "newRecord='".JsQuote($newRecord->ToPrint("thread.php"))."';";
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
									false
								))."';";
							}
						} else {
							$error .= "Ошибка при сохранении сообщения!<br>";
						}
					}
			}
		}
   	}
	echo "error='".JsQuote($error)."';";
?>
