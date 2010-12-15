<?
    require "menu_base.php";

	if ($user->IsEmpty() || !$user->IsSuperAdmin()) {
		exit();
	}

?>

<h4>Запуск нового бота:</h4>
<input type="radio" name="TYPE" id="TYPE_YTKA" checked> <label for="TYPE_YTKA"><b>YTKA</b> &mdash; простой автоответчик</label><br />
<input type="radio" name="TYPE" id="TYPE_VICTORINA"> <label for="TYPE_VICTORINA"><b>BUKTOPUHA</b> &mdash; вопросы/ответы</label><br />
<input type="radio" name="TYPE" id="TYPE_LINGVIST"> <label for="TYPE_LINGVIST"><b>Lingvist</b> &mdash; контроль за соблюдением правил</label><br />

<h4>Пользователь</h4>
<input type="text" name="USER" id="USER" class="Wide" /><br />
<p class="Note">Начните печатать логин или никнейм</p>

<h4>Комната</h4>
<select name="ROOM" id="ROOM" class="Wide"><option></select>

