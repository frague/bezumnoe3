<?
    require "menu_base.php";

    if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
        exit();
    }

?>

<h4>Показывать только комнаты:</h4>
<input type="checkbox" id="locked" name="locked"> <label for="locked">заблокированные</label>&nbsp;
<input type="checkbox" id="by_invitation" name="by_invitation"> <label for="by_invitation">с допуском</label>&nbsp;
<input type="checkbox" id="deleted" name="deleted" checked> <label for="deleted">не удалённые</label>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="ReRequestData(this)" class="Refresh" id="RefreshRooms">Обновить список комнат/применить фильтр</a>
</ul>

<table cellpadding="2" cellspacing="0" id="RoomsGrid" class="Grid"><tbody><tr><th style="width:100%">Комната</th><th>Операции</th></tr></table>

<ul class="Links">
    <li> <a href="javascript:void(0)" onclick="AddNewRoom(this)" class="Add" id="AddRoom">Новая комната</a>
</ul>

