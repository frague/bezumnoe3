<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="windows-1251" />
		<link rel="stylesheet" href="/css/global.css" />
		<link rel="stylesheet" href="/css/wakeup.css" />
		<link rel="icon" href="/img/icons/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="/img/icons/favicon.ico" type="image/x-icon">
		<?

	$root = "./";
	require_once $root."server_references.php";

	$user = GetAuthorizedUser(true);

	$wakeup_id = round($_GET["id"]);
	$wakeup = new Wakeup();
	if (!$user->IsEmpty() && $wakeup_id > 0) {
		$wakeup->FillForUser($user->User, $wakeup_id);
		if (!$wakeup->IsEmpty() && !$wakeup->IsRead) {
			$wakeup->IsRead = 1;
			$wakeup->Save();
		}
	}

	require_once $root."references.php"; 
	

	?>		<title><? echo ($wakeup->IsEmpty() ? "Wakeup Error!" : "Сообщение от ".$wakeup->FromUserName) ?></title>
		<?php include $root."/inc/ui_parts/google_analythics.php"; ?>
	</head>

	<body>
			<? 
			
			if ($user->IsEmpty()) {
				?>
			<div class="Error">Пользователь не авторизован!</div>
			<?
			} else {
				if ($wakeup->IsEmpty()) {
				?>
			<div class="Error">Сообщение не найдено!</div>
				<?
				} else {
				?>
			<h1>Сообщение от <? echo $wakeup->FromUserName; ?></h1>
			<div id='WakeupContainer'>
				<strong><? echo OuterLinks(MakeLinks($wakeup->Message, true)); ?></strong><br><br>
				<a href="javascript:void(0)" onclick="ReplyForm()" class="Reply">Ответить</a>
				<p><a><? echo $wakeup->FromUserName; ?></a>
					<span><? echo PrintableDate($wakeup->Date); ?></span>
					</p>
			</div>
			<div id='WakeupReply' style='display:none'>
				<form target="#" onsubmit="Send(<? echo $wakeup->Id; ?>);return false;">
					<div class="RoundedCorners" id="status"></div>
					<table>
						<tr>
							<td>Ответ:</td>
							<td width="100%"><input name="reply" id="reply" style="width:100%" autocomplete="off"></td>
							<td><input type="image" value="Отправить" src="/img/send_button.gif"></td>
						</tr>
					</table>
				</form>
			</div>
			<script language="javascript" src="/js1/wakeup.js"></script>
				<?
				}
			}
			
			 ?>
		
	</body>
</html>