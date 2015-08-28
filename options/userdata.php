<?
    require "menu_base.php";
?>

<h2>Профиль пользователя <span id="LOGIN">%username%</span></h2>

<table class="halves">
    <tr>
        <td>
            <label>
                <span>Дата регистрации</span>
                <div id="REGISTERED"></div>
            </label>

            <label>
                <span>Последнее посещение</span>
                <div id="LAST_VISIT"></div>
            </label>

            <label>
                <span>E-mail</span>
                <div id="EMAIL"></div>
            </label>

            <label>
                <span>Пароль</span>
                <input name="PASSWORD" id="PASSWORD" type="password" value="**********" maxlength="255" onfocus="clearInput(this)" onblur="restoreInput(this,'ConfirmBlock')" class="Wide" />
            </label>

            <label id="ConfirmBlock" style="display:none">
                <span>Подтверждение пароля</span>
                <input name="PASSWORD_CONFIRM" id="PASSWORD_CONFIRM" type="password" maxlength="255" class="Wide" />
            </label>

            <label>
                <span>Имя</span>
                <input name="NAME" id="NAME" class="Wide" />
            </label>

            <label>
                <span>Пол</span>
                <select name="GENDER" id="GENDER" class="Wide">
                    <option value="m">мужской</option>
                    <option value="f">женский</option>
                    <option value="">неопределённый</option>
                </select>
            </label>

            <label>
                <span>День рождения</span>
                <input name="BIRTHDAY" id="BIRTHDAY" maxlength="10" />
            </label>

            <label>
                <span>Откуда вы</span>
                <input name="CITY" id="CITY" maxlength="100" class="Wide" />
            </label>

            <label>
                <span>Адрес сайта в интернете</span>
                <input name="URL" id="URL" maxlength="255" class="Wide" />
            </label>

            <label>
                <span>ICQ</span>
                <input name="ICQ" id="ICQ" maxlength="20" class="Wide" />
            </label>

            <label>
                <span>О себе</span>
                <textarea name="ABOUT" id="ABOUT" rows="6" onclick="Maximize(this)"></textarea>
            </label>
        </td>
        <td>
            <label>
                <span>Фотография</span>
                <div id="Photo"></div>
            </label>

            <form name="uploadForm" id="uploadForm" action="/services/profile.service.php" method="POST" enctype="multipart/form-data">
                <label>
                    <span>Изменить фотографию</span>
                    <input type="hidden" name="go" id="go" value="upload_photo" />
                    <input type="hidden" name="tab_id" id="tab_id" />
                    <input type="hidden" name="USER_ID" id="USER_ID" />
                    <input type="hidden" name="MAX_FILE_SIZE" value="2097152" class="Wide" />
                    <input type="file" name="PHOTO1" id="PHOTO1" />
                    <p>Допускается загружать файлы размером не более 2 Мб.</p>
                </label>
            </form>

            <label>
                <span>Аватар</span>
                <div id="Avatar"></div>
            </label>

            <form name="avatarUploadForm" id="avatarUploadForm" action="/services/profile.service.php" method="POST" enctype="multipart/form-data">
                <label>
                    <span>Изменить аватар</span>
                    <input type="hidden" name="go" id="go" value="upload_avatar" />
                    <input type="hidden" name="tab_id" id="tab_id" />
                    <input type="hidden" name="USER_ID" id="USER_ID" />
                    <input type="hidden" name="MAX_FILE_SIZE" value="2097152" class="Wide" />
                    <input type="file" name="PHOTO1" id="PHOTO1" />
                    <p>Допускается загружать файлы размером не более 2 Мб.</p>
                </label>
            </form>
        </td>
    </tr>
</table>

<div id="OpenIds"></div>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="ReRequestData(this)" id="linkRefresh" class="Refresh">Обновить данные с сервера</a>
    <li id="liDeletePhoto"> <a href="javascript:void(0)" onclick="DeletePhoto(this)" id="linkDeletePhoto" class="Delete">Удалить фотографию</a>
    <li id="liDeleteAvatar"> <a href="javascript:void(0)" onclick="DeleteAvatar(this)" id="linkDeleteAvatar" class="Delete">Удалить аватар</a>
</ul>
<?
    if (!$user->IsEmpty() && $user->IsAdmin()) {
?>
<div id="AdminSection">
    <h2>Административная часть</h2>

    <table id="NotForMe" class="halves">
        <tr>
            <td>
                <label>
                    <span>Права/статус</span>
                    <select id="STATUS_ID" name="STATUS_ID" class="Wide">
                        <? $status = new Status(); print $status->ToSelect(Status::STATUS_ID, $user); ?>
                    </select>
                </label>

                <label>
                    <span>IP-адрес (хост) последней сессии</span>
                    <span id="SESSION_ADDRESS"></span> (<a href="javascript:void(0)" onclick="LockIP(this)" id="linkLockIP">закрыть адрес</a>)
                </label>
            </td>
            <td>
                <label>
                    <span>Бан</span>
                    <div id="BanStatus"></div>
                    <input type="checkbox" id="BANNED" name="BANNED" onclick="ShowBanDetails(this)" /> Пользователь забанен
                    <input type="hidden" id="BANNED_BY" name="BANNED_BY" />
                </label>

                <div id="BanDetails" style="display:none">
                    <label>
                        <span>Причина бана</span>
                        <textarea name="BAN_REASON" id="BAN_REASON" class="Wide" rows="3"></textarea>
                        <p>Будьте предельно корректны в формулировке причины бана. Не допускайте оскорблений в адрес пользователя.</p>
                    </label>

                    <label>
                        <span>Срок бана</span>
                        <input name="BANNED_TILL" id="BANNED_TILL" />
                    </label>
                </div>
            </td>
        </tr>
    </table>

    <div id="AdminComments"></div>

</div><? } ?>
