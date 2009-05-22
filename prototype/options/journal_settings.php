<?
    require "menu_base.php";

?>



<table class="Wide">
	<tr>
		<td width="50%">

<h4>Алиас:</h4>
Текущий: <span id="ALIAS" class="Bold">не задан</span>
<p class="Note">Альтернативное название вашего журнала (латинские буквы, цифры, знак "_"),
используемое при построении ссылки. Например, http://www.bezumnoe.ru/journal/your_alias</p>

		</td><td>

<h4>Смена алиаса:</h4>
<p class="Note">Если вы хотите изменить алиас вашего журнала, введите желаемый в это поле и сохраните настройки.
После проверки администратором будет принято решение о смене алиаса.</p>
<input id="REQUESTED_ALIAS" name="REQUESTED_ALIAS" class="Wide" maxlength="20" />

		</td></tr>

	<tr>
		<td>

<h4>Дружественные журналы:</h4>
<p class="Note">Сообщения журналов из этого списка будут отображться в вашей "френдленте". Обозначенные пользователи также смогут просматривать ваши сообщения "только для друзей".</p>

<ul class="Links" id="friendlyBlogs"></ul>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="LoadFriendlyJournals(this)" id="linkRefresh" class="Refresh">Обновить список</a>
</ul>

		
		</td><td>

<h4>Запрет на комментирование вашего журнала:</h4>
<p class="Note">Указанные пользователи не смогут оставлять комментарии к сообщениям вашего журнала.</p>

<ul class="Links"  id="forbiddenCommenters"></ul>
		
<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="LoadForbiddenCommenters(this)" id="linkRefresh" class="Refresh">Обновить список</a>
</ul>
		</td></tr></table>

<br />
<br />
