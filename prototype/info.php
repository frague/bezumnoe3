<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/3/css/global.css">
		<link rel="stylesheet" type="text/css" href="/3/css/info.css">
		<?

	$root = "../";
	require_once $root."server_references.php";

	$user = GetAuthorizedUser($dbMain, true);

	$user_id = round($_GET["id"]);
	if (!$user_id)  {
		error("Не задан ID пользователя.");
		exit;
	}

	$person = new UserComplete();
	$person->GetById($user_id);
	if ($person->IsEmpty()) {
		error("Пользователь не найден.");
		exit;
	}

	$profile = new Profile($person->User->Id);
	$profile->Retrieve();

	require_once $root."references.php"; 


/* Functions */

	function ProfileLine($label, $value, $emptyValue = "") {
		if ($value || $emptyValue) {
			echo "<div class='InfoSection'><label>".$label.":</label>".nl2br($value ? $value : $emptyValue)."</div>";
		}
	}

/* Functions */


	?>		<title><? echo $person->User->Login ?> - Пользователи Безумное.Ру</title>
	</head>

	<body>
		<div id="InfoContainer">
			<div id="InfoContent">
				<h1><? echo $person->User->Login ?></h1>
				<div id="Info" class="TabContainer">
					<h4>В чате с <strong><? echo PrintableDate($profile->Registered) ?></strong>, последний раз <strong><? echo PrintableDate($profile->LastVisit) ?></strong></h4>
		<?

			if ($profile->Photo) {
				if (($size = @GetImageSize(file_exists($PathToThumbs.$profile->Photo))) !== false) {
					echo "<a href='".$PathToPhotos.$profile->Photo."'><img src='".$PathToThumbs.$profile->Photo."' ".$size[3]." class='Photo'></a>";
				} else {
					$w = "width=\"300\"";
					if (($size = @GetImageSize(file_exists($PathToPhotos.$profile->Photo))) !== false) {
						$w = $size[3];
						if ($size[0] <= 300) {
							$w = $size[3];
						}
					}
					echo "<img src='".$PathToPhotos.$profile->Photo."' ".$w." class='Photo'>";
				}
			}

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
			ProfileLine("ICQ", $profile->Icq);
			ProfileLine("Адрес в интернете", $profile->Url);
			ProfileLine("О себе", $profile->About);

		?>
		
				</div>
			</div>
		</div>
		<script src="/3/js1/info.js"></script>
	</body>
</html>