<?php

	$root = "./";
	require_once $root."server_references.php";

	$user = GetAuthorizedUser(true);
	SetUserSessionCookie($user->User);

	require $root."inc/ui_parts/templates.php";
	require $root."inc/ui_parts/news.php";

	#require_once $root."references.php";

?><!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="windows-1251" />
	<!-- Саратовский молодёжный чат общение среди студентов молодёжи, популярный блог-сервис, блоги, знакомства в Саратове чаты Саратова Саратовский чат лучший чат Саратова быстрый чат прикольный чат початиться поболтать Волга Волжский самый известный чат поволжья -->
	<title>Лучший саратовский чат &mdash; Безумное ЧАепиТие у Мартовского Зайца</title>
	<meta name="description" content="Молодежный чат Безумное ЧАепиТие у Мартовского Зайца. Быстрый, удобный чат молодёжи и студентов Саратова. Популярный саратовский блог-сервис. Фотографии с мероприятий чата. Многолетние традиции, встречи, знакомства, общение в своей среде по интересам.">
	<meta name="abstract" content="Молодежный чат Безумное ЧАепиТие у Мартовского Зайца. Быстрый, удобный чат молодёжи и студентов Саратова. Популярный саратовский блог-сервис. Фотографии с мероприятий чата. Многолетние традиции, встречи, знакомства, общение в своей среде по интересам.">
	<meta name="keywords" content="чат, безумное, Саратов, саратовский, в Саратове, блог, блог-сервис, блоги, молодёжный, студенческий, студент, чаепитие, встречи, знакомства, общение, волга, российский, общероссийский, чаты, Саратов, Саратова, saratov, chat, blogs, blog-service">
	<meta name="distribution" content="Global">
	<meta name="rating" content="General">
	<meta name="subject" content="Чат, Общение, Блоги, Блог-сервис">
	<meta name="page-type" content="Чат, Общение, Блоги, Блог-сервис">
	<meta name="page-topic" content="Молодежный саратовский чат bezumnoe.ru: неформальное молодежное общение, знакомства в Саратове">
	<meta name="title" content="Студенческий чат в Саратове - bezumnoe.ru: неформальное молодежное общение, знакомства в Саратове">
	<meta name="site-created" content="17-10-1999">
	<meta name="revisit" content="1 day">
	<meta name="revisit-after" content="1 day">
	<meta name="content-Language" content="russian">
	<meta name="resource-type" content="document">
	<meta name="audience" content="all">
	<meta name="robots" content="index,all">
	<meta name="Address" content="Saratov, Russia">
	<meta name="home_url" content="http://bezumnoe.ru/">
	<!-- Саратовский молодёжный чат общение среди студентов молодёжи, популярный блог-сервис, блоги, знакомства в Саратове чаты Саратова Саратовский чат лучший чат Саратова быстрый чат прикольный чат початиться поболтать Волга Волжский самый известный чат поволжья -->
	<link rel="stylesheet" href="/css/global.css" />
	<link rel="stylesheet" href="/css/template_layout.css" />
	<link rel="stylesheet" href="/css/default3.css" />
<!--[if IE]>	<link rel="stylesheet" type="text/css" href="/css/ie.css" /><![endif]-->
	<link rel="icon" href="/img/icons/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="/img/icons/favicon.ico" type="image/x-icon">
	<?php include $root."/inc/ui_parts/google_analythics.php"; ?>
	<script language="javascript" src="/js1/modernizr.js"></script>
	<script language="javascript" src="/js1/common.js"></script>
</head>

<body>
	<div class="MainLeft">
		<div class="MainRight" align="center">
			<div class="Main">
				<div>
					<div class="Column Left">
						<div class="UserList">
							<h5>Сейчас в чате:</h5>
							<?php include $root."inc/ui_parts/online_users.php"; ?>
						</div>

						<div class="News">
							<h5>Новости</h5>
							<?php ShowNews(-1, 3); ?>
						</div>

						<div class="Birthdays">
							<h5>Дни рождения</h5>
							<?php include $root."inc/ui_parts/birthdays.php"; ?>
						</div>

						<div class="UserList Photos">
							<h5>Новые лица</h5>
							<?php include $root."inc/ui_parts/new_photos.php"; ?>
						</div>
					</div>

					<div class="Column Center Left">
						<div class="Divider Vertical">
							<div class="Welcome">
