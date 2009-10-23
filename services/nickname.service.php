<?
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);

	if (!$user || $user->IsEmpty()) {
		exit;
	}
	$userNames;
	$nick = new Nickname();
	$q = $nick->GetUserNicknames($user->Id);
	$userNamesCount = $q->NumRows();

	for ($i = 0; $i < $userNamesCount; $i++) {
		$q->NextResult();
		$n = new Nickname();
		$n->FillFromResult($q);
		$userNames["_".$n->Id] = $n;
	}

	$errors = "";
	$hasNickname = false;
	$newNickname = "-1";
	if ($_POST["name0"]) {
		$result = "";
		for ($i = 1; $i <= $MaxNicknames; $i++) {

			$id = $_POST["id".$i];
			$name = ereg_replace(" +", " ", trim(UTF8toWin1251($_POST["name".$i])));

			if ($name) {
				$n = "";
				if ($id) {
					$n = $userNames["_".$id];
					if ($n && $n->Id) {
						if ($n->Title != $name) {
							$n->Title = $name;
						}
					} else {
					    $errors .= "<li> ������ ���������� &laquo;".$name."&raquo;: ��� �� ����������� ������������";
					    $n = "";
					}
				} else {
					if ($userNamesCount < $MaxNicknames) {
						$n = new Nickname();
						$n->Title = $name;
						$n->UserId = $user->Id;
					} else {
				    	$errors .= "<li> ������ ���������� &laquo;".$name."&raquo;: ��������� ����������� ���������� ����� ���";
				    	$n = "";
					}
				}
				if ($n && $n->Title) {
					$n->IsSelected = ($_POST["selected"] == $i);
					$hasNickname = $hasNickname || $n->IsSelected;

					$validationError = $n->Validate();
					if (!$validationError) {
						if (!$n->Save()) {
							$errors .= "<li> ��� &laquo;".$n->Title."&raquo ��� ������";
							if ($n->IsSelected) {
								$msg = new SystemMessage("<b>".$user->DisplayedName()."</b> ������ ��� �� ".$user->User->Login, $user->User->RoomId);
								$msg->Save();
							}
						} else {
							$userNames["_".$n->id] = $n;
							if ($n->IsSelected && $n->Title != $user->DisplayedName()) {
								$msg = new SystemMessage("<b>".$user->DisplayedName()."</b> ������ ��� �� ".$n->Title, $user->User->RoomId);
								$msg->Save();
								$newNickname = $n->Title;
							}
						}
					} else {
			    		$errors .= "<li> ".$validationError;
					}
				}
			} else {
				if ($id) {
					$n = $userNames["_".$id];
					if ($n) {
						$n->Delete();
						$userNames["_".$id] = "";
					}
				}
			}
		}
		if (!$hasNickname && $user->Nickname->Title) {
			$msg = new SystemMessage("<strong>".$user->DisplayedName()."</strong> ������ ��� �� ".$user->User->Login, $user->User->RoomId);
			$msg->Save();
		}
	}

	$i = 0;
	if ($userNames) {
		while (list($k,$n) = each($userNames)) {
			if ($n && $n->Id) {
				$result .= "nicknames.Add(new Nickname(".(++$i).",".$n->Id.",\"".JsQuote($n->Title)."\"));";
			}
		}
	}
	if ($errors) {
		echo "SetStatus(\"".JsQuote($errors)."\");";
	}

	echo "NewNickname='".SqlQuote($newNickname)."';";
	echo $result;
?>