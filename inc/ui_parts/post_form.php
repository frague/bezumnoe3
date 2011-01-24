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
		<span id="LoggedLine"<?php echo $user->IsEmpty() ? " class=\"Hidden\"" : "" ?>>
			<input type="radio" name="AUTH" id="AUTH_LOGGED" value="0" checked="checked" /> <label for="AUTH_LOGGED">�������� ������������ (<strong id="LoggedLogin"><?php echo $user->User->Login ?></strong>)</label><br />
		</span>
		<input type="radio" name="AUTH" id="AUTH_NOW" value="1" /> <label for="AUTH_NOW">�� ������ � ������</label>
		<div id="AuthByLogin">
			�����: <input name="<?php echo LOGIN_KEY ?>" id="<?php echo LOGIN_KEY ?>" size="20" tabindex="1" />, 
			������: <input type="password" name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" size="20" tabindex="2" />
		</div>

		<input type="radio" name="AUTH" id="AUTH_OPENID" value="2" disabled /> <label for="AUTH_OPENID">� �������������� OpenID</label>
		<div id="AuthByOpenID">
			<form method="POST" action="/auth.php">
			<input type="hidden" name="openid_action" value="login" />
			<input type="hidden" name="<?php echo REFERER_KEY ?>" id="<?php echo REFERER_KEY ?>" />
			�����: <input name="<?php echo LOGIN_KEY ?>" id="<?php echo LOGIN_KEY ?>" size="20" tabindex="1" />, 
			������: <input name="<?php echo OPENID_KEY ?>" id="<?php echo OPENID_KEY ?>" type="hidden" />
			<span class="OpenID">
			<?php

	$op = new OpenIdProvider();
	$q = $op->GetAll();
	for ($i = 0; $i < $q->NumRows(); $i++) {
		$q->NextResult();
		$op->FIllFromResult($q);
		echo $op->ToPrint($i, OPENID_KEY);
	}

			?></span> <a href="javascript:void(0)" onclick="SubmitOpenId('<?php echo REFERER_KEY ?>')">&nbsp;</a>
			</form>
		</div>

		<h4>���������:</h4>
		<input id="TITLE" name="TITLE" class="Wide" size="30" tabindex="3" /><br>
		<h4 class="Mandatory">����� ���������:</h4>
		<textarea id="CONTENT" name="CONTENT" class="Wide" cols="30" rows="10" tabindex="4">
		</textarea><br>
		<div class="Right">	
			<div style="float:left" id="IsProtected">
				<input type="checkbox" name="IS_PROTECTED" id="IS_PROTECTED" /> ������� ���������
			</div>
			<input type="Button" value="������" onclick="MakeCite()" id="buttonCite">
			<input type="Button" value="��������" onclick="CancelReply()" style="margin-right:20px">
			<input type="Button" value="���������" onclick="AddMessage(this)" id="SubmitMessageButton" tabindex="5" style="font-weight:bold">
		</div><?php } ?>
	</div>
</div>

<script language="javascript">OpenReplyForm();</script>
	
