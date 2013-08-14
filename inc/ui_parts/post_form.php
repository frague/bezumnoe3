<div id="Hider">
	<div id="ReplyForm" class="ui-dialog ui-widget ui-widget-content ui-corner-all" style="position: static; font-size: inherit;">
		<div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
			<span id="ui-dialog-title-dialog" class="ui-dialog-title">Новый комментарий</span>
		</div>
		<div  style="width: auto;" class="ui-dialog-content ui-widget-content">
			<p>
<?php

	if (!IsPostingAllowed()) {

?>
				<span class="Warning">Вам закрыт доступ к публикации сообщений.</span>

<?php

		} else {
?>
				<div id="ERROR"></div>
		
				Пользователь: <strong id="LoggedLogin"><?php echo $user->User->Login ?></strong><br />

				<label for="TITLE">Заголовок:</label>
				<input id="TITLE" name="TITLE" class="Wide" size="30" tabindex="3" /><br>

				<label for="CONTENT" class="Mandatory">Текст сообщения:</label>
				<textarea id="CONTENT" name="CONTENT" class="Wide" cols="30" rows="8" tabindex="4"></textarea><br>

				<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix" style="text-align:right">
					<div style="float:left" id="IsProtected">
						<input type="checkbox" name="IS_PROTECTED" id="IS_PROTECTED" /> Скрытое сообщение
					</div>
					<input type="button" value="Цитата" onclick="MakeCite()" id="buttonCite">
					<input type="button" value="Отменить" onclick="CancelReply()" style="margin-right:20px">
					<input type="button" value="Отправить" onclick="AddMessage(this)" id="SubmitMessageButton" tabindex="5" style="font-weight:bold">
				</div><?php } ?>
			</p>
		</div>
	</div>
</div>

<style>
	#auth_form {display:none}
</style>
<div id="auth_form">
	<form method="POST" action="/auth/" name="auth" id="auth">
		<table class="Wide">
			<tr>
				<td width="50%" valign="top">
					<input type="radio" name="AUTH" id="AUTH_NOW" value="1" checked /> <label for="AUTH_NOW">по логину и паролю</label>
					<div id="AuthByLogin">
						<div><label for="<?php echo LOGIN_KEY ?>">Логин</label> <input class="submitter" name="<?php echo LOGIN_KEY ?>" id="<?php echo LOGIN_KEY ?>" size="20" tabindex="1000" /></div>
						<div><label for="<?php echo PASSWORD_KEY ?>">Пароль</label> <input class="submitter" type="password" name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" size="20" tabindex="1001" /></div>
					</div></td>
				<td width="50%">
					<input type="radio" name="AUTH" id="AUTH_OPENID" value="2" /> <label for="AUTH_OPENID">с использованием OpenID</label>
					<div id="AuthByOpenID">
						<input type="hidden" name="openid_action" value="login" />
						<input type="hidden" name="<?php echo REFERER_KEY ?>" id="<?php echo REFERER_KEY ?>" />
						<div><label for="<?php echo OPENID_LOGIN_KEY ?>">Логин</label> <input class="submitter" name="<?php echo OPENID_LOGIN_KEY ?>" id="<?php echo OPENID_LOGIN_KEY ?>" size="20" /></div>
						Сервис <input name="<?php echo OPENID_KEY ?>" id="<?php echo OPENID_KEY ?>" type="hidden" /><br />
						<input type="hidden" id="callback" name="callback" />
						<span class="OpenID">
<?php

	$op = new OpenIdProvider();
	$q = $op->GetAll();
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$op->FIllFromResult($q);
		echo $op->ToPrint($i, OPENID_KEY);
	}
?>
						</span>
				</div></td></tr></table>
	</form></div>