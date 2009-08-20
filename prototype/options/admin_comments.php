<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>
<p class="Note">Все административные изменения в профиле пользователя: бан/отмена бана, смена статуса и другие произвольные комментарии.</p>

<table class="Wide">
	<tr>
		<td class="Nowrap">
			<h4>Дата:</h4>
			<input name="DATE" id="DATE" /></td>
		<td class="Wide">
			<h4>Поиск по ключевым словам:</h4>
			<input name="SEARCH" id="SEARCH" class="Wide" /></td></tr>
	<tr>
		<td colspan="2">
			<h4>Приоритет:</h4>
			<input type="checkbox" id="SEVERITY_NORMAL" name="SEVERITY_NORMAL" checked> <label for="SEVERITY_NORMAL">нормальный</label>&nbsp;
			<input type="checkbox" id="SEVERITY_WARNING" name="SEVERITY_WARNING" checked> <label for="SEVERITY_WARNING">предупреждение</label>&nbsp;
			<input type="checkbox" id="SEVERITY_ERROR" name="SEVERITY_ERROR" checked> <label for="SEVERITY_ERROR">ошибка</label>
			</td></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshAdminComments">Обновить список/применить фильтр</a>
	<li> <a href="javascript:void(0)" onclick="ResetFilter(this)" class="Delete" id="ResetFilter">Сбросить фильтр</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="AdminCommentsGrid" class="Grid"><tbody><tr><th style="width:120px">Дата/Админ</th><th>Комментарий</th></tr></table>

<h4>Добавить комментарий:</h4>
<table class="Wide">
	<tr>
		<td width="100%">
			<input name="ADMIN_COMMENT" id="ADMIN_COMMENT" class="Wide" />
		</td><td>
			<input type="image" src="/3/img/icons/add_to.gif" id="AddComment" onclick="AddComment(this)" />
			</td></tr></table>
