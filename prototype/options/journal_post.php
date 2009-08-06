<?
    require "menu_base.php";

?>
<h2>Сообщение: <span id="TITLE1"></span></h2>

<h4>Дата и время:</h4>
<input id="DATE" name="DATE" maxlength="10">

<h4>Заголовок:</h4>
<input id="TITLE" name="TITLE" class="Wide" maxlength="1024">

<h4>Текст сообщения:</h4>
<div id="ContentHolder"></div>

<table class="Wide">
	<tr>
		<td width="50%">

<h4>Тип сообщения:</h4>

<div id="TYPE" class="Radios">
	<input type="radio" name="TYPE1" id="TYPE0" value="0" checked /> <label for="TYPE0">Публичное (видимое для всех)</label><br />
	<input type="radio" name="TYPE1" id="TYPE1" value="1" /> <label for="TYPE1">Для друзей (скрытое)</label><br />
	<input type="radio" name="TYPE1" id="TYPE2" value="2" /> <label for="TYPE2" id="TYPE2LABEL">Приватное (видимое только вам)</label><br />
</div>

		</td><td>

<h4>Комментарии к сообщению:</h4>
	<input type="checkbox" id="IS_COMMENTABLE" name="IS_COMMENTABLE" checked /> <label for="IS_COMMENTABLE">Разрешить комментировать сообщение</label>

		</td></tr></table>

<br />
<br />
