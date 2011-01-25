// Scripts related to site pages only, not menu

var alerts = new MyFrame($('AlertContainer'));
var winSize = new MyFrame(window);

function AdjustDivs() {
	winSize.GetPosAndSize();
	alerts.Replace(-1, -1, winSize.width, winSize.height);
}


DisplayElement(alerts.element, false);
AdjustDivs();

window.onresize = AdjustDivs;
if (window.addEventListener) {
	window.addEventListener("resize", AdjustDivs, true);
};
