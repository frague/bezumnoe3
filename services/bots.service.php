<?php 
	require_once "base.service.php";

	$user = GetAuthorizedUser(true);
	if (!$user || $user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();	// TODO: Implement client functionality
	}

	switch($go) {
		case "save":
			$botUserId = round($_POST["BOT_USER_ID"]);
			if (!$botUserId) {
				echo JsAlert("Не задан ID бота.");
				exit();
			}

			$botUser = new User($botUserId);
			$botUser->Retrieve();
			if ($botUser->IsEmpty()) {
				echo JsAlert("Пользователь не найден.");
				exit();
			}

			$roomId = round($_POST["ROOM"]);
			if (!$roomId) {
				echo JsAlert("Не указана комната.");
				exit();
			}
			$room = new Room($roomId);
			$room->Retrieve();
			if ($room->IsEmpty()) {
				echo JsAlert("Комната не найдена.");
				exit();
			}

			$bot = "";
			$executionDate = NowDateTime();

			switch ($_POST["TYPE"]) {
				case ScheduledTask::TYPE_YTKA_BOT:
					$bot = new YtkaBotScheduledTask($botUserId, $roomId);
					break;
				case ScheduledTask::TYPE_VICTORINA_BOT:
					$bot = new VictorinaBotScheduledTask($botUserId, $roomId);
					break;
				case ScheduledTask::TYPE_LINGVIST_BOT:
					$bot = new LingvistBotScheduledTask($botUserId, $roomId);
					break;
				case ScheduledTask::TYPE_TELEGRAM_BOT:
					$bot = new TelegramBotScheduledTask($botUserId, $roomId);
					break;
				default:
					exit();
			}

			$error = $bot->SaveChecked();
			if ($error) {
				echo JsAlert($error, 1);
			} else {
				echo JsAlert("Изменения сохранены.");
			}

			break;
		default:
			$u = new User();
			$expression = $u->FindUserExpression();
			$rows = 0;
			$value = SqlQuote(trim(substr(UTF8toWin1251($_POST["FIND_USER"]), 0, 20)));

			if ($value) {
				$condition = $value ? "(t1.".User::LOGIN." LIKE '%".$value."%' OR t2.".Nickname::TITLE." LIKE '%".$value."%')" : "1=1";
				$limit = 20;

				$q = $u->GetByCondition($condition." AND ".User::IS_DELETED."<>1", $expression.($limit ? " LIMIT ".($limit + 1) : ""));
				$rows = $q->NumRows();
				$result = "";
				if ($limit) {
					$result .= "this.more=".($rows > $limit ? 1 : 0).";";
					if ($rows > $limit) {
						$rows = $limit;
					}
				}
			}
			$result .= "this.data=[";

			if ($rows && $q) {
				for ($i = 0; $i < $rows; $i++) {
					$q->NextResult();

					$login = Mark($q->Get(User::LOGIN), $value);
					$nick = Mark($q->Get(Nickname::TITLE), $value);

					$result .= ($i > 0 ? "," : "")."new udto(".$q->Get(User::USER_ID).",'".JsQuote($login)."','".JsQuote($nick)."')";
				}
				$q->Release();
			}
			$result .= "];";

			echo $result;
	}

?>
