<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>
<p class="Note">Все административные изменения в профиле пользователя: бан/отмена бана, смена статуса и другие произвольные комментарии.</p>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="AdminCommentsGrid" class="Grid"><tbody><tr><th style="width:120px">Дата/Админ</th><th>Комментарий</th></tr></table>

<h4>Фильтровать по дате:</h4>
<input name="DATE" id="DATE" />

<h4>Поиск в тексте комментария:</h4>
<input name="SEARCH" id="SEARCH" class="Wide" />

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshAdminComments">Обновить список комментариев</a>
	<li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">Сбросить фильтр</a>
</ul>

<h4>Добавить комментарий:</h4>
<table class="Wide">
	<tr><td width="100%">
		<input name="ADMIN_COMMENT" id="ADMIN_COMMENT" class="Wide" />
	</td><td>
		<input type="image" src="/3/img/icons/add_to.gif" id="AddComment" onclick="AddComment(this)" />
	</td></tr></table>
