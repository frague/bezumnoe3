<div id="Hider">
	<div id="ReplyForm"><?
		if (!$postAccess) {

	?>
		<span class="Warning">Вам закрыт доступ к публикации сообщений.</span>

	<?

		} elseif (!$user->IsEmpty()) {
	?>
		<div class='Red' id="ERROR"></div>
		<h4>Заголовок:</h4>
		<input id="TITLE" name="TITLE" class="Wide" /><br>
		<h4>Текст сообщения:</h4>
		<textarea id="CONTENT" name="CONTENT" class="Wide" rows="10">
		</textarea><br>
		<div class="Right">	
			<div style="float:left" id="IsProtected">
				<input type="checkbox" name="IS_PROTECTED" id="IS_PROTECTED" /> Скрытое сообщение
			</div>
			<input type="Button" value="Отправить" onclick="AddMessage()">
			<input type="Button" value="Цитировать" onclick="MakeCite()" id="buttonCite">
			<input type="Button" value="Отменить" onclick="CancelReply()">
		</div>
	<?
		} else {
	?>
		<span class="Warning">Для публикации сообщений в форуме необходимо <a href="/3/prototype">авторизоваться</a> в системе.</span>

	<?
		}
	?>
	</div>
</div>
