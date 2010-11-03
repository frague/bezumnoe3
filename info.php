<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/css/global.css">
		<link rel="stylesheet" type="text/css" href="/css/info.css">
		<link rel="icon" href="/img/icons/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="/img/icons/favicon.ico" type="image/x-icon">
		<?php

	$root = "./";
	require_once $root."server_references.php";

	$user = GetAuthorizedUser($dbMain, true);
	$error = "";

	$user_id = round($_GET["id"]);

	if (!$user_id)  {
		$error = "Не задан ID пользователя.";
	}

	if (!$error) {
		$person = new UserComplete();
		$person->GetById($user_id);
		if ($person->IsEmpty()) {
			$error = "Пользователь не найден.";
		} else {
			$profile = new Profile();
			$profile->GetByUserId($person->User->Id);

//			echo $profile;
//			echo $person->User->Id;

			InfoRating($person->User->Id);
		}
	}

	require_once $root."references.php"; 


/* Functions */

	function ProfileLine($label, $value, $emptyValue = "") {
		if ($value || $emptyValue) {
			echo "<div class='InfoSection'><label>".$label.":</label>".nl2br($value ? $value : $emptyValue)."</div>";
		}
	}

/* Functions */


	?>		<title><? echo $error ? "Ошибка" : $person->User->Login ?> - Пользователи Безумное.Ру</title>
		<?php include $root."/inc/ui_parts/google_analythics.php"; ?>
	</head>

	<body>
		<div id="InfoContainer">
			<div id="InfoContent">
				<?php 
					if (!$error) {
				?>
				<h1><?php echo $person->User->Login ?></h1>
				<div id="Info" class="TabContainer">
					<div class="Small">В чате с <strong><?php echo PrintableDate($profile->Registered) ?></strong>, последний раз <strong><? echo PrintableDate($profile->LastVisit) ?></strong></div>
		<?php

				echo ProfilePhoto($profile, $person->User->Login);

				ProfileLine("Статус", "<font color='".$person->Status->Color."'>".$person->Status->Title."</font>");
				if ($person->User->IsBanned()) {
					$reason = $person->User->BanReason ? "по причине &laquo;".$person->User->BanReason."&raquo; " : "";
					$reason .= $person->User->BannedTill ? "до ".PrintableDate($person->User->BannedTill) : "";
					ProfileLine("Отбывает наказание", $reason);
				}
				ProfileLine("Имя", $profile->Name);
				$gender = strtolower($profile->Gender);
				ProfileLine("Пол", ($gender == "m" ? "мужской" : ($gender == "f" ? "женский" : "не определён")));
				ProfileLine("Дата рождения", BirthdayDate($profile->Birthday));
				ProfileLine("Место жительства", $profile->City);
				ProfileLine("ICQ", (preg_match("/^[0-9]+$/", $profile->Icq) ? "<img src='http://status.icq.com/online.gif?icq=".preg_replace("[^0-9]", "", $profile->Icq)."&img=26' width='13' height='13'> " : "").$profile->Icq);
				ProfileLine("Адрес в интернете", OuterLinks(MakeLinks($profile->Url)));

				// Owned journal(s)
				$journal = new Journal();
				$q = $journal->GetByUserId($person->User->Id);
				if ($q->NumRows()) {
					$j = "";
					for ($i = 0; $i < $q->NumRows(); $i++) {
						$q->NextResult();
						$journal->FillFromResult($q);
						$settings = new JournalSettings();
						$settings->GetByForumId($journal->Id);
						$j .= ($i ? "<br />" : "")."&laquo;".$settings->ToTitleLink($journal->Title, "journal")."&raquo; (".Countable("сообщение", $journal->TotalCount, "нет").")";
					}
					ProfileLine("Журнал", $j);
				}

				// Nicknames
				$names = "";
				$nick = new Nickname();
				$q = $nick->GetUserNicknames($person->User->Id);
				for ($i = 0; $i < $q->NumRows(); $i++) {
					$q->NextResult();
					$nick->FillFromResult($q);
					$names .= ($i ? "<br />" : "").$nick->Title;
				}
				if ($names) {
					ProfileLine("Альтернативные имена", "<span style='color:".$person->Settings->FontColor."'>".$names."</style>");
				}


				ProfileLine("Рейтинг активности", "<b>".$profile->Rating."</b><sup>".$profile->GetRatingDelta()."</sup> <a href=\"/rating.php\" target=\"rating\">(полный рейтинг)</a>");

				ProfileLine("О себе", OuterLinks(MakeLinks($profile->About)));
			} else {
				error($error);
			}

		?>
		
				</div>
			</div>
		</div>
		<script src="/js1/info.js"></script>
	</body>
</html>