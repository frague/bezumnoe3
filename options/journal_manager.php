<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>

<h2>Журналы/Форумы/Фотогалереи:</h2>

<div>
	<div id="Selector">
		<h4>Поиск:</h4>
		<INPUT id="SEARCH" name="SEARCH" class="Wide" onchange="this.obj.Request()" />
		<input type="checkbox" id="SHOW_JOURNALS" name="SHOW_JOURNALS" checked onchange="this.obj.Request()"> <label for="SHOW_JOURNALS">Журналы</label> 
		<input type="checkbox" id="SHOW_FORUMS" name="SHOW_FORUMS" onchange="this.obj.Request()"> <label for="SHOW_FORUMS">Форумы</label> 
		<input type="checkbox" id="SHOW_GALLERIES" name="SHOW_GALLERIES" onchange="this.obj.Request()"> <label for="SHOW_GALLERIES">Фотогалереи</label> 
	</div>

	<div id="ForumsContainer"></div>

	<ul class="Links">
		<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" id="linkRefresh" class="Refresh">Обновить данные с сервера</a>
		<li id="CreateJournal"> <a href="javascript:void(0)" onclick="CreateForum(this.obj)" id="linkNewForum" class="Add">Создать журнал</a>
	</ul>
</div>
