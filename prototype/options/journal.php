<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>
<h2>Журнал:</h2>

<div id="Spoilers"></div>

<ul class="Links">
	<li> <a id="linkNewPost" href="javascript:void(0)" onclick="EditJournalPost(this.Tab.Journal, 0)" class="Add">Создать запись в журнале</a>
	<li> <a id="linkDeleteJournal" href="javascript:void(0)" onclick="" class="Delete">Удалить журнал</a>
</ul>