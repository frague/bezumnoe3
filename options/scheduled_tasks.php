<?
    require "menu_base.php";

	if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();
	}

?>

<h4>������ �� ���� �����:</h4>
<table class="Wide">
	<tr>
		<td width="50%">
			<input type="checkbox" id="status" name="status"> <label for="status">����� ������� (��������)</label><br />
			<input type="checkbox" id="unban" name="unban" checked> <label for="unban">���-��������</label><br />
			<input type="checkbox" id="expired_sessions" name="expired_sessions" checked> <label for="expired_sessions">�������� �����</label><br />
			<input type="checkbox" id="ratings" name="ratings" checked> <label for="ratings">���������� ��������</label><br />
			<input type="checkbox" id="inactivated" name="inactivated" checked> <label for="inactivated">���������������� ��������</label><br />
			</td>
		<td width="50%">
			<input type="checkbox" id="ytka" name="ytka" checked> <label for="ytka">���: YTKA</label><br />
			<input type="checkbox" id="victorina" name="victorina" checked> <label for="victorina">���: BUKTOPUHA</label><br />
			<input type="checkbox" id="lingvist" name="lingvist" checked> <label for="lingvist">���: Lingvist</label><br />
			</td></tr></table>

<ul class="Links">
	<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshScheduledTasks">�������� ������ �����/��������� ������</a>
</ul>

<div id="Pager"></div>
<table cellpadding="2" cellspacing="0" id="ScheduledTasksGrid" class="Grid"><tbody><tr><th style="width:10px">������</th><th>������</th><th style="width:150px">���� ����������</th><th style="width:20px">������ (���.)</th><th style="width:20px">��������</th></tr></table>
