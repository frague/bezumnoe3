<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/css/global.css">
		<link rel="stylesheet" type="text/css" href="/css/options.css">
		<?

	$root = "../";
	require_once $root."server_references.php";

	$user = GetAuthorizedUser(true);

	require_once $root."references.php"; 
	

	?>		<title>���� ������������</title>
		<script language="javascript" src="/js1/wysiwyg/tiny_mce.js"></script>
	</head>

	<body onload="OnLoad()">
		<div id="AlertContainer">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
				<tr><td align="center" valign="middle">
					<div id="AlertBlock">
					</div>
				</td></tr></table></div>

		<div id="OptionsContainer">
			<div id="OptionsContent">
		<? 
			if ($user->IsEmpty()) {
		?>
				<div class="Error">������������ �� �����������!</div>
		<?
			} else {
				echo "<h1>���� ������������ ".$user->User->Login."</h1>";
			}
		?>
			</div>
	   	</div>
		<script src="/js1/options.js"></script>
	</body>
</html>