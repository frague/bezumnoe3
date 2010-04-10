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
	<!-- Саратовский молодёжный чат общение среди студентов молодёжи знакомства в Саратове чаты Саратова Саратовский чат лучший чат Саратова быстрый чат прикольный чат початиться поболтать Волга Волжский самый известный чат поволжья -->
	<title>Лучший саратовский чат &mdash; Безумное ЧАепиТие у Мартовского Зайца</title>
	<meta name="description" content="Молодежный чат bezumnoe.ru. Скоростной, удобный чат студентов Саратова. Многолетние традиции, встречи, знакомства, общение в своей среде по интересам. Удобный, современный механизм, журналы пользователей, форум, фотогалерея."><meta name="abstract" content="Молодежный чат bezumnoe.ru. Скоростной, удобный чат студентов Саратова. Многолетние традиции, встречи, знакомства, общение в своей среде по интересам. Удобный, современный механизм, журналы пользователей, форум, фотогалерея."><meta name="keywords" content="чат, безумное, Саратов, саратовский, молодёжный, студенческий, студент, чаепитие, встречи, знакомства, общение, волга, российский, общероссийский, чаты, Саратов, Саратова, saratov, chat"><meta name="distribution" content="Global"><meta name="rating" content="General"><meta name="subject" content="Чат, Общение"><meta name="page-type" content="Чат, Общение"><meta name="page-topic" content="Молодежный саратовский чат bezumnoe.ru: неформальное молодежное общение, знакомства в Саратове"><meta name="title" content="Студенческий чат в Саратове - bezumnoe.ru: неформальное молодежное общение, знакомства в Саратове"><meta name="site-created" content="17-10-1999"><meta name="revisit" content="15 days"><meta name="revisit-after" content="15 days"><meta name="content-Language" content="russian"><meta name="resource-type" content="document"><meta name="audience" content="all"><meta name="robots" content="index,all"><meta name="Address" content="Saratov, Russia"><meta name="home_url" content="http://bezumnoe.ru/">
	<meta http-equiv="content-type" content="text/html; charset=windows-1251">
	<!-- Саратовский молодёжный чат общение среди студентов молодёжи знакомства в Саратове чаты Саратова Саратовский чат лучший чат Саратова быстрый чат прикольный чат початиться поболтать Волга Волжский самый известный чат поволжья -->
	<link rel="stylesheet" type="text/css" href="/css/template.css" />
	<link rel="stylesheet" type="text/css" href="/css/default3.css" />
	<link rel="stylesheet" type="text/css" href="/css/global.css" />
</head>

<body>
	<div class="MainLeft">
		<div class="MainRight" align="center">
			<div class="Main">
				<div>
					<div class="Column Left">
						<!--h3>Сейчас в чате:</h3-->
						<img src="/img/titles/online.gif" style="margin-top:20px" />
						<div class="UserList">
							<?php include $root."inc/ui_parts/online_users.php"; ?>
						</div>

						<img src="/img/titles/news.gif" style="margin-top:20px" />
						<!--h3>Новости чата:</h3-->
						<div class="News">
							<?php ShowNews(-1, 3); ?>
						</div>

						<!--h3>Дни рождения:</h3-->
						<img src="/img/titles/birthdays.gif" style="margin-top:20px" />
						<div class="Birthdays">
							<?php include $root."inc/ui_parts/birthdays.php"; ?>
						</div>

						<!--h3>Новые фотки:</h3-->
						<img src="/img/titles/photos.gif" style="margin:20px 15px 10px" />
						<div class="UserList Photos">
							<?php include $root."inc/ui_parts/new_photos.php"; ?>
						</div>

					</div>

					<div class="Column Center Left">
						<div class="Divider Vertical">
							<div class="Welcome">
