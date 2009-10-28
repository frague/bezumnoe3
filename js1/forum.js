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

function AddMessage() {
	if (replyTitleElement) {
		params = MakeParametersPair("RECORD_ID", replyMessageId);
		params+= MakeParametersPair("FORUM_ID", forumId);
		params+= MakeParametersPair("TITLE", replyTitleElement.value);
		params+= MakeParametersPair("CONTENT", replyContentElement.value);
		params+= MakeParametersPair("IS_PROTECTED", replyIsProtected.checked ? 1 : 0);
		sendRequest(servicesPath + "forum.service.php", ForumMessageAddCallback, params, lastLink);
	}
};

function ForumMessageAddCallback(req, el) {
	var newRecord = '';
	var error = '';
	eval(req.responseText);
	if (!error) {
		CancelReply();
		var ul = FindTargetElement(el);
		if (ul) {
			var li = d.createElement("li");
			li.innerHTML = newRecord;
/*			alert(newRecord);
			try {
				li.innerHTML = newRecord;
			} catch (e) {
				alert(e.description);
			}*/

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