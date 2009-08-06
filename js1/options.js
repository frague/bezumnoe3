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
		ProfileTab = new Tab(1, "������ ������", 1, "");
		CurrentTab = ProfileTab;
		tabs.Add(CurrentTab);

		tabs.Add(new Tab(2, "���������", 1, "", function(tab){new Settings().LoadTemplate(tab, me.Id)}));

		var journalTab = new Tab(3, "������", 1, "", function(tab){new Journal().LoadTemplate(tab, me.Id)});
		tabs.Add(journalTab);

		WakeupsTab = new Tab(5, "���������", 1, "", function(tab){new Wakeups().LoadTemplate(tab, me.Id)});
		tabs.Add(WakeupsTab);

		if (me.Rights >= adminRights) {
			tabs.Add(new Tab(6, "������������", 1, "", function(tab){new Userman().LoadTemplate(tab,me.Id)}));

			MainTab = new Tab(7, "�����������������", 1, "", function(tab){new AdminOptions().LoadTemplate(tab, me.Id)});
			tabs.Add(MainTab);
		} else {
        	MainTab = ProfileTab;
        }

		tabs.Print();

		new Profile().LoadTemplate(ProfileTab, me.Id);	// Loading profile to 1st tab
	} else {
		alert("���� �������� ������ ���� �� ���������� � ����.");
	}
}

var co = new Confirm();