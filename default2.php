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
	<title>������ ����������� ��� &mdash; �������� �������� � ����������� �����</title>
	<link rel="stylesheet" type="text/css" href="/css/global.css" />
	<link rel="stylesheet" type="text/css" href="/css/default2.css" />
</head>

<style>
</style>

<body>
	<div class="Block">
		<div id="left" class="News">
			<img src="/img/t/news.gif" />
			<?php ShowNews(-1, 5); ?>
		</div>

		<div id="right" class="Rooms">
			<img src="/img/t/online.gif" />
			<?php include $root."inc/ui_parts/online_users.php"; ?>
		</div>

		<div id="center">
����� ���������� � ��������� ����������� ��� &laquo;�������� �������� � ����������� �����&raquo;!

<p>����� �� ������� ��������� ���������� �����, ���������� �� ������������ ��� ���� � ������ ������� �������� �����.

<p>���� �� � ��� �������, ������������ � <a href="#">���������</a> � <a href="/register.php">�����������������</a>. ���� �� ��� ������ ����� ������ - ������ ������� ���� ����� � ������.
 		</div>
	</div>

	<!--div class="Block Center">
		<img src="/3/img/t/hline.gif" />
	</div-->

	<div class="Block">
		<div id="left" style="padding-top:20px">
			<ul class="Bold">
				<li class="l3"> <a href="/journal/">�������</a>
				<li class="l1"> <a href="/forum/">�����</a>
				<li> <a href="/gallery/">�����������</a>
				<li class="l1"> <a href="#">���� ����</a>
				<li> <a href="#">׸���� ������</a>
				<li class="l2"> <a href="#">��������</a>
				<li class="l1"> <a href="#">��������� �����</a>
			</ul>
		</div>

		<div id="right" style="padding-top:20px">
			<form method="POST">
			�����:
			<input name="<? echo LOGIN_KEY ?>" value="<? echo $_POST[LOGIN_KEY] ?>" class="Input" tabindex="1">
			������: (<a href="#">�����?</a>)
			<input name="<? echo PASSWORD_KEY ?>" type="password" class="Input" tabindex="2">
			<input type="image" src="/img/t/enter.gif" tabindex="2"></form>
<?php

	if (!$user->IsEmpty()) {
		$user->User->TouchSession();
		$user->User->Save();

		echo "�� ������������ ���<br>
			<b>".$user->User->Login."</b><br>
			� ������ <a href='inside.php'>�����</a> � ���.";
	} else {
		if ($_POST[LOGIN_KEY]) {
			echo "������ �����������!";
		}
	}

?>
			<a href="/register.php">������������������</a>
		</div>

		<div id="center" class="Center" style="vertical-align:middle">
			<img src="/img/t/logo.gif" />
			<img src="/img/t/title.gif" align="bottom" style="margin-bottom:20px" />
		</div>
	</div>

	<!--div class="Block Center">
		<img src="/3/img/t/hline.gif" />
	</div-->

	<div class="Block">
		<div id="left">
			<img src="/img/t/photo.gif" />
		</div>

		<div id="right">
			<img src="/img/t/birthdays.gif" />
			<ul class="Hbd"> <h4>�������</h4>
				<li> <a href="#">���������� ����</a>,
				<li> <a href="#">���������� ����</a>,
				<li> <a href="#">���������� ����</a>,
				<li> <a href="#">���������� ����</a>
			</ul>
			<ul class="Hbd"> <h4>������</h4>
				<li> <a href="#">���������� ����</a>,
				<li> <a href="#">���������� ����</a>,
				<li> <a href="#">���������� ����</a>,
				<li> <a href="#">���������� ����</a>
			</ul>
			<ul class="Hbd"> <h4>�����������</h4>
				<li> <a href="#">���������� ����</a>,
				<li> <a href="#">���������� ����</a>,
				<li> <a href="#">���������� ����</a>,
				<li> <a href="#">���������� ����</a>
			</ul>
		</div>

		<div id="center">
			<img src="/img/t/journals.gif" />
			<?php include $root."inc/ui_parts/journal.posts.php"; ?>
		</div>
	</div>
	<div id="middle" class="Block Center">
		&copy; 1999 �������� � ��������� ����� - ������� ��������.
	</div>
</body>