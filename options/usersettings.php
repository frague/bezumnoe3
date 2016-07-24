<?php
    require "menu_base.php";

?>

<h2>Настройки пользователя <span id="LOGIN">%username%</span></h2>

<table width="100%" cellpadding="4">
    <tr>
        <td width="50%" colspan="2">
<h4>Шрифт:</h4>
Название шрифта:<br>
<input id="FONT_FACE" name="FONT_FACE" class="Wide" onchange="UpdateFontView()" /><br />

Размер шрифта: <select id="FONT_SIZE" name="FONT_SIZE" onchange="UpdateFontView()"><option value="1">самый малый<option value="2">малый<option value="3">нормальный<option value="4">большой<option value="5">очень большой</select><br />

Цвет:<br>
<input id="FONT_COLOR" name="FONT_COLOR" onchange="UpdateFontView()" /><br />

Стили:<br>
<input type="checkbox" id="FONT_BOLD" name="FONT_BOLD" onclick="UpdateFontView()" /> <label for="FONT_BOLD"><b>Жирный</b></label><br />
<input type="checkbox" id="FONT_ITALIC" name="FONT_ITALIC" onclick="UpdateFontView()" /> <label for="FONT_ITALIC"><i>Курсив</i></label><br />
<input type="checkbox" id="FONT_UNDERLINED" name="FONT_UNDERLINED" onclick="UpdateFontView()" /> <label for="FONT_UNDERLINED" class="NoBorder"><u>Подчёркнутый</u></label><br />

<h4>Ваш шрифт выглядит так:</h4>
<div id="fontExample">
    Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nam nibh nunc, ultricies eu, ultricies id, tristique vel, purus.
    <p>Съешь ещё этих мягких французских булок, да выпей же чаю.
</div>

        </td><td colspan="2">
<h4>Отображать сообщения чата:</h4>
<input type="checkbox" id="IGNORE_FONTS" name="IGNORE_FONTS" /> <label for="IGNORE_FONTS">шрифтом по умолчанию</label><br />
<input type="checkbox" id="IGNORE_COLORS" name="IGNORE_COLORS" /> <label for="IGNORE_COLORS">одним цветом</label><br />
<input type="checkbox" id="IGNORE_FONT_SIZE" name="IGNORE_FONT_SIZE" /> <label for="IGNORE_FONT_SIZE">шрифтом одного размера</label><br />
<input type="checkbox" id="IGNORE_FONT_STYLE" name="IGNORE_FONT_STYLE" /> <label for="IGNORE_FONT_STYLE">игнорировать стили</label><br />

<h4>Другие настройки:</h4>
<input type="checkbox" id="RECEIVE_WAKEUPS" name="RECEIVE_WAKEUPS" /> <label for="RECEIVE_WAKEUPS">Раскрывать вейкап сообщения при получении</label>

<h4>Сообщения входа и выхода:</h4>
Сообщение о входе в чат:<br>
<input id="ENTER_MESSAGE" name="ENTER_MESSAGE" class="Wide" /><br />
<p class="Note">Используйте ключевое слово <b>%name</b> в том месте, куда будет подставлен ваш никнейм</p>

Сообщение о выходе из чата:<br />
<input id="QUIT_MESSAGE" name="QUIT_MESSAGE" class="Wide" /><br />
<p class="Note">Используйте ключевое слово <b>%name</b> в том месте, куда будет подставлен ваш никнейм</p>
        </td>
    </tr>
    <tr>
        <td colspan="4">
            <h4>Расположение "фреймов" чата:</h4></td></tr>
    <tr class="Radios" id="FRAMESET"><td width="25%"><img src="/img/frames/0.gif" /><input type="radio" id="SET0" name="FRAMESET1" value="0" /><label for="SET0">Список пользователей слева, поле ввода вверху</label></td><td width="25%"><img src="/img/frames/1.gif" /><input type="radio" id="SET1" name="FRAMESET1" value="1" /><label for="SET1">Список пользователей справа, поле ввода вверху</label></td><td width="25%"><img src="/img/frames/2.gif" /><input type="radio" id="SET2" name="FRAMESET1" value="2"><label for="SET2" />Список пользователей слева, поле ввода внизу</label></td><td width="25%"><img src="/img/frames/3.gif" /><input type="radio" id="SET3" name="FRAMESET1" value="3" /><label for="SET3">Список пользователей справа, поле ввода внизу</label></td></tr></table>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="ReRequestData(this)" id="linkRefresh" class="Refresh">Обновить данные с сервера</a>
</ul>