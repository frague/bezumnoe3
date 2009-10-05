<?php

	$root = "../";
	require_once $root."server_references.php";

	$user = GetAuthorizedUser(true);
	SetUserSessionCookie($user->User);

	require $root."inc/ui_parts/news.php";

	require_once $root."references.php";

?><html>
<head>
	<title>Лучший саратовский чат &mdash; Безумное ЧАепиТие у Мартовского Зайца</title>
</head>

<style>
	* {
		margin:0;
		padding:0;
	}
	body, td, th, div {
		font-family:arial;
		font-size:10pt;
	}
	div {
		/*border:dashed 1px #b0b0b0;*/
	}
	h3 {
		font-size:10pt;
	}
	h4 {
		font-size:9pt;
	}
	#left {
		float: left;
		width:25%;
		padding-right:4px;
	}

	#right {
		float: right;
		width:25%;
		padding-left:4px;
	}
	#middle {
		clear:both;
	}

	#center {
		margin: 0 25%;
	}

	.Block {
		width:90%;
		margin:0 auto;
		clear:both;
		padding:4px;
	}
	ul {
		margin-left:20px;
	}
	.fLeft {
		float:left;
	}
	.Center {
		text-align:center;
	}
	.Input {
		display:block;
		clear:both;
		width:120px;
	}
	p {
		margin-top:10px;
	}
	.News ul {
		margin-left:0;
		font-size:90%;
	}
	.News ul, .News ul li {
		list-style:none;
	}
	ul.Hbd {
		list-style:none;
		margin-left:0;
		font-size:90%;
	}
	ul.Hbd li, .Rooms ul li {
		display:inline;
	}
	.Rooms ul {
		margin-left:0;
	}
	.Rooms ul li {
		list-style:none;
		font-size:90%;
	}
	ul li {
		list-style-image: url(/3/img/li/1.gif);
	}
	ul li.l1 {
		list-style-image: url(/3/img/li/2.gif);
		margin-left:2px;
	}
	ul li.l2 {
		list-style-image: url(/3/img/li/3.gif);
		margin-left:1px;
	}
	ul li.l3 {
		list-style-image: url(/3/img/li/4.gif);
		margin-left:-1px;
	}
	.Error {
		color:red;
	}
</style>

<body>
	<div class="Block">
		<div id="left" class="News">
			<h3>Новости</h3>
			<?php ShowNews(-2, 5); ?>
		</div>

		<div id="right" class="Rooms">
			<h3>Сейчас в чате:</h3>
			<?php include $root."inc/ui_parts/online_users.php"; ?>
		</div>

		<div id="center">
Добро пожаловать в старейший саратовский чат &laquo;Безумное ЧАепиТие у Мартовского Зайца&raquo;!

<p>Здесь вы сможете встретить интересных людей, пообщаться на интересующие вас темы и просто приятно провести время.

<p>Если вы у нас впервые, ознакомьтесь с <a href="#">правилами</a> и <a href="/3/prototype/register.php">зарегистрируйтесь</a>. Если вы уже бывали здесь раньше - просто введите свой логин и пароль.

<p>Чат функционирует при поддержке <a href="http://www.sgu.ru">СГУ</a>.
 		</div>
	</div>

	<!--div class="Block Center">
		<img src="/3/img/t/hline.gif" />
	</div-->

	<div class="Block">
		<div id="left" style="padding-top:20px">
			<ul>
				<li class="l3"> <a href="/3/journal/">Журналы</a>
				<li class="l1"> <a href="/3/forum/">Форум</a>
				<li> <a href="/3/gallery/">Фотогалерея</a>
				<li class="l1"> <a href="#">Лица чата</a>
				<li> <a href="#">Чёрный список</a>
				<li class="l2"> <a href="#">Рейтинги</a>
				<li class="l1"> <a href="#">Фамильное древо</a>
			</ul>
		</div>

		<div id="right" style="padding-top:20px">
			<form method="POST">
			Логин:
			<input name="<? echo LOGIN_KEY ?>" value="<? echo $_POST[LOGIN_KEY] ?>" class="Input" tabindex="1">
			Пароль: (<a href="#">забыл?</a>)
			<input name="<? echo PASSWORD_KEY ?>" type="password" class="Input" tabindex="2">
			<input type="image" src="/3/img/t/submit.gif" tabindex="2"></form>
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
			<a href="/3/prototype/register.php">Зарегистрироваться</a>
		</div>

		<div id="center" class="Center" style="vertical-align:middle">
			<img src="/3/img/t/logo.gif" />
			<img src="/3/img/t/title.gif" align="bottom" style="margin-bottom:20px" />
			<p>Чат основан в 1999 году.
		</div>
	</div>

	<!--div class="Block Center">
		<img src="/3/img/t/hline.gif" />
	</div-->

	<div class="Block">
		<div id="left">
			<h3>Новые фото:</h3>
		</div>

		<div id="right">
			<h3>Дни рождения:</h3>
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
		</div>

		<div id="center">
			<h3>В форуме:</h3>
			<h3>В журналах:</h3>
			<?php include $root."inc/ui_parts/journal.posts.php"; ?>
		</div>
	</div>
	<div id="middle" class="Block Center">
		&copy; 1999 Создание и поддержка сайта - Николай Богданов.
	</div>
</body>