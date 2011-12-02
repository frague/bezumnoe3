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
	<!-- ����������� ��������� ��� ������� ����� ��������� �������, ���������� ����-������, �����, ���������� � �������� ���� �������� ����������� ��� ������ ��� �������� ������� ��� ���������� ��� ���������� ��������� ����� �������� ����� ��������� ��� �������� -->
	<title>������ ����������� ��� &mdash; �������� �������� � ����������� �����</title>
	<meta name="description" content="���������� ��� �������� �������� � ����������� �����. �������, ������� ��� ������� � ��������� ��������. ���������� ����������� ����-������. ���������� � ����������� ����. ����������� ��������, �������, ����������, ������� � ����� ����� �� ���������.">
	<meta name="abstract" content="���������� ��� �������� �������� � ����������� �����. �������, ������� ��� ������� � ��������� ��������. ���������� ����������� ����-������. ���������� � ����������� ����. ����������� ��������, �������, ����������, ������� � ����� ����� �� ���������.">
	<meta name="keywords" content="���, ��������, �������, �����������, � ��������, ����, ����-������, �����, ���������, ������������, �������, ��������, �������, ����������, �������, �����, ����������, ��������������, ����, �������, ��������, saratov, chat, blogs, blog-service">
	<meta name="distribution" content="Global">
	<meta name="rating" content="General">
	<meta name="subject" content="���, �������, �����, ����-������">
	<meta name="page-type" content="���, �������, �����, ����-������">
	<meta name="page-topic" content="���������� ����������� ��� bezumnoe.ru: ������������ ���������� �������, ���������� � ��������">
	<meta name="title" content="������������ ��� � �������� - bezumnoe.ru: ������������ ���������� �������, ���������� � ��������">
	<meta name="site-created" content="17-10-1999">
	<meta name="revisit" content="1 day">
	<meta name="revisit-after" content="1 day">
	<meta name="content-Language" content="russian">
	<meta name="resource-type" content="document">
	<meta name="audience" content="all">
	<meta name="robots" content="index,all">
	<meta name="Address" content="Saratov, Russia">
	<meta name="home_url" content="http://bezumnoe.ru/">
	<!-- ����������� ��������� ��� ������� ����� ��������� �������, ���������� ����-������, �����, ���������� � �������� ���� �������� ����������� ��� ������ ��� �������� ������� ��� ���������� ��� ���������� ��������� ����� �������� ����� ��������� ��� �������� -->
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
							<h5>������ � ����:</h5>
							<?php include $root."inc/ui_parts/online_users.php"; ?>
						</div>

						<div class="News">
							<h5>�������</h5>
							<?php ShowNews(-1, 3); ?>
						</div>

						<div class="Birthdays">
							<h5>��� ��������</h5>
							<?php include $root."inc/ui_parts/birthdays.php"; ?>
						</div>

						<div class="UserList Photos">
							<h5>����� ����</h5>
							<?php include $root."inc/ui_parts/new_photos.php"; ?>
						</div>
					</div>

					<div class="Column Center Left">
						<div class="Divider Vertical">
							<div class="Welcome">
<p style="font-size:102%">����� ���������� � ��������� ����������� ��� &laquo;<b>�������� �������� � ����������� �����</b>&raquo;!
<p>����� �� ������� ��������� ���������� �����, ���������� �� ������������ ��� ���� � ������ ������� �������� �����. 
<p>���� �� � ��� �������, ������������ � <a href="/rules/" class="Link">���������</a> � <a href="/register" class="Link">�����������������</a>. ���� �� ��� ������ ����� ������ - ������ ������� ���� ����� � ������ � �������!
							</p></div>

							<div class="MainLink Register">
								<img alt="����������� � ����" title="����������� � ����" src="/img/t/pict/register.gif" align="left" height="69" width="70">
								<a href="/register" class="NoBorder">
									<h4>�����������</h4></a>
								<p>�������������������, �� ��������� ������ � ����, ������� � ������������� �������.
								<span class="Newcomers">
									<h6>��������� �������������������� ������������</h6>
									<?php include $root."inc/ui_parts/newcomers.php"; ?>
								</span>
							</div>

							<div class="MainLink RightOriented Forum">
								<img src="/img/t/pict/forum.gif" align="right" height="79" width="95" alt="������" title="������">
								<a href="/forum/" class="NoBorder">
									<h4>�����</h4></a>
								<p>� ������� ���������� ���������� ���� �������� ����, ����������� ���������� � ������������. ������������� ��� ���������� ���������.
							</div>

							<div class="MainLink Gallery UserList">
								<img src="/img/t/pict/photo.gif" align="left" height="66" width="70" alt="�����������" title="�����������">
								<a href="/gallery/" class="NoBorder">
									<h4>�����������</h4></a>
								<p>�� �������� �� ������ � ���������! � ���� ���������� ���� ��������, ���������, ������� �� �������� ������, ��� �� ������ ����������� ��� ������. <a href="/gallery/" class="Link">����������</a> ���������� � ���� �������!
								<h6>��������� �����������:</h6>
								<?php 
									$shownComments = 1;
									include $root."inc/ui_parts/gallery.comments.php"; 
								?>
							</div>

							<div class="MainLink RightOriented Blogs UserList">
								<img src="/img/t/pict/journals.gif" align="right" width="70" height="64" alt="������� (�����)" title="������� (�����)">
								<a href="/journal/" class="NoBorder">
									<h4>������� (�����)</h4></a>
								<p>����� �� �������� ���������� �������� ���� �������� ������ ������������ ������ (��������). ������ ������� ���������� � �������������� ��� ����� ���.
								<h6>������ ��������� ������� � ��������:</h6>
								<?php include $root."inc/ui_parts/journal.posts.php"; ?>
							</div>
						</div>
					</div>

					<div class="Column Right">
						<img src="/img/t/logo.gif" height="184" width="187" alt="������ ����������� ���" title="������ ����������� ���" >
						<img alt="����������� ���" title="����������� ���" src="/img/t/title.gif" height="126" width="187">

						<form method="POST"><input type="hidden" name="AUTH" id="AUTH" value="1" />
							<table cellpadding="0" cellspacing="0">
								<tr>
									<td class="FormTitle">�����</td>
									<td width="100%"><input name="<?php echo LOGIN_KEY ?>" value="<? echo $_POST[LOGIN_KEY] ?>" size="10"></td></tr>
								<tr>
									<td class="FormTitle">������</td>
									<td><input name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" value="" size="10" type="password">
									<!--script>$("<?php echo PASSWORD_KEY ?>").focus()</script--></td></tr>
								<tr>
									<td></td>
									<td><input src="/img/t/auth.gif" border="0" width="90" height="30" alt="�������������� � ����" type="image" style="margin-top:5px" /></td></tr></table>
						</form>
<?php

	if (!$user->IsEmpty()) {
		$user->User->TouchSession();
		$user->User->Save();

		echo "<div class='Auth'>�� ������������ ���<br>
			<b>".$user->User->Login."</b><br>
			� ������ <a href='/inside.php'>�����</a> � ���.</div>";
	} else {
		if ($_POST[LOGIN_KEY]) {
			echo "<div class='Error'>������ �����������!</div>";
		}
	}

?>

						<div class="UserList">
							<nav>
								<ul class="Menu" role="navigation sitemap">
									<li style="margin-bottom:10px"> <a href="/forgot/">����� ������?</a>

									<li> <a href="/register">�����������</a>
									<li class="l1 Bold"> <a href="/journal/">�������</a>
									<li class="l4"> <a href="/tree/" class="Red">��������� �����</a>
									<li> <a href="/gallery/">�����������</a>
									<li class="l2"> <a href="/forum/">������ ����</a>
									<li class=""> <a href="/photos/">����� ������</a>
									<li class="l3"> <a href="/banned/">&laquo;׸���� ������&raquo;</a>
									<li class="l1"> <a href="/rating/">�������</a>
							</ul>
							<nav>
						</div>

						<div class="Counters">
							<a href="http://www.yandex.ru/cy?base=0&host=bezumnoe.ru" rel="nofollow"><img src="http://www.yandex.ru/cycounter?www.bezumnoe.ru" width=88 height=31 alt="������ ����������� bezumnoe.RU" border=0 hspace=5 vspace=8></a><br />
							<!--LiveInternet counter--><script type="text/javascript">document.write("<a href='http://www.liveinternet.ru/click' target=_blank><img src='//counter.yadro.ru/hit?t14.5;r" + escape(document.referrer) + ((typeof(screen)=="undefined")?"":";s"+screen.width+"*"+screen.height+"*"+(screen.colorDepth?screen.colorDepth:screen.pixelDepth)) + ";u" + escape(document.URL) +";i" + escape("��"+document.title.substring(0,80)) + ";" + Math.random() + "' border=0 width=88 height=31 alt='' title=''><\/a>")</script><!--/LiveInternet-->
						</div>
					</div>
				</div>

				<div style="clear: both;" class="Divider Horizontal"><span></span></div>

				<?php include $root."inc/ui_parts/rle_banner.php"; ?>

				<div class="Divider Horizontal Alternative"><span></span></div>
				
				<div class="Bottom Centered">
					������ ������ ����� ���� ������� 19 ������� 1999 ����<br />
					&copy; ������ � ���������� ����� - <a href="javascript:void(0)" onclick="Feedback()">������� ��������</a>
				</div>
			</div>
	   	</div>
	</div>

<?php

	include $root."/inc/li_spider_check.inc.php";

?>
<!-- ����������� ��������� ��� ������� ����� ��������� �������, ���������� ����-������, �����, ���������� � �������� ���� �������� ����������� ��� ������ ��� �������� ������� ��� ���������� ��� ���������� ��������� ����� �������� ����� ��������� ��� �������� -->
</body>
</html>