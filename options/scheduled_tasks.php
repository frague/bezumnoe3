<?
    require "menu_base.php";

	if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();
	}

?>

<h4>Фильтр по типу задач:</h4>
<table class="Wide">
	<tr>
		<td width="50%">
			<input type="checkbox" id="status" name="status"> <label for="status">смена статуса (старожил)</label><br />
			<input type="checkbox" id="unban" name="unban" checked> <label for="unban">бан-амнистия</label>
			</td>
		<td width="50%">
			<input type="checkbox" id="expired_sessions" name="expired_sessions" checked> <label for="expired_sessions">истекшие сесии</label>
			</td></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshScheduledTasks">Обновить список задач/применить фильтр</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="ScheduledTasksGrid" class="Grid"><tbody><tr><th style="width:10px">Статус</th><th>Задача</th><th style="width:150px">Дата исполнения</th><th style="width:20px">Период (сек.)</th><th style="width:20px">Операции</th></tr></table>
