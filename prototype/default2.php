<?php

	$root = "../";
	require_once $root."server_references.php";

	$user = GetAuthorizedUser(true);
	SetUserSessionCookie($user->User);

	require $root."inc/ui_parts/news.php";

	require_once $root."references.php";

?><html>
<head>
	<title>������ ����������� ��� &mdash; �������� �������� � ����������� �����</title>
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
			<h3>�������</h3>
			<?php ShowNews(-2, 5); ?>
		</div>

		<div id="right" class="Rooms">
			<h3>������ � ����:</h3>
			<?php include $root."inc/ui_parts/online_users.php"; ?>
		</div>

		<div id="center">
����� ���������� � ��������� ����������� ��� &laquo;�������� �������� � ����������� �����&raquo;!

<p>����� �� ������� ��������� ���������� �����, ���������� �� ������������ ��� ���� � ������ ������� �������� �����.

<p>���� �� � ��� �������, ������������ � <a href="#">���������</a> � <a href="/3/prototype/register.php">�����������������</a>. ���� �� ��� ������ ����� ������ - ������ ������� ���� ����� � ������.

<p>��� ������������� ��� ��������� <a href="http://www.sgu.ru">���</a>.
 		</div>
	</div>

	<!--div class="Block Center">
		<img src="/3/img/t/hline.gif" />
	</div-->

	<div class="Block">
		<div id="left" style="padding-top:20px">
			<ul>
				<li class="l3"> <a href="/3/journal/">�������</a>
				<li class="l1"> <a href="/3/forum/">�����</a>
				<li> <a href="/3/gallery/">�����������</a>
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
			<input type="image" src="/3/img/t/submit.gif" tabindex="2"></form>
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
			<a href="/3/prototype/register.php">������������������</a>
		</div>

		<div id="center" class="Center" style="vertical-align:middle">
			<img src="/3/img/t/logo.gif" />
			<img src="/3/img/t/title.gif" align="bottom" style="margin-bottom:20px" />
			<p>��� ������� � 1999 ����.
		</div>
	</div>

	<!--div class="Block Center">
		<img src="/3/img/t/hline.gif" />
	</div-->

	<div class="Block">
		<div id="left">
			<h3>����� ����:</h3>
		</div>

		<div id="right">
			<h3>��� ��������:</h3>
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
			<h3>� ������:</h3>
			<h3>� ��������:</h3>
			<?php include $root."inc/ui_parts/journal.posts.php"; ?>
		</div>
	</div>
	<div id="middle" class="Block Center">
		&copy; 1999 �������� � ��������� ����� - ������� ��������.
	</div>
</body>