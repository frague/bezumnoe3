<?
    require "menu_base.php";

?>
<h2 id="TITLE1"></h2>

<table class="Wide">
	<tr>
		<td colspan="2">
<h4>Название:</h4>
<input id="TITLE" name="TITLE" class="Wide" /><br />
		
<div id="gallery">
	<h4>Краткое описание:</h4>
	<textarea id="DESCRIPTION" name="DESCRIPTION" rows="5" class="Wide"></textarea>
</div>

<input type="checkbox" id="IS_PROTECTED" name="IS_PROTECTED" /> <label for="IS_PROTECTED">Раздел защищён от просмотра</label><br />
<p class="Note">При установке только пользователи из "белого списка" смогут видеть записи в разделе</p>

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
