var hiderElement, replyFormElement;
var isVisible = 0, lastLink;
var replyErrorElement, replyTitleElement, replyContentElement, replyIsProtected;

var brClean = new RegExp("(<br[^>]*>)", "g");
var addCite = new RegExp("(\\n)", "g");

function FindReplyElements() {
	hiderElement = $("Hider");
	replyFormElement = $("ReplyForm");
	replyErrorElement = $("ERROR");
	replyTitleElement = $("TITLE");
	replyContentElement = $("CONTENT");
	replyIsProtected = $("IS_PROTECTED");
	authType = $("AUTH");
};

var citeLevel;

function SubstrCount(s, subStr, offset) {
	var ex = new RegExp("(" + subStr + ")");
	while (s.match(ex)) {
		citeLevel += offset;
		s = s.replace(ex, "");
	}
	return s;
}

function MakeCite() {
	if (lastLink) {
		var text = lastLink.parentNode.previousSibling.innerHTML;
		text = text.replace(brClean, "");

		cites = ">>>>>>>>>>>>";
		citeLevel = 1;

		lines = text.split("\n");
		text = "";
		for (i = 0; i < lines.length; i++) {
			line = lines[i];

			line = SubstrCount(line, "<cite>", 1);
			line = SubstrCount(line, "</cite>", -1);

			text += cites.substr(0, citeLevel) + line + "\n";
		}

		if (replyContentElement) {
			replyContentElement.value = text;
		}
	}
};

function ClearReply() {
	if (replyTitleElement) {
		replyErrorElement.innerHTML = "";
		replyTitleElement.value = "";
		replyContentElement.value = "";

		$("login").value = "";
		$("password").value = "";
		if ($("LoggedLine").className) {
			$("AUTH_NOW").checked = true;
		} else {
			$("AUTH_LOGGED").checked = true;
		}
	}
};

function CancelReply() {
	hiderElement.appendChild(replyFormElement);
	isVisible = 0;
};

function FindTargetElement(el) {
	var div = el.parentNode;
	if (div.nextSibling) {
		return div.nextSibling;
	} else {
		var ul = d.createElement("ul");
		div.parentNode.appendChild(ul);
		return ul;
	}
};

function FindParentTag(tag, el)  {
	var parent = el.parentNode;
	if (parent) {
		if (parent.tagName.toLowerCase() == tag) {
			return parent;
		} else {
			return FindParentTag(tag, parent);
		}
	}
	return "";
};

function SetElementClass(tag, el, className) {
	if (el.tagName && el.tagName.toLowerCase() == tag) {
		el.className = className;
	}
};

function SetChildClass(tag, el, className) {
	if (!el) {
		return;
	}

	SetElementClass(tag, el, className);

	for (var i = 0, l = el.childNodes.length; i < l; i++) {
		var child = el.childNodes[i];
		SetElementClass(tag, child, className);

		if (child.hasChildNodes()) {
			SetChildClass(tag, child, className);
		}
	}
};

function OpenReplyForm() {
	var l = window.location.hash;
	if (l) {
		var a = document.getElementsByName(l.substr(1));
		if (a && a.length > 0) {
			a[0].onclick();
		}
	}
};