<p style="font-size:102%">Добро пожаловать в старейший саратовский чат &laquo;<b>Безумное ЧАепиТие у Мартовского Зайца</b>&raquo;!
<p>Здесь вы сможете встретить интересных людей, пообщаться на интересующие вас темы и просто приятно провести время. 
<p>Если вы у нас впервые, ознакомьтесь с <a href="/rules.php" class="Link">правилами</a> и <a href="/register.php" class="Link">зарегистрируйтесь</a>. Если вы уже бывали здесь раньше - просто введите свой логин и пароль и входите!
							</p></div>

							<div class="MainLink">
								<img alt="Саратовский чат" title="Саратовский чат" src="/img/t/pict/register.gif" align="left" height="69" width="70">
								<a href="/register.php"><img src="/img/titles/register.gif" /></a>
								<p>Зарегистрировавшись, вы получаете доступ к чату, форумам и персональному журналу.
								<span class="Newcomers">Последние зарегистрировавшиеся пользователи: <?php include $root."inc/ui_parts/newcomers.php"; ?></span>
							</div>

							<div class="MainLink RightOriented">
								<img src="/img/t/pict/forum.gif" align="right" height="79" width="95" alt="Форум" title="Форум">
								<a href="/forum/"><img src="/img/titles/forums.gif" /></a>
								<p>В форумах происходит обсуждение насущных вопросов чата. Авторизуйтесь для публикации сообщений.
							</div>

							<div class="MainLink">
								<img src="/img/t/pict/photo.gif" align="left" height="66" width="70" alt="Фотогалерея" title="Фотогалерея">
								<a href="/gallery/"><img src="/img/titles/gallery.gif" /></a>
								<p>Мы общаемся не только в интернете! В чате существуют свои традиции, праздники, которые мы отмечаем вместе, или же просто встречаемся без повода. <a href="/gallery/" class="Link">Посмотрите</a> фотографии с мест событий!
							</div>

							<div class="MainLink RightOriented Blogs UserList">
								<img src="/img/t/pict/journals.gif" align="right" width="70" height="64" alt="Популярные блоги" title="Популярные блоги">
								<a href="/journal/"><img src="/img/titles/journals.gif" /></a>
								<p>Одним из наиболее популярных разделов чата является раздел персональных блогов (журналов). Многие журналы существуют и поддерживаются уже много лет.<br />
								Список последних записей в журналах:
								<?php include $root."inc/ui_parts/journal.posts.php"; ?>
							</div>
						</div>
					</div>

					<div class="Column Right">
						<img src="/img/t/logo.gif" height="184" width="187" alt="Саратовский чат" title="Саратовский чат" >
						<img alt="Саратовский чат" title="Саратовский чат" src="/img/t/title.gif" height="126" width="187">

						<form method="POST">
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td class="FormTitle">Логин</td>
									<td width="100%"><input name="<?php echo LOGIN_KEY ?>" value="<? echo $_POST[LOGIN_KEY] ?>" size="10"></td></tr>
								<tr>
									<td class="FormTitle">Пароль</td>
									<td><input name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" value="" size="10" type="password">
									<script>$("<?php echo PASSWORD_KEY ?>").focus()</script></td></tr>
								<tr>
									<td></td>
									<td><input src="/img/t/enter.gif" border="0" width="48" height="26" alt="Авторизоваться в чате" type="image" /></td></tr></table>
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
							<ul class="Menu">
								<li> <a href="#" onclick="alert('Бывает... ==8)))');">Забыл пароль?</a><br /><br />

								<li> <a href="/register.php">Регистрация</a>
								<li class="l1 Bold"> <a href="/journal/">Журналы</a>
								<li class="l4 Inactive"> <a href="#/tree/" class="Red">Фамильное древо</a>
								<li> <a href="/gallery/">Фотогалерея</a>
								<li class="l2"> <a href="/forum/">Форумы чата</a>
								<li class=""> <a href="/photos/">Фотки чатлан</a>
								<li class="l3"> <a href="/banned.php">&laquo;Чёрный список&raquo;</a>
								<li class="l1"> <a href="/rating.php">Рейтинг</a>
							</ul>
						</div>

						<div class="Counters">
							<a href="http://www.yandex.ru/cy?base=0&host=bezumnoe.ru" rel="nofollow"><img src="http://www.yandex.ru/cycounter?www.bezumnoe.ru" width=88 height=31 alt="Яндекс цитирования bezumnoe.RU" border=0 hspace=5 vspace=8></a><br />
							<!--LiveInternet counter--><script language="JavaScript">document.write('<a href="http://www.liveinternet.ru/click" target=_blank rel=nofollow><img src="http://counter.yadro.ru/hit?t21.6;r' + escape(document.referrer) + ((typeof(screen)=='undefined')?'':';s'+screen.width+'*'+screen.height+'*'+(screen.colorDepth?screen.colorDepth:screen.pixelDepth)) + ';u' + escape(document.URL) +';i' + escape('Жж'+document.title.substring(0,80)) + ';' + Math.random() + '" border=0 width=88 height=31 title="LiveInternet: показано число просмотров за 24 часа, посетителей за 24 часа и за сегодн\я"></a>')</script><!--/LiveInternet-->
						</div>
					</div>
				</div>

				<div style="clear: both;" class="Divider Horizontal"><span></span></div>

				<?php include $root."inc/ui_parts/rle_banner.php"; ?>

				<div class="Divider Horizontal Alternative"><span></span></div>
				
				<div class="Bottom Centered">
					Первая версия сайта была создана 19 октября 1999 года.<br />
					&copy; Дизайн и разработка сайта - <a href="javascript:void(0)" onclick="mail();">Николай Богданов</a>

					<script>function mail() {document.location='ma'+'ilto:Николай%20Богданов%20<'+'bezumnoe'+'@'+'gma' + 'il.com>';return false;}</script>
				</div>
			</div>
	   	</div>
	</div>

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-309866-1");
pageTracker._trackPageview();
} catch(err) {}</script>

<?php

	include $root."/inc/li_spider_check.inc.php";

?>
<!-- Саратовский чат общение среди студентов молодёжи знакомства в Саратове чаты Саратова Саратовский молодёжный чат лучший чат Саратова быстрый чат прикольный чат початиться поболтать Волга Волжский самый известный чат поволжья -->
</body>
</html>