//7.3
/*
	Journal messages grid. Edit & delete buttons.
*/

// Global reference to update messages list
var journalMessagesObj;

function JournalMessages() {
	this.fields = ["SEARCH", "LOGIN", "FORUM_ID", "SHOW_FORUMS", "SHOW_JOURNALS", "SHOW_GALLERIES"];
	this.ServicePath = servicesPath + "journal.messages.service.php";
	this.Template = "journal_messages";
	this.ClassName = "JournalMessages";

	this.GridId = "MessagesGrid";

	journalMessagesObj = this;
	this.ForumsLoaded = 0;
};

JournalMessages.prototype = new PagedGrid();

JournalMessages.prototype.InitPager = function() {
	this.Pager = new Pager(this.Inputs[this.PagerId], function(){this.Tab.JournalMessages.SwitchPage()}, this.PerPage);
};

JournalMessages.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data, obj.Total);

		if (!this.ForumsLoaded) {
			obj.DisplayTabElement("TARGET", (obj.forums && obj.forums.length > 0));
			if (obj.forums) {
				obj.BindForums();
				this.ForumsLoaded = 1;
			}
		}
	}
};

JournalMessages.prototype.BindForums = function(request) {
	var select = this.Inputs["FORUM_ID"];
	var showForums = this.Inputs["SHOW_FORUMS"].checked;
	var showJournals = this.Inputs["SHOW_JOURNALS"].checked;
	var showGalleries = this.Inputs["SHOW_GALLERIES"].checked;

	if (select) {
		select.innerHTML = "";
		for (i = 0, l = this.forums.length; i < l; i++) {
			var item = this.forums[i];
			switch (item.TYPE) {
				case "f":
					if (!showForums) {
						continue;
					}
					break;
				case "g":
					if (!showGalleries) {
						continue;
					}
					break;
				case "j":
					if (!showJournals) {
						continue;
					}
					break;
			}
			item.ToString(i, this, select);
			
		}
		if (request) {
			this.Request();
		}
	}
};

JournalMessages.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);

	this.GroupSelfAssign(["buttonSearch", "ResetFilter", "FORUM_ID", "SHOW_FORUMS", "SHOW_JOURNALS", "SHOW_GALLERIES"]);
	this.DisplayTabElement("TARGET", 0);
	BindEnterTo(this.Inputs["SEARCH"], this.Inputs["buttonSearch"]);
};


/* Journal Record Data Transfer Object */

function jrdto(id, title, content, date, comments, type) {
	this.fields = ["Id", "Title", "Content", "Date", "Comments", "Type"];
	this.Init(arguments);
};

jrdto.prototype = new DTO();

jrdto.prototype.ToString = function(index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
	var h2 = d.createElement("h2");
	if (this.Type) {
		h2.className = (this.Type == "1" ? "Friends" : "Private");
	}
	h2.innerHTML = this.Title;
	td1.appendChild(h2);
	td1.innerHTML += this.Content;
	tr.appendChild(td1);

	var td2 = d.createElement("td");
	td2.className = "Centered";
	    var comments = Math.round(this.Comments);
	    if (comments) {
			var a = d.createElement("a");
			a.innerHTML = comments;
			a.jrdto = this;
			a.obj = obj;
			a.href = voidLink;
			a.onclick = function() {ShowMessageComments(this)};
			td2.appendChild(a);
		} else {
			td2.innerHTML = comments;
		}
	tr.appendChild(td2);

	var td3 = d.createElement("td");
	td3.className = "Centered";
		td3.appendChild(MakeButton("EditRecord(this,"+this.Id+")", "icons/edit.gif", obj));
		td3.appendChild(MakeButton("DeleteRecord(this,"+this.Id+")", "delete_icon.gif", obj));
	tr.appendChild(td3);
	
	return tr;
};

/* Forum line DTO */

function fldto(forum_id, access, title, type, login) {
	this.fields = ["FORUM_ID", "ACCESS", "TITLE", "TYPE", "LOGIN"];
	this.Init(arguments);
};

fldto.prototype = new DTO();

fldto.prototype.ToString = function(index, obj, select) {
    var prefix = "[Форум] ";
	switch (this.TYPE) {
		case "g":
			prefix = "[Галерея] ";
			break;
		case "j":
			prefix = "[Журнал] ";
			break;
	}
	AddSelectOption(select, prefix + " \"" + this.TITLE + "\" " + " (" + this.LOGIN + ")", this.FORUM_ID, this.FORUM_ID == obj.FORUM_ID);
};


/* Actions */

function EditRecord(a, post_id) {
	EditJournalPost(a.obj, post_id)
};


// TODO: Rewrite using Requestor
function DeleteRecordConfirmed(obj, id) {
	obj.Tab.Alerts.Clear();
	var params = MakeParametersPair("go", "delete");
	params += MakeParametersPair("USER_ID", obj.USER_ID);
	params += MakeParametersPair("RECORD_ID", id);
	sendRequest(post_service, DeleteCallback, params, obj);

};

function DeleteCallback(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		if (!obj.Tab.Alerts.HasErrors) {
			obj.Request();
		}
	}
};


/* Confirms */

function DeleteRecord(a, id) {
	co.Show(function() {DeleteRecordConfirmed(a.obj, id)}, "Удалить запись?", "Запись в блоге и все комментарии к ней будут удалены.<br>Продолжить?");
};

