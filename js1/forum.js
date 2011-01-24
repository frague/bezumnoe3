var replyMessageId, forumId;

function ForumReply(a, id, forum_id) {
	if (!replyFormElement) {
		FindReplyElements();
	}
	if (!replyFormElement) {
		return false;
	} else {
		ForumClearReply();
		replyMessageId = id;
		forumId = forum_id;
		if (isVisible && lastLink == a) {
			CancelReply();
		} else {
			// Treat protected replies
			LockProtection(a.parentNode.previousSibling);

			insertAfter(replyFormElement, a.parentNode);
	   		isVisible = 1;
	   		if (replyTitleElement) {
	   			replyTitleElement.focus();
	   		}
	   	}
	}
	lastLink = a;
};

function LockProtection(el) {
	if (!el || !replyIsProtected) {
		return;
	}
	var state = (el.className.indexOf("Protected") >= 0);
	replyIsProtected.checked = state;
	replyIsProtected.disabled = state;
};


function ForumClearReply() {
	replyMessageId = "";
	forumId = "";
	ClearReply();
};

function AddMessage(lnk) {
	if (replyTitleElement) {
		lnk.disabled = true;
		params = MakeParametersPair("RECORD_ID", replyMessageId);
		params+= MakeParametersPair("FORUM_ID", forumId);
		params+= MakeParametersPair("TITLE", replyTitleElement.value);
		params+= MakeParametersPair("CONTENT", replyContentElement.value);
		params+= MakeParametersPair("IS_PROTECTED", replyIsProtected.checked ? 1 : 0);
		if ($("AUTH_NOW").checked) {
			params+= MakeParametersPair("AUTH", 0);
			params+= MakeParametersPair("login", $("login").value);
			params+= MakeParametersPair("password", $("password").value);
		} else if ($("AUTH_OPENID").checked) {
			
		}
		sendRequest(servicesPath + "forum.service.php", ForumMessageAddCallback, params, lastLink);
	}
};

function ForumMessageAddCallback(req, el) {
	var newRecord = '';
	var error = '';
	var logged_user = '';
	eval(req.responseText);
	if (!error) {
		CancelReply();
		var ul = FindTargetElement(el);
		if (ul) {
			var li = d.createElement("li");
			li.innerHTML = newRecord;

			if (ul.hasChildNodes()) {
				ul.insertBefore(li, ul.firstChild);
			} else {
				ul.appendChild(li);
			}
		}
	} else {
		if (replyErrorElement) {
			replyErrorElement.innerHTML = error;
		}
	}

	if (logged_user) {
		ShowLoggedLogin(logged_user);
	}

	var btn = $("SubmitMessageButton");
	if (btn) {
		btn.disabled = "";
	}
};

function ShowLoggedLogin(login) {
	$("Logged").innerHTML = login;
	$("LoggedLogin").innerHTML = login;
	$("AUTH_LOGGED").checked = true;
	$("LoggedLine").className = "";
};

function ForumDelete(a, id, forum_id) {
	params = MakeParametersPair("RECORD_ID", id);
	params+= MakeParametersPair("FORUM_ID", forum_id);
	params+= MakeParametersPair("go", "delete");

	sendRequest(servicesPath + "forum.service.php", ForumMessageDelCallback, params, a);
}

function ForumMessageDelCallback(req, a) {
	var error = '', className = '';
	eval(req.responseText);
	if (!error) {
		var li = FindParentTag("li", a);
		SetChildClass("li", li, className);
	} else {
		alert(error);	// TODO:
	}
};

var selectedProvider = "";
function SetOpenID(id, el, a) {
	el = $(el);
	if (!el || !id && !a) {
		return;
	}
	if (selectedProvider) {
		selectedProvider.className = "";
	}
	selectedProvider = a;
	a.className = "Selected";
	a.blur();
	el.value = id;
};

function SubmitOpenId(referer) {
	// TODO: Rewrite!!!

	var ref = $(referer);
	if (ref) {
		ref.value = d.location.href;
		d.forms[0].submit();
	}
};