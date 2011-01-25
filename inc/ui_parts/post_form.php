<div id="Hider">
	<div id="ReplyForm"><?php
	if (!IsPostingAllowed()) {

?>
		<span class="Warning">Вам закрыт доступ к публикации сообщений.</span>

<?php

		} else {
?>
		<div class='Red' id="ERROR"></div>
		
		Активная сессия: <strong id="LoggedLogin"><?php echo $user->User->Login ?></strong><br />

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

