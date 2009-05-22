var container = new MyFrame($('WakeupContainer'));
var replyDiv = $('WakeupReply');
var reply = new MyFrame(replyDiv);
var winSize = new MyFrame(window);
var replyFormHeight = 75;
var offset = 0;
var inputReply = $('reply');
var statusLabel = $('status');

function AdjustDivs(e) {
	if (!e) {
		var e = window.event;
	}

	winSize.GetPosAndSize();
	container.Replace(10, 40, winSize.width - 20, winSize.height - 50 - offset);
	reply.Replace(10, winSize.height - replyFormHeight, winSize.width - 20, replyFormHeight - 10);
}

AdjustDivs();

window.onresize = AdjustDivs;
if (window.addEventListener) {
	window.addEventListener("resize", AdjustDivs, true);
};

function ReplyForm() {
	replyDiv.style.display = offset ? "none" : "";
	offset = replyFormHeight - offset;
	AdjustDivs();
	inputReply.focus();
}

function Send(message_id) {
	var s = MakeParametersPair("message", inputReply.value);
	s += MakeParametersPair("reply_to", message_id);
	sendRequest("../services/wakeup.service.php", MessageAdded, s);
	status.innerHTML = "Отправка сообщения...";
	inputReply.value = "";
}

function MessageAdded(req) {
	if (req.responseText) {
		statusLabel.className = req.responseText.charAt(0) == '-' ? "Fail" : "Succeed";
		statusLabel.innerHTML = req.responseText.substring(1, req.responseText.length);
	}
	setTimeout("self.close()", 2000);
}
