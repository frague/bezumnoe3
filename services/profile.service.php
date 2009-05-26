<?

	$response = "";
	$postResponse = "";
	require_once "base.service.php";
	$isAjax = 1;

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty() || !$user_id) {
		exit;
	}
	
	$tabId = $_POST["tab_id"];
	$photo = $_FILES["PHOTO1"];

	$ownProfile = ($user_id == $user->User->Id);

	if ($ownProfile || $user->IsAdmin()) {
		if ($user_id == $user->User->Id) {
			$targetUser = $user->User;
			$targetStatus = $user->Status;
		} else {
			$targetUser = new User($user_id);
			$targetUser->Retrieve();

			$targetStatus = new Status($targetUser->StatusId);
			$targetStatus->Retrieve();

			if ($targetStatus->IsAdmin() && !$user->IsSuperAdmin()) {
				$targetUser->Clear();
			}
		}
		if ($targetUser->IsEmpty()) {
			echo "co.AlertType=true;co.Show(\"\", \"Нет доступа к профилю\", \"У админов нет доступа к профилям других администраторов и хранителей чата.\");CloseTab(obj.Tab.Id);";
			return;
		}

		//------------------------------------------------

		$profile = new Profile();
		$profile->GetByUserId($user_id);
		if (!$profile->IsEmpty()) {
			switch ($go) {
				case "upload_photo":
				case "upload_avatar":

					$isAvatar = ($go == "upload_avatar");

					/* Upload new image */
					if ($photo) {
						$response = "var tabObject = top.tabs.tabsCollection.Get('".$tabId."');obj = tabObject.Profile;";
						$postResponse = "obj.Bind();";

						$name = $isAvatar ? $profile->Avatar : $profile->Photo;
						if (!$name || $name == "nophoto.gif") {
							$name = $user_id.".jpg";
						}
						$isAjax = 0;
						$errors = Upload($photo, ($isAvatar ? $PathToAvatars : $PathToPhotos).$name);
						if (!$errors) {
							// No errors occured
							if ($isAvatar) {
								if ($name != $profile->Avatar) {
									// Image name has changed
									$profile->Avatar = $name;
									$profile->Save();
								}
								$response .= AddJsAlert("Аватар обновлён.");
							} else {
								if ($name != $profile->Photo) {
									// Image name has changed
									$profile->Photo = $name;
									$profile->Save();
								}
								$response .= AddJsAlert("Фотография обновлена.");
							}

						} else {
							$response .= AddJsAlert($errors, 1);
						}
					}
					break;
				case "save":
					$response = "";

					/* Change Password */
					$save_user = false;
					$pass_result = $targetUser->FillPasswordFromHash($_POST);
					if ($pass_result != -1) {
						if (!$pass_result) {
							$response .= JsAlert("Пароль изменён.");
							$save_user = true;
						} else {
							$response .= JsAlert($pass_result, 1);
						}
					}
					
					/* Admin's functionality (Ban & Status) */
					if (!$ownProfile && $user->IsAdmin()) {
						$targetStatus = new Status($targetUser->StatusId);
						$targetStatus->Retrieve();

						$newStatusId = round($_POST[User::STATUS_ID]);
						if ($newStatusId) {
							$newStatus = new Status($newStatusId);
							$newStatus->Retrieve();

							/* Status has changed */
							if (!$newStatus->IsEmpty() && $newStatus->Id != $targetStatus->Id) {
								if ($targetStatus->Rights < $user->Status->Rights && $newStatus->Rights < $user->Status->Rights) {

									// Write log
									LogStatusChange($targetUser->Id, $targetStatus, $newStatus, $user->User->Login);

									$targetUser->StatusId = $newStatus->Id;
									$save_user = true;
								} else {
									$response .= JsAlert("Недостаточно прав для смены статуса!", 1);
								}
							}

						}
						/* Ban/Stop ban */
						if ($user->Status->Rights > $targetStatus->Rights) {
							$bannedBy = $targetUser->BannedBy;
							if ($targetUser->FillBanInfoFromHash($_POST, $user->User)) {
								// Write log
								if (IdIsNull($bannedBy) && $targetUser->BannedBy) {
									LogBan($targetUser->Id, $targetUser->BanReason, $user->User->Login, $targetUser->BannedTill);
								} else if ($bannedBy && !$targetUser->BannedBy) {
									LogBanEnd($targetUser->Id, $user->User->Login);
								}

								$save_user = true;
							}
						}
					}
					if ($save_user) {
						$targetUser->Save();
					}

					/* Saving profile */
					$oldProfile = $profile->GetFieldset();
					$profile->FillFromHash($_POST);
					$profile->Save();
					
					// Write to log 
					LogProfileChanges($targetUser->Id, $profile, $oldProfile, $user->User->Login);

					$response .= JsAlert("Профиль успешно сохранён.");

					break;
				case "delete_photo":
					if ($profile->Photo) {
						unlink($PathToPhotos.$profile->Photo);
						$profile->Photo = "";
						$profile->Save();
						$response = JsAlert("Фотография удалена.");
					}
					break;
				case "delete_avatar":
					if ($profile->Avatar) {
						unlink($PathToAvatars.$profile->Avatar);
						$profile->Avatar = "";
						$profile->Save();
						$response = JsAlert("Аватар удалён.");
					}
					break;
			}
			$response .= "obj.FillFrom(".$profile->ToJs($targetUser, $user->IsAdmin()).");";
			if (($user->IsAdmin() && $user->Status->Rights > $targetUser->Rights) || $user->Status->Rights > 75) {
				// Sending Admin data
				$response .= "obj.FillFrom(".$targetUser->ToJs().",".$targetUser->ToJsAdminFields().");";
			}
		}
	}

	echo ($isAjax ? "" : "<script>").$response.$postResponse.($isAjax ? "" : "</script>");

?>
