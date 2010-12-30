var usersFrame = new MyFrame($('Users'), 0, 100);
var formFrame = new MyFrame($('MessageForm'), 500);
var messagesFrame = new MyFrame($('Messages'), 500, 100);
var textLinesFrame = new MyFrame($('MessagesContainer'));
var statusFrame = new MyFrame($('Status'), 660);
var alerts = new MyFrame($('AlertContainer'));
var winSize = new MyFrame(window);

var configIndex = 0;

function AdjustDivs() {
	winSize.GetPosAndSize();
	LayoutConfigs[configIndex]();
	alerts.Replace(-1, -1, winSize.width, winSize.height);
	d.body.className = "Layout" + configIndex;
}


var LayoutConfigs = [
	function() {
		statusFrame.Replace(10, 10, winSize.width - 20, 28);
		usersFrame.Replace(10, statusFrame.y + statusFrame.height + 10, 150, winSize.height - 60);
		formFrame.Replace(10 + usersFrame.x + usersFrame.width, usersFrame.y, winSize.width - 20 - usersFrame.x - usersFrame.width, -1);

		messagesFrame.Replace(
			formFrame.x,
			formFrame.y + formFrame.height + 10,
			formFrame.width + 2,
			winSize.height - 80 - formFrame.height);

		textLinesFrame.Replace(-1, -1, -1, messagesFrame.height - 40);
	},
	function() {
		statusFrame.Replace(10, 10, winSize.width - 20, 26);
		usersFrame.Replace(winSize.width - 160, statusFrame.y + statusFrame.height + 10, 150, winSize.height - 60);
		formFrame.Replace(10, usersFrame.y, winSize.width - 30 - usersFrame.width, -1);

		messagesFrame.Replace(
			formFrame.x,
			formFrame.y + formFrame.height + 10,
			formFrame.width + 2,
			winSize.height - 80 - formFrame.height);

		textLinesFrame.Replace(-1, -1, -1, messagesFrame.height - 40);
	},
	function() {
		statusFrame.Replace(10, 10, winSize.width - 20, 28);
		usersFrame.Replace(10, statusFrame.y + statusFrame.height + 10, 150, winSize.height - statusFrame.height - 30);

		messagesFrame.Replace(
			10 + usersFrame.x + usersFrame.width,
			usersFrame.y,
			winSize.width - usersFrame.width - 30,
			winSize.height - 80 - formFrame.height);

		textLinesFrame.Replace(-1, -1, -1, messagesFrame.height - 40);

		formFrame.Replace(messagesFrame.x, winSize.height - 110, winSize.width - 20 - usersFrame.x - usersFrame.width, 100);
	},
	function() {
		statusFrame.Replace(10, 10, winSize.width - 20, 28);

		messagesFrame.Replace(
			10,
			statusFrame.y + statusFrame.height + 10,
			winSize.width - usersFrame.width - 28,
			winSize.height - 70 - formFrame.height);

		textLinesFrame.Replace(-1, -1, -1, messagesFrame.height - 40);

		usersFrame.Replace(winSize.width - 160, messagesFrame.y, 150, winSize.height - statusFrame.height - 30);
		formFrame.Replace(messagesFrame.x, winSize.height - 110, winSize.width - usersFrame.width - 30, 100);
	}
];














AdjustDivs();

window.onresize = AdjustDivs;
if (window.addEventListener) {
	window.addEventListener("resize", AdjustDivs, true);
};

var textForm = $('Message');
function _s(text, erase) {
	if (textForm) {
		textForm.value = (erase ? "" : textForm.value) + text;
		textForm.focus();
	}
};

function _(text, erase) {
	_s(text + ", ", erase);
};

function __(el) {
	if (el.hasChildNodes && el.childNodes[0].hasChildNodes && el.childNodes[0].childNodes[0]) {
		el = el.childNodes[0];
	}
	_(el.innerText || el.textContent);
};