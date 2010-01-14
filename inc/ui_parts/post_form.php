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
			логин: <input name="<?php echo LOGIN_KEY ?>" id="<?php echo LOGIN_KEY ?>" size="20" />, 
			пароль: <input type="password" name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" size="20" />
			
		</div>

		<h4>Заголовок:</h4>
		<input id="TITLE" name="TITLE" class="Wide" size="30" /><br>
		<h4 class="Mandatory">Текст сообщения:</h4>
		<textarea id="CONTENT" name="CONTENT" class="Wide" cols="30" rows="10">
		</textarea><br>
		<div class="Right">	
			<div style="float:left" id="IsProtected">
				<input type="checkbox" name="IS_PROTECTED" id="IS_PROTECTED" /> Скрытое сообщение
			</div>
			<input type="Button" value="Отправить" onclick="AddMessage(this)" id="SubmitMessageButton">
			<input type="Button" value="Цитировать" onclick="MakeCite()" id="buttonCite">
			<input type="Button" value="Отменить" onclick="CancelReply()">
		</div><?php } ?>
	</div>
</div>
