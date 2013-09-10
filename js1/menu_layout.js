// Scripts related to menu only


var container = new MyFrame($("#OptionsContainer")[0], 580, 400);
var content = new MyFrame($("#OptionsContent")[0]);
var alerts = new MyFrame($("#AlertContainer")[0]);
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
		var UploadFrame = $("#uploadFrame")[0];

		/* Tabs */
		var tabs = new Tabs($("#OptionsContainer")[0], $("#OptionsContent")[0]);
		ProfileTab = new Tab(1, "Личные данные", 1, "");
		CurrentTab = ProfileTab;
		tabs.Add(CurrentTab);

		tabs.Add(new Tab(2, "Настройки", 1, "", function(tab){new Settings().LoadTemplate(tab, me.Id)}));

		var journalTab = new Tab(3, "Журнал", 1, "", function(tab){new JournalsManager().LoadTemplate(tab, me.Id)});
		tabs.Add(journalTab);

		WakeupsTab = new Tab(5, "Сообщения", 1, "", function(tab){new Wakeups().LoadTemplate(tab, me.Id)});
		tabs.Add(WakeupsTab);

		if (me.Rights >= adminRights) {
			tabs.Add(new Tab(6, "Пользователи", 1, "", function(tab){new Userman().LoadTemplate(tab,me.Id)}));

			MainTab = new Tab(7, "Администрирование", 1, "", function(tab){new AdminOptions().LoadTemplate(tab, me.Id)});
			tabs.Add(MainTab);
		} else {
        	MainTab = ProfileTab;
        }

		tabs.Print();

		new Profile().LoadTemplate(ProfileTab, me.Id);	// Loading profile to 1st tab
	} else {
		alert("Меню работает только пока вы находитесь в чате.");
	}
}

var co = new Confirm();
