<?
    require "menu_base.php";

?>
<h2>Доступ к <span id="TITLE">журналу:</span></h2>
<h3 id="DESCRIPTION"></h3>

<table class="Wide">
	<tr>
		<td width="50%">

<h4>&laquo;Чёрный список&raquo;:</h4>
<p class="Note">Пользователи из этого списка не имеют возможности публикации сообщений/комментариев в вашем журнале.</p>
<table cellpadding="2" cellspacing="0" id="BLACK_LIST" class="Grid Wide"><tbody><tr><th>Пользователь</th><th width="1%">Удалить</th></tr></tbody></table>
		</td><td>

<h4>&laquo;Белый список&raquo;:</h4>
<p class="Note">Пользователи из этого списка имеют эксклюзивный доступ к вашему журналу: возможность публикации сообщений и удаления комментариев.</p>
<table cellpadding="2" cellspacing="0" id="WHITE_LIST" class="Grid Wide"><tbody><tr><th>Пользователь</th><th width="1%">Удалить</th></tr></tbody></table>
		</td></tr>
	<tr>
		<td width="50%">

<h4>Дружественные журналы:</h4>
<p class="Note">Записи из журналов указанных пользователей попадают в вашу френд-ленту.</p>
<table cellpadding="2" cellspacing="0" id="FRIENDS_LIST" class="Grid Wide"><tbody><tr><th>Пользователь</th><th width="1%">Удалить</th></tr></tbody></table>
		</td><td>
			&nbsp;
		</td></tr>
</table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshForumAccess">Обновить данные</a>
</ul>
