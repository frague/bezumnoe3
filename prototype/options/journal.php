<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>
<h2>Журнал:</h2>

<h4>Доступные разделы:</h4>
<select id="FORUM_ID" name="FORUM_ID" class="Wide" onchange="this.obj.React()"></select>
<input type="checkbox" id="SHOW_JOURNALS" name="SHOW_JOURNALS" checked onchange="this.obj.Bind()"> <label for="SHOW_JOURNALS">Журналы</label> 
<input type="checkbox" id="SHOW_FORUMS" name="SHOW_FORUMS" onchange="this.obj.Bind()"> <label for="SHOW_FORUMS">Форумы</label> 
<input type="checkbox" id="SHOW_GALLERIES" name="SHOW_GALLERIES" onchange="this.obj.Bind()"> <label for="SHOW_GALLERIES">Галереи</label> 
<br />
<br />
<div id="Spoilers"></div>

<ul class="Links">
	<li> <a id="linkNewPost" href="javascript:void(0)" onclick="EditJournalPost(this.Tab.Journal, 0)" class="Add">Создать запись в журнале</a>
	<li> <a id="linkDeleteJournal" href="javascript:void(0)" onclick="" class="Delete">Удалить журнал</a>
</ul>