<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>

<h2>Журнал:</h2>

<div id="journal_exists">
	<div id="no_journals">
		<p>Журнал (блог) позволяет вам вести в интернете персональный дневник, в котором вы сможете публиковать
		различные сообщения в хронологическом порядке. Зарегистрированные пользователи чата смогут комментировать
		сообщения вашего журнала.
		<p>Для создания собственного журнала зайдите в настройки, введите название и описание вашего журнала и нажмите
		кнопку "Готово". Ваш журнал будет создан.
	</div>

	<div id="Selector">
		<h4 id="Title">Доступные разделы:</h4>
		<select id="FORUM_ID" name="FORUM_ID" class="Wide" onchange="this.obj.React()"></select>
		<input type="checkbox" id="SHOW_JOURNALS" name="SHOW_JOURNALS" checked onchange="this.obj.Bind()"> <label for="SHOW_JOURNALS">Журналы</label> 
		<input type="checkbox" id="SHOW_FORUMS" name="SHOW_FORUMS" onchange="this.obj.Bind()"> <label for="SHOW_FORUMS">Форумы</label> 
		<input type="checkbox" id="SHOW_GALLERIES" name="SHOW_GALLERIES" onchange="this.obj.Bind()"> <label for="SHOW_GALLERIES">Галереи</label> 
	</div>

	<div id="Spoilers"></div>

	<ul class="Links" id="Links">
		<li> <a id="linkNewPost" href="javascript:void(0)" onclick="EditJournalPost(this.obj, 0)" class="Add">Создать запись в журнале</a>
		<li> <a id="linkDeleteJournal" href="javascript:void(0)" onclick="" class="Delete">Удалить журнал</a>
	</ul>
</div>
