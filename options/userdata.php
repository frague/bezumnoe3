<?
    require "menu_base.php";

?>

<h2>Профиль пользователя <span id="LOGIN">%username%</span></h2>

<table width="100%" cellpadding="4">
	<tr>
		<td width="50%">

<h4>Дата регистрации:</h4>
<div id="REGISTERED"></div>

<h4>Последнее посещение:</h4>
<div id="LAST_VISIT"></div>

<h4>E-mail:</h4>
<div id="EMAIL"></div>

<h4>Пароль:</h4>
<input name="PASSWORD" id="PASSWORD" type="password" value="**********" maxlength="255" onfocus="ClearInput(this)" onblur="RestoreInput(this,'ConfirmBlock')" class="Wide" />

<div id="ConfirmBlock" style="display:none;margin-bottom:10px;">
	<h4 style="color:red;">Подтверждение пароля:</h4>
	<input name="PASSWORD_CONFIRM" id="PASSWORD_CONFIRM" type="password" maxlength="255" class="Wide" /></div>

<h4>Имя:</h4>
<input name="NAME" id="NAME" class="Wide" />

<h4>Пол:</h4>
<select name="GENDER" id="GENDER" class="Wide"><option value="m">мужской</option><option value="f">женский</option><option value="">неопределённый</option></select>

<h4>День рождения:</h4>
<input name="BIRTHDAY" id="BIRTHDAY" maxlength="10" />

<h4>Город:</h4>
<input name="CITY" id="CITY" maxlength="100" class="Wide" />

<h4>Адрес сайта в интернете:</h4>
<input name="URL" id="URL" maxlength="255" class="Wide" />

<h4>ICQ:</h4>
<input name="ICQ" id="ICQ" maxlength="20" class="Wide" />

<h4>О себе:</h4>
<textarea name="ABOUT" id="ABOUT" rows="6" onclick="Maximize(this)"></textarea>

		</td><td>
<h4>Фотография:</h4>
<div id="Photo"></div>
<form name="uploadForm" id="uploadForm" action="/services/profile.service.php" method="POST" enctype="multipart/form-data">
<h4>Изменить фотографию:</h4>
	<input type="hidden" name="go" id="go" value="upload_photo" />
	<input type="hidden" name="tab_id" id="tab_id" />
	<input type="hidden" name="USER_ID" id="USER_ID" />
	<input type="hidden" name="MAX_FILE_SIZE" value="2097152" class="Wide" />
	<input type="file" name="PHOTO1" id="PHOTO1" />
	<p class="Note">Допускается загружать файлы размером не более 2 Мб.</p>
</form>

<h4>Аватар:</h4>
<div id="Avatar"></div>
<form name="avatarUploadForm" id="avatarUploadForm" action="/services/profile.service.php" method="POST" enctype="multipart/form-data">
<h4>Изменить аватар:</h4>
	<input type="hidden" name="go" id="go" value="upload_avatar" />
	<input type="hidden" name="tab_id" id="tab_id" />
	<input type="hidden" name="USER_ID" id="USER_ID" />
	<input type="hidden" name="MAX_FILE_SIZE" value="2097152" class="Wide" />
	<input type="file" name="PHOTO1" id="PHOTO1" />
	<p class="Note">Допускается загружать файлы размером не более 2 Мб.</p>
</form>


		</td>
	</tr>
</table>
<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" id="linkRefresh" class="Refresh">Обновить данные с сервера</a>
	<li id="liDeletePhoto"> <a href="javascript:void(0)" onclick="DeletePhoto(this)" id="linkDeletePhoto" class="Delete">Удалить фотографию</a>
	<li id="liDeleteAvatar"> <a href="javascript:void(0)" onclick="DeleteAvatar(this)" id="linkDeleteAvatar" class="Delete">Удалить аватар</a>
</ul>

<?
	if (!$user->IsEmpty() && $user->IsAdmin()) {
?><div id="AdminSection">
	<h2>Административная часть:</h2>

	<table cellpadding="4" id="NotForMe">
		<tr>
			<td width="50%">

	<h4>Права/статус:</h4>
	<select id="STATUS_ID" name="STATUS_ID" class="Wide"><? 
	$status = new Status();
	echo $status->ToSelect(Status::STATUS_ID, $user);
?></select>

	<h4>IP-адрес (хост) последней сессии:</h4>
	<span id="SESSION_ADDRESS"></span> (<a href="javascript:void(0)" onclick="LockIP(this)" id="linkLockIP">закрыть адрес</a>)

			</td><td>

	<h4>Бан:</h4>
	<div id="BanStatus"></div>
	<input type="checkbox" id="BANNED" name="BANNED" onclick="ShowBanDetails(this)" /> Пользователь забанен
	<input type="hidden" id="BANNED_BY" name="BANNED_BY" />

	<div id="BanDetails" style="display:none">
		<h4>Причина бана:</h4>
		<textarea name="BAN_REASON" id="BAN_REASON" class="Wide" rows="3"></textarea>
		<p class="Note">Будьте предельно корректны в формулировке причины бана. Не допускайте оскорблений в адрес пользователя.</p>

		<h4>Срок бана:</h4>
		<input name="BANNED_TILL" id="BANNED_TILL" />
	</div>

			</td>
		</tr>
	</table>

	<div id="AdminComments"></div>

</div><?
	}
?>
<br><br>