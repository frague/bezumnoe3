<?
    require "menu_base.php";

?>
<form method="POST" action="/auth/" name="auth_form" id="auth_form">
	<table class="Wide">
		<tr>
			<td width="50%">
				<input type="radio" name="AUTH" id="AUTH_NOW" value="1" checked /> <label for="AUTH_NOW">�� ������ � ������</label>
				<div id="AuthByLogin">
					�����: <input name="<?php echo LOGIN_KEY ?>" id="<?php echo LOGIN_KEY ?>" size="20" tabindex="1000" /><br />
					������: <input type="password" name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" size="20" tabindex="1001" />
				</div></td>
			<td width="50%">
				<input type="radio" name="AUTH" id="AUTH_OPENID" value="2" disabled /> <label for="AUTH_OPENID">� �������������� OpenID</label>
				<div id="AuthByOpenID">
						<input type="hidden" name="openid_action" value="login" />
						<input type="hidden" name="<?php echo REFERER_KEY ?>" id="<?php echo REFERER_KEY ?>" />
						�����: <input name="<?php echo OPENID_LOGIN_KEY ?>" id="<?php echo OPENID_LOGIN_KEY ?>" size="20" disabled /><br />
						������: <input name="<?php echo OPENID_KEY ?>" id="<?php echo OPENID_KEY ?>" type="hidden" /><br />
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
</form>
