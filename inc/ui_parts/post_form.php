<div id="Hider">
	<div id="ReplyForm"><?php
	if (!IsPostingAllowed()) {

?>
		<span class="Warning">Вам закрыт доступ к публикации сообщений.</span>

<?php

		} else {
?>
		<div class='Red' id="ERROR"></div>
		
		<h4>Авторизация:</h4>
		<span id="LoggedLine"<?php echo $user->IsEmpty() ? " class=\"Hidden\"" : "" ?>>
			<input type="radio" name="AUTH" id="AUTH_LOGGED" value="0" checked="checked" /> <label for="AUTH_LOGGED">активный пользователь (<strong id="LoggedLogin"><?php echo $user->User->Login ?></strong>)</label><br />
		</span>
		<input type="radio" name="AUTH" id="AUTH_NOW" value="1" /> <label for="AUTH_NOW">по логину и паролю</label>
		<div id="AuthByLogin">
			логин: <input name="<?php echo LOGIN_KEY ?>" id="<?php echo LOGIN_KEY ?>" size="20" tabindex="1" />, 
			пароль: <input type="password" name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" size="20" tabindex="2" />
		</div>

		<input type="radio" name="AUTH" id="AUTH_OPENID" value="2" disabled /> <label for="AUTH_OPENID">с использованием OpenID</label>
		<div id="AuthByOpenID">
			<form><input type="hidden" name="openid_action" value="login" />
			логин: <input name="<?php echo LOGIN_KEY ?>" id="<?php echo LOGIN_KEY ?>" size="20" tabindex="1" disabled />, 
			сервис: <input name="<?php echo OPENID_KEY ?>" id="<?php echo OPENID_KEY ?>" type="hidden" />
			<?php

	$op = new OpenIdProvider();
	$q = $op->GetAll();
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$op->FIllFromResult($q);
		echo $op->ToPrint($i, OPENID_KEY);
	}

			?>
			</form>
		</div>

		<h4>Заголовок:</h4>
		<input id="TITLE" name="TITLE" class="Wide" size="30" tabindex="3" /><br>
		<h4 class="Mandatory">Текст сообщения:</h4>
		<textarea id="CONTENT" name="CONTENT" class="Wide" cols="30" rows="10" tabindex="4">
		</textarea><br>
		<div class="Right">	
			<div style="float:left" id="IsProtected">
				<input type="checkbox" name="IS_PROTECTED" id="IS_PROTECTED" /> Скрытое сообщение
			</div>
			<input type="Button" value="Цитата" onclick="MakeCite()" id="buttonCite">
			<input type="Button" value="Отменить" onclick="CancelReply()" style="margin-right:20px">
			<input type="Button" value="Отправить" onclick="AddMessage(this)" id="SubmitMessageButton" tabindex="5" style="font-weight:bold">
		</div><?php } ?>
	</div>
</div>

<script language="javascript">OpenReplyForm();</script>
	
