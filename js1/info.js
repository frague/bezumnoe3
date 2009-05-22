var container = new MyFrame($('InfoContainer'));
var content = new MyFrame($('InfoContent'));
var winSize = new MyFrame(window);

function AdjustDivs(e) {
	if (!e) {
		var e = window.event;
	}

	winSize.GetPosAndSize();
	container.Replace(10, 10, winSize.width - 20, winSize.height - 20);
	content.Replace(-1, -1, -1, container.height - 40);
}

AdjustDivs();

window.onresize = AdjustDivs;
if (window.addEventListener) {
	window.addEventListener("resize", AdjustDivs, true);
};

/* Tabs */
var tabs = new Tabs($("InfoContainer"), $("InfoContent"));
CurrentTab = new Tab(1, "Инфо", 1);
tabs.Add(CurrentTab, $('Info'));
tabs.Add(new Tab(2, "Блабла1", 1));
tabs.Add(new Tab(3, "Блабла2", 1));
tabs.Print();
