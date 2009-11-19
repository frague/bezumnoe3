<?php

	$root = "./";
	require_once $root."server_references.php";

	$user = GetAuthorizedUser(true);
	SetUserSessionCookie($user->User);

	require $root."inc/ui_parts/templates.php";
	require $root."inc/ui_parts/news.php";

	require_once $root."references.php";

?><html>
<head>
	<title>Лучший саратовский чат &mdash; Безумное ЧАепиТие у Мартовского Зайца</title>
	<link rel="stylesheet" type="text/css" href="/css/global.css" />
	<link rel="stylesheet" type="text/css" href="/css/default2.css" />
</head>

<style>
</style>

<body>
	<div class="Block">
		<div id="left">

			<img src="/img/t/online.gif" />
			<?php include $root."inc/ui_parts/online_users.php"; ?>

			<br />
			<br />

			<img src="/img/t/news.gif" />
			<?php ShowNews(-1, 5); ?>

			<br />
			<br />

			<img src="/img/t/birthdays.gif" />
			<ul class="Hbd"> <h4>сегодня</h4>
				<li> <a href="#">Мартовский Заяц</a>,
				<li> <a href="#">Мартовский Заяц</a>,
				<li> <a href="#">Мартовский Заяц</a>,
				<li> <a href="#">Мартовский Заяц</a>
			</ul>
			<ul class="Hbd"> <h4>завтра</h4>
				<li> <a href="#">Мартовский Заяц</a>,
				<li> <a href="#">Мартовский Заяц</a>,
				<li> <a href="#">Мартовский Заяц</a>,
				<li> <a href="#">Мартовский Заяц</a>
			</ul>
			<ul class="Hbd"> <h4>послезавтра</h4>
				<li> <a href="#">Мартовский Заяц</a>,
				<li> <a href="#">Мартовский Заяц</a>,
				<li> <a href="#">Мартовский Заяц</a>,
				<li> <a href="#">Мартовский Заяц</a>
			</ul>


			<br />
			<br />

			<img src="/img/t/photo.gif" />
		</div>

		<div id="right">

			<img src="/img/t/logo.gif" />
			<img src="/img/t/title.gif" />


			<form method="POST">
			Логин:
			<input name="<? echo LOGIN_KEY ?>" value="<? echo $_POST[LOGIN_KEY] ?>" class="Input" tabindex="1">
			Пароль: (<a href="#">забыл?</a>)
			<input name="<? echo PASSWORD_KEY ?>" type="password" class="Input" tabindex="2">
			<input type="image" src="/img/t/enter.gif" tabindex="2"></form>
<?php

	if (!$user->IsEmpty()) {
		$user->User->TouchSession();
		$user->User->Save();

		echo "Вы авторизованы как<br>
			<b>".$user->User->Login."</b><br>
			и можете <a href='inside.php'>войти</a> в чат.";
	} else {
		if ($_POST[LOGIN_KEY]) {
			echo "Ошибка авторизации!";
		}
	}

?>
			<a href="/register.php">Зарегистрироваться</a>

			<br />
			<br />

			<ul class="Bold">
				<li class="l3"> <a href="/journal/">Журналы</a>
				<li class="l1"> <a href="/forum/">Форум</a>
				<li> <a href="/gallery/">Фотогалерея</a>
				<li class="l1"> <a href="#">Лица чата</a>
				<li> <a href="#">Чёрный список</a>
				<li class="l2"> <a href="#">Рейтинги</a>
				<li class="l1"> <a href="#">Фамильное древо</a>
			</ul>


			<br />
			<br />

		
		</div>

		<div id="center">
Добро пожаловать в старейший саратовский чат &laquo;Безумное ЧАепиТие у Мартовского Зайца&raquo;!

<p>Здесь вы сможете встретить интересных людей, пообщаться на интересующие вас темы и просто приятно провести время.

<p>Если вы у нас впервые, ознакомьтесь с <a href="#">правилами</a> и <a href="/register.php">зарегистрируйтесь</a>. Если вы уже бывали здесь раньше - просто введите свой логин и пароль.</p>


			<br />

			<img src="/img/t/journals.gif" />
			<?php include $root."inc/ui_parts/journal.posts.php"; ?>


 		</div>
	</div>

	<div id="middle" class="Block Center">
		&copy; 1999 Создание и поддержка сайта - Николай Богданов.
	</div>
</body>