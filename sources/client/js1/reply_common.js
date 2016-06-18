var hiderElement, replyFormElement;
var isVisible = 0, lastLink;
var replyErrorElement, replyTitleElement, replyContentElement, replyIsProtected;

var replyMessageId, forumId;

var citeLevel;

var brClean = new RegExp("(<br[^>]*>)", "g");
var addCite = new RegExp("(\\n)", "g");

function FindReplyElements() {
    // Finds all reply form elements necessary

    hiderElement = $("#Hider")[0];
    replyFormElement = $("#ReplyForm")[0];

    replyErrorElement = $("#ERROR");
    replyTitleElement = $("#TITLE");
    replyContentElement = $("#CONTENT");
    replyIsProtected = $("#IS_PROTECTED");
};

function SubstrCount(s, subStr, offset) {
    var ex = new RegExp("(" + subStr + ")");
    while (s.match(ex)) {
        citeLevel += offset;
        s = s.replace(ex, "");
    }
    return s;
}

function MakeCite() {
    // Makes cite from text
    if (lastLink) {
        var text = lastLink.parentNode.parentNode.previousSibling.innerHTML;
        text = text.replace(brClean, "");

        cites = new Array(10).join('>');
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
            replyContentElement.val(text);
        }
    }
};

function ClearHash() {
    document.location.hash = "";
};

function CancelReply() {
    hiderElement.appendChild(replyFormElement);
    isVisible = 0;
    ClearHash();
};

function FindTargetElement(el) {
    var div = el.parentNode.parentNode;
    if (div.nextSibling) {
        return div.nextSibling;
    } else {
        var ul = d.createElement("ul");
        div.parentNode.appendChild(ul);
        return ul;
    }
};

function FindParentTag(tag, el, skip)  {
    var parent = el.parentNode;
    if (!skip) {
        skip = 0;
    };
    if (parent && parent.tagName) {
        if (parent.tagName.toLowerCase() === tag) {
            if (!skip) {
                return parent;
            }
            skip--;
        }
        return FindParentTag(tag, parent, skip);
    }
    return null;
};

function SetElementClass(tag, el, className) {
    if (el.tagName && el.tagName.toLowerCase() === tag) {
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

var linkExpr = new RegExp('^#[a-z]\\d+$');
var linkNewExpr = new RegExp('^#new_comment$');

// Opens reply form after authentication
function OpenReplyForm() {
    var l = window.location.hash;
    if (l) {
        if (linkExpr.test(l)) {
            $('a[name='+l.substr(1)+']').click();
        } else if (linkNewExpr.test(l)) {
            $('a[name=new_comment]').click();
        }
    }
};

function ForumReply(a, id, forum_id) {
    $("#callback").val(a.href);
    if (!GetCurrentSession()) {
        $("#auth_form").dialog("open");
        return false;
    }

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
            replyErrorElement.hide();

            insertAfter(replyFormElement, a.parentNode.parentNode);
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
    var state = (el.className && el.className.indexOf("Protected") >= 0);
    replyIsProtected.attr('checked', state);
    if (state)
        replyIsProtected.attr('disabled', 'disabled');
    else
        replyIsProtected.removeAttr('disabled');
};

// Clears reply form
function ClearReply() {
    if (replyTitleElement) {
        replyErrorElement.html('');
        replyTitleElement.val('');
        replyContentElement.val('');
    }
};

// Clears form and message and forum relations
function ForumClearReply() {
    replyMessageId = "";
    forumId = "";
    ClearReply();
};

// Sends new message information to server
function AddMessage(lnk) {
    // Tries to submit the form
    if (replyTitleElement) {
        lnk.disabled = true;
        var s = new ParamsBuilder()
            .add('RECORD_ID', replyMessageId)
            .add('FORUM_ID', forumId)
            .add('TITLE', replyTitleElement.val())
            .add('CONTENT', replyContentElement.val())
            .add('IS_PROTECTED', replyIsProtected.is('checked') ? 1 : 0);

        sendRequest(servicesPath + "forum.service.php", ForumMessageAddcallback, s.build(), lastLink);
    }
};

// New message adding callback
function ForumMessageAddcallback(reesponseText, el) {
    var newId = "", newRecord = "", error = "", logged_user = "";
    eval(reesponseText);

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
            if (newId) {
                window.location.hash = "cm" + newId;
            }
        }
    } else {
        if (replyErrorElement) {
            replyErrorElement.html(error).show().delay(5000).hide('blind', {}, 'slow');
        }
    }
    $("#SubmitMessageButton").removeAttr("disabled");
};

// Message deletion
function ForumDelete(a, id, forum_id) {
    var s = new ParamsBuilder()
        .add('RECORD_ID', id)
        .add('FORUM_ID', forum_id)
        .add('go', 'delete');

    sendRequest(servicesPath + "forum.service.php", ForumMessageDelcallback, s.build(), a);
}

// Deletion callback
function ForumMessageDelcallback(responseText, a) {
    var error = "", className = "";
    eval(responseText);
    if (!error) {
        var li = FindParentTag("li", a, 1);
        SetChildClass("li", li, className);
    } else {
        alert(error);   // TODO:
    }
};

// OpenID provider visual selection
var selectedProvider = "";
function SetOpenID(id, el, a) {
    el = $("#" + el);
    if (!el || !id || !a) {
        return;
    }
    if (selectedProvider) {
        selectedProvider.className = "";
    }
    selectedProvider = a;
    a.className = "Selected";
    a.blur();
    el[0].val(id);
};

function startup() {
    OpenReplyForm();
};