<p style="font-size:102%">Добро пожаловать в старейший саратовский чат &laquo;<b>Безумное ЧАепиТие у Мартовского Зайца</b>&raquo;!
<p>Здесь вы сможете встретить интересных людей, пообщаться на интересующие вас темы и просто приятно провести время. 
<p>Если вы у нас впервые, ознакомьтесь с <a href="/rules/" class="Link">правилами</a> и <a href="/register" class="Link">зарегистрируйтесь</a>. Если вы уже бывали здесь раньше - просто введите свой логин и пароль и входите!
							</p></div>

							<div class="MainLink Register">
								<img alt="Регистрация в чате" title="Регистрация в чате" src="/img/t/pict/register.gif" align="left" height="69" width="70">
								<a href="/register" class="NoBorder">
									<h4>Регистрация</h4></a>
								<p>Зарегистрировавшись, вы получаете доступ к чату, форумам и персональному журналу.
								<span class="Newcomers">
									<h6>Последние зарегистрировавшиеся пользователи</h6>
									<?php include $root."inc/ui_parts/newcomers.php"; ?>
								</span>
							</div>

							<div class="MainLink RightOriented Forum">
								<img src="/img/t/pict/forum.gif" align="right" height="79" width="95" alt="Форумы" title="Форумы">
								<a href="/forum/" class="NoBorder">
									<h4>Форум</h4></a>
								<p>В форумах происходит обсуждение всех вопросов чата, публикуются объявления и поздравления. Авторизуйтесь для добавления сообщений.
							</div>

							<div class="MainLink Gallery UserList">
								<img src="/img/t/pict/photo.gif" align="left" height="66" width="70" alt="Фотогалерея" title="Фотогалерея">
								<a href="/gallery/" class="NoBorder">
									<h4>Фотогалерея</h4></a>
								<p>Мы общаемся не только в интернете! В чате существуют свои традиции, праздники, которые мы отмечаем вместе, или же просто встречаемся без повода. <a href="/gallery/" class="Link">Посмотрите</a> фотографии с мест событий!
								<h6>Последний комментарий:</h6>
								<?php 
									$shownComments = 1;
									include $root."inc/ui_parts/gallery.comments.php"; 
								?>
							</div>

							<div class="MainLink RightOriented Blogs UserList">
								<img src="/img/t/pict/journals.gif" align="right" width="70" height="64" alt="Журналы (блоги)" title="Журналы (блоги)">
								<a href="/journal/" class="NoBorder">
									<h4>Журналы (блоги)</h4></a>
								<p>Одним из наиболее популярных разделов чата является раздел персональных блогов (журналов). Многие журналы существуют и поддерживаются уже много лет.
								<h6>Список последних записей в журналах:</h6>
								<?php include $root."inc/ui_parts/journal.posts.php"; ?>
							</div>
						</div>
					</div>

					<div class="Column Right">
						<img src="/img/t/logo.gif" height="184" width="187" alt="Лучший саратовский чат" title="Лучший саратовский чат" >
						<img alt="Саратовский чат" title="Саратовский чат" src="/img/t/title.gif" height="126" width="187">

						<form method="POST"><input type="hidden" name="AUTH" id="AUTH" value="1" />
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td class="FormTitle">Логин</td>
									<td width="100%"><input name="<?php echo LOGIN_KEY ?>" value="<? echo $_POST[LOGIN_KEY] ?>" size="10"></td></tr>
								<tr>
									<td class="FormTitle">Пароль</td>
									<td><input name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" value="" size="10" type="password">
									<!--script>$("<?php echo PASSWORD_KEY ?>").focus()</script--></td></tr>
								<tr>
									<td></td>
									<td><input src="/img/t/auth.gif" border="0" width="90" height="30" alt="Авторизоваться в чате" type="image" style="margin-top:5px" /></td></tr></table>
						</form>
<?php

	if (!$user->IsEmpty()) {
		$user->User->TouchSession();
		$user->User->Save();

		echo "<div class='Auth'>Вы авторизованы как<br>
			<b>".$user->User->Login."</b><br>
			и можете <a href='/inside.php'>войти</a> в чат.</div>";
	} else {
		if ($_POST[LOGIN_KEY]) {
			echo "<div class='Error'>Ошибка авторизации!</div>";
		}
	}

?>

						<div class="UserList">
							<nav>
								<ul class="Menu" role="navigation sitemap">
									<li style="margin-bottom:10px"> <a href="/forgot/">Забыл пароль?</a>

									<li> <a href="/register">Регистрация</a>
									<li class="l1 Bold"> <a href="/journal/">Журналы</a>
									<li class="l4"> <a href="/tree/" class="Red">Фамильное древо</a>
									<li> <a href="/gallery/">Фотогалерея</a>
									<li class="l2"> <a href="/forum/">Форумы чата</a>
									<li class=""> <a href="/photos/">Фотки чатлан</a>
									<li class="l3"> <a href="/banned/">&laquo;Чёрный список&raquo;</a>
									<li class="l1"> <a href="/rating/">Рейтинг</a>
							</ul>
							<nav>
						</div>

						<div class="Counters">
							<a href="http://www.yandex.ru/cy?base=0&host=bezumnoe.ru" rel="nofollow"><img src="http://www.yandex.ru/cycounter?www.bezumnoe.ru" width=88 height=31 alt="Яндекс цитирования bezumnoe.RU" border=0 hspace=5 vspace=8></a><br />
							<!--LiveInternet counter--><script type="text/javascript">document.write("<a href='http://www.liveinternet.ru/click' target=_blank><img src='//counter.yadro.ru/hit?t14.5;r" + escape(document.referrer) + ((typeof(screen)=="undefined")?"":";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?screen.colorDepth:screen.pixelDepth)) + ";u" + escape(document.URL) +";i" + escape("Жж"+document.title.substring(0,80)) + ";" + Math.random() + "' border=0 width=88 height=31 alt='' title=''><\/a>")</script><!--/LiveInternet-->
						</div>
					</div>
				</div>

				<div style="clear: both;" class="Divider Horizontal"><span></span></div>

				<?php include $root."inc/ui_parts/rle_banner.php"; ?>

				<div class="Divider Horizontal Alternative"><span></span></div>
				
				<div class="Bottom Centered">
					Первая версия сайта была создана 19 октября 1999 года<br />
					&copy; Дизайн и разработка сайта - <a href="javascript:void(0)" onclick="Feedback()">Николай Богданов</a>
				</div>
			</div>
	   	</div>
	</div>

<?php

	include $root."/inc/li_spider_check.inc.php";

?>
<!-- Саратовский молодёжный чат общение среди студентов молодёжи, популярный блог-сервис, блоги, знакомства в Саратове чаты Саратова Саратовский чат лучший чат Саратова быстрый чат прикольный чат початиться поболтать Волга Волжский самый известный чат поволжья -->
</body>
</html>