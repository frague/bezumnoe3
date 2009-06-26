<?
    require "menu_base.php";

?>



<table class="Wide">
	<tr>
		<td colspan="2">
<h4>Название:</h4>
<input id="TITLE" name="TITLE" class="Wide" /><br />
		
<div id="gallery">
	<h4>Краткое описание:</h4>
	<textarea id="DESCRIPTION" name="DESCRIPTION" rows="5" class="Wide"></textarea>
</div>
		</td></tr>
	<tr id="forum">
		<td width="50%">

<h4>Алиас:</h4>
Текущий: <span id="ALIAS" class="Bold">не задан</span>
<p class="Note">Альтернативное название журнала (латинские буквы, цифры, знак "_"),
используемое при построении ссылки. Например, http://www.bezumnoe.ru/journal/your_alias</p>

		</td><td>

<h4>Смена алиаса:</h4>
<p class="Note">Если вы хотите изменить алиас журнала, введите желаемый в это поле и сохраните настройки.
После проверки администратором будет принято решение о смене алиаса.</p>
<input id="REQUESTED_ALIAS" name="REQUESTED_ALIAS" class="Wide" maxlength="20" />

		</td></tr>
</table>

<br />
<br />
