<?
    require "menu_base.php";

?>
<h2 id="TITLE"></h2>

<table class="Wide">
	<tr>
		<td colspan="2">
<h4>Поиск пользователей:</h4>
<p class="Note">
	В результатах поиска отображаются пользователи и их журналы.
	Журналы можно добавить в вашу френдленту (дружественные журналы). 
	Для пользователей вы можете задать определённый уровень доступа к вашему журналу (смотрите всплывающие подсказки над иконками).
</p>
<div>
	<input id="ADD_USER" name="ADD_USER" class="Wide" />
	<div id="FOUND_USERS" name="FOUND_USERS"></div>
</div>
		</td></tr>
	<tr id="FRIENDS">
		<td colspan="2">
<h4>Дружественные журналы:</h4>
<p class="Note">Записи из журналов этого списка попадают во френд-ленту выбранного раздела.</p>
<table cellpadding="2" cellspacing="0" id="FRIENDS_LIST" class="Grid Wide"><tbody><tr><th>Название/владелец дружественного журнала</th><th width="1%">Удалить</th></tr></tbody></table>
		</td></tr>
	<tr>
		<td width="50%">
<h4>&laquo;Белый список&raquo;:</h4>
<p class="Note">Пользователи из этого списка имеют эксклюзивный доступ к выбранному разделу: возможность публикации сообщений и удаления комментариев.</p>
<table cellpadding="2" cellspacing="0" id="WHITE_LIST" class="Grid Wide"><tbody><tr><th>Пользователь</th><th width="1%">Удалить</th></tr></tbody></table>
		</td><td>
<h4>&laquo;Чёрный список&raquo;:</h4>
<p class="Note">Пользователи из этого списка не имеют возможности публикации сообщений/комментариев в выбранном разделе.</p>
<table cellpadding="2" cellspacing="0" id="BLACK_LIST" class="Grid Wide"><tbody><tr><th>Пользователь</th><th width="1%">Удалить</th></tr></tbody></table>
		</td></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshForumAccess">Обновить данные</a>
</ul>
