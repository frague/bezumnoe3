<html>
	<head>
		<link rel="stylesheet" type="text/css" href="/3/css/global.css">
		<link rel="stylesheet" type="text/css" href="/3/css/wakeup.css">
		<?

	$root = "../";
	require_once $root."server_references.php";

	$user = GetAuthorizedUser(true);

	$wakeup_id = round($_GET["id"]);
	$wakeup = new Wakeup();
	if (!$user->IsEmpty() && $wakeup_id > 0) {
		$wakeup->GetForUser($user->User, $wakeup_id);
		if (!$wakeup->IsEmpty() && !$wakeup->IsRead) {
			$wakeup->IsRead = 1;
			$wakeup->Save();
		}
	}

	require_once $root."references.php"; 
	

	?>		<title><? echo ($wakeup->IsEmpty() ? "Wakeup Error!" : "��������� �� ".$wakeup->FromUserName) ?></title>
	</head>

	<body>
			<? 
			
			if ($user->IsEmpty()) {
				?>
			<div class="Error">������������ �� �����������!</div>
			<?
			} else {
				if ($wakeup->IsEmpty()) {
				?>
			<div class="Error">��������� �� �������!</div>
				<?
				} else {
				?>
			<h1>��������� �� <? echo $wakeup->FromUserName; ?></h1>
			<div id='WakeupContainer'>
				<strong><? echo $wakeup->Message; ?></strong><br><br>
				<a href="javascript:void(0)" onclick="ReplyForm()" class="Reply">��������</a>
				<p><a><? echo $wakeup->FromUserName; ?></a>
					<span><? echo PrintableDate($wakeup->Date); ?></span>
					</p>
			</div>
			<div id='WakeupReply' style='display:none'>
				<form target="#" onsubmit="Send(<? echo $wakeup->Id; ?>);return false;">
					<table>
						<tr><td colspan="2">�����:</td></tr>
						<tr>
							<td width="100%"><input name="reply" id="reply" style="width:100%" autocomplete="off"></td>
							<td><input type="image" value="���������" src="/3/img/send_button.gif"></td>
						</tr>
					</table>
					<span id="status"></span>
				</form>
			</div>
			<script language="javascript" src="/3/js1/wakeup.js"></script>
				<?
				}
			}
			
			 ?>
		
	</body>
</html>