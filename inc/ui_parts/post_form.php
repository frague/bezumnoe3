<div id="Hider">
	<div id="ReplyForm"><?php
	if (!IsPostingAllowed()) {

?>
		<span class="Warning">��� ������ ������ � ���������� ���������.</span>

<?php

		} else {
?>
		<div class='Red' id="ERROR"></div>
		
		<h4>�����������:</h4>
<?php

	if ($user->IsEmpty()) {
		echo "<style>#LoggedLine {
			display:none;
		}</style>";
	}

?>
		<span id="LoggedLine">
			<input type="radio" checked="checked" name="AUTH" id="AUTH_LOGGED" /> <label for="AUTH_LOGGED">�������� ������������ (<strong id="LoggedLogin"><?php echo $user->User->Login ?></strong>)</label><br />
		</span>
		<input type="radio" name="AUTH" id="AUTH_NOW" /> <label for="AUTH_NOW">�� ������ � ������</label>
		<div id="AuthByLogin">
			�����: <input name="<?php echo LOGIN_KEY ?>" id="<?php echo LOGIN_KEY ?>" size="20" />, 
			������: <input type="password" name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" size="20" />
			
		</div>

		<h4 class="Mandatory">���������:</h4>
		<input id="TITLE" name="TITLE" class="Wide" /><br>
		<h4 class="Mandatory">����� ���������:</h4>
		<textarea id="CONTENT" name="CONTENT" class="Wide" rows="10">
		</textarea><br>
		<div class="Right">	
			<div style="float:left" id="IsProtected">
				<input type="checkbox" name="IS_PROTECTED" id="IS_PROTECTED" /> ������� ���������
			</div>
			<input type="Button" value="���������" onclick="AddMessage(this)" id="SubmitMessageButton">
			<input type="Button" value="����������" onclick="MakeCite()" id="buttonCite">
			<input type="Button" value="��������" onclick="CancelReply()">
		</div><?php } ?>
	</div>
</div>
