var container = new MyFrame($('OptionsContainer'));
var content = new MyFrame($('OptionsContent'));
var alerts = new MyFrame($('AlertContainer'));
var winSize = new MyFrame(window);

function AdjustDivs(e) {
	if (!e) {
		var e = window.event;
	}

	winSize.GetPosAndSize();
	container.Replace(10, 10, winSize.width - 20, winSize.height - 20);
	content.Replace(-1, -1, -1, container.height - 40);

	alerts.Replace(-1, -1, winSize.width, winSize.height);
}

AdjustDivs();

window.onresize = AdjustDivs;
if (window.addEventListener) {
	window.addEventListener("resize", AdjustDivs, true);
};

/* Initial data request */

if (opener) {
	var me = opener.me;
	if (me) {
		var UploadFrame = $("uploadFrame");

		/* Tabs */
		var tabs = new Tabs($("OptionsContainer"), $("OptionsContent"));
		ProfileTab = new Tab(1, "Личные данные", 1, "");
		CurrentTab = ProfileTab;
		tabs.Add(CurrentTab);

		tabs.Add(new Tab(2, "Настройки", 1, "", function(tab){LoadAndBindSettingsToTab(tab,me.Id)}));

		var journalTab = new Tab(3, "Журнал", 1, "", function(tab){LoadAndBindJournalToTab(tab,me.Id)});
		tabs.Add(journalTab);

		UsersTab = new Tab(6, "Пользователи", 1, "", function(tab){LoadAndBindUsermanToTab(tab,me.Id)});
		tabs.Add(UsersTab);

		if (me.Rights >= adminRights) {
			MainTab = new Tab(7, "Администрирование", 1, "", function(tab){LoadAndBindAdminOptionsToTab(tab,me.Id)});
			tabs.Add(MainTab);
		} else {
        	MainTab = UsersTab;
        }

		tabs.Print();

		LoadAndBindProfileToTab(ProfileTab, me.Id);	// Loading profile to 1st tab
	} else {
		alert("Меню работает только пока вы находитесь в чате.");
	}
}
