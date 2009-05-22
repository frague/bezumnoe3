var usersFrame = new MyFrame($('Users'));
var formFrame = new MyFrame($('MessageForm'));
var messagesFrame = new MyFrame($('Messages'));
var textLinesFrame = new MyFrame($('MessagesContainer'));
var statusFrame = new MyFrame($('Status'));
var alerts = new MyFrame($('AlertContainer'));
var winSize = new MyFrame(window);

var configIndex = 0;

function AdjustDivs() {
	winSize.GetPosAndSize();
	LayoutConfigs[configIndex]();
	alerts.Replace(-1, -1, winSize.width, winSize.height);
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

		formFrame.Replace(messagesFrame.x, winSize.height - 70, winSize.width - 20 - usersFrame.x - usersFrame.width, 60);
	},
	function() {
		statusFrame.Replace(10, 10, winSize.width - 20, 28);

		messagesFrame.Replace(
			10,
			statusFrame.y + statusFrame.height + 10,
			winSize.width - usersFrame.width - 28,
			winSize.height - 80 - formFrame.height);

		textLinesFrame.Replace(-1, -1, -1, messagesFrame.height - 40);

		usersFrame.Replace(winSize.width - 160, messagesFrame.y, 150, winSize.height - statusFrame.height - 30);
		formFrame.Replace(messagesFrame.x, winSize.height - 70, winSize.width - usersFrame.width - 30, 60);
	}
];














AdjustDivs();

window.onresize = AdjustDivs;
if (window.addEventListener) {
	window.addEventListener("resize", AdjustDivs, true);
};

var textForm = $('Message');
function _(text, erase) {
	if (textForm) {
		textForm.value = (erase ? "" : textForm.value) + text + ", ";
		textForm.focus();
	}
}
