<?
    require "menu_base.php";

    if ($user->IsEmpty()) {
        exit();
    }

?>

<h2 id="Title">##TITLE##</h2>

<div>
    <input type="hidden" name="FORUM_ID" id="FORUM_ID" />
    <div id="Spoilers"></div>

    <ul class="Links" id="Links">
        <li> <a id="linkNewPost" href="javascript:void(0)" onclick="EditJournalPost(this.obj, 0)" class="Add">Создать новую запись</a>
        <li> <a id="linkDeleteJournal" href="javascript:void(0)" onclick="" class="Delete">Удалить журнал</a>
    </ul>
</div>
