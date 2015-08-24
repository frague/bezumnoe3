var menuInitilized = false;

function InitMenu(div) {
	var menu = new MenuItemsCollection(true);
	var main = new MenuItem(1, "Команды");

	main.SubItems.Items.Add(new MenuItem(3, "Отойти&nbsp;(Away)", "MI('away')"));
	main.SubItems.Items.Add(new MenuItem(4, "Сменить статус", "MI('status')"));

	main.SubItems.Items.Add(new MenuItem(5, "Сменить никнейм", "ChangeName()"));
	if (me.Rights >= topicRights) {
		var t = new MenuItem(6, "Сменить тему", "MI('topic')");
		if (me.Rights >= adminRights) {
			t.SubItems.Items.Add(new MenuItem(1, "С блокировкой", "MI('locktopic')"));
			t.SubItems.Items.Add(new MenuItem(2, "Разблокировать", "MI('unlocktopic')"));
		}
		main.SubItems.Items.Add(t);
	}
	main.SubItems.Items.Add(new MenuItem(7, "<b>Меню</b>", "ShowOptions()"));
	main.SubItems.Items.Add(new MenuItem(8, "Выход из чата", "MI('quit')"));

	menu.Items.Add(main);
	menu.Create(div);
	
	menuInitilized = true;
};
