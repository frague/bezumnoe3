<div id="Hider">
	<div id="ReplyForm"><?php
	if (!IsPostingAllowed()) {

?>
		<span class="Warning">Вам закрыт доступ к публикации сообщений.</span>

<?php

		} else {
?>
		<div class='Red' id="ERROR"></div>
		
		Пользователь: <strong id="LoggedLogin"><?php echo $user->User->Login ?></strong><br />

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

<script language="javascript">
$(function() {
		// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
		$( "#dialog:ui-dialog" ).dialog( "destroy" );
		
		function checkLength( o, n, min, max ) {
			if ( o.val().length > max || o.val().length < min ) {
				o.addClass( "ui-state-error" );
				updateTips( "Length of " + n + " must be between " +
					min + " and " + max + "." );
				return false;
			} else {
				return true;
			}
		}

		function checkRegexp( o, regexp, n ) {
			if ( !( regexp.test( o.val() ) ) ) {
				o.addClass( "ui-state-error" );
				updateTips( n );
				return false;
			} else {
				return true;
			}
		}
		
		$( "#auth_form" ).dialog({
			title: 'Авторизация в чате',
			autoOpen: false,
			height: 230,
			width: 420,
			modal: true,
			buttons: {
				"Авторизоваться": function() {
					$( "#auth" ).submit();
					$( this ).dialog( "close" );
				},
				"Отмена": function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
			},
		});
	});
</script>

<div id="auth_form">
	<form method="POST" action="/auth/" name="auth" id="auth">
		<table class="Wide">
			<tr>
				<td width="50%" valign="top">
					<input type="radio" name="AUTH" id="AUTH_NOW" value="1" checked /> <label for="AUTH_NOW">по логину и паролю</label>
					<div id="AuthByLogin">
						<div><label for="<?php echo LOGIN_KEY ?>">Логин</label> <input name="<?php echo LOGIN_KEY ?>" id="<?php echo LOGIN_KEY ?>" size="20" tabindex="1000" /></div>
						<div><label for="<?php echo PASSWORD_KEY ?>">Пароль</label> <input type="password" name="<?php echo PASSWORD_KEY ?>" id="<?php echo PASSWORD_KEY ?>" size="20" tabindex="1001" /></div>
					</div></td>
				<td width="50%">
					<input type="radio" name="AUTH" id="AUTH_OPENID" value="2" /> <label for="AUTH_OPENID">с использованием OpenID</label>
					<div id="AuthByOpenID">
						<input type="hidden" name="openid_action" value="login" />
						<input type="hidden" name="<?php echo REFERER_KEY ?>" id="<?php echo REFERER_KEY ?>" />
						<div><label for="<?php echo OPENID_LOGIN_KEY ?>">Логин</label> <input name="<?php echo OPENID_LOGIN_KEY ?>" id="<?php echo OPENID_LOGIN_KEY ?>" size="20" /></div>
						Сервис <input name="<?php echo OPENID_KEY ?>" id="<?php echo OPENID_KEY ?>" type="hidden" /><br />
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