//4.1
/*
	Journal messages grid. Edit & delete buttons.
*/

// Global reference to update messages list
var journalMessagesObj;

function JournalMessages() {
	this.fields = new Array("SEARCH", "LOGIN");
	this.ServicePath = servicesPath + "journal.messages.service.php";
	this.Template = "journal_messages";

	this.GridId = "MessagesGrid";

	journalMessagesObj = this;
};

JournalMessages.prototype = new PagedGrid();

JournalMessages.prototype.InitPager = function() {
	this.Pager = new Pager(this.Inputs[this.PagerId], function(){this.Tab.JournalMessages.SwitchPage()}, this.PerPage);
};

JournalMessages.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data, obj.Total);
	}
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

/* Helper methods */

function LoadAndBindJournalMessagesToTab(tab, user_id, login) {
	LoadAndBindObjectToTab(tab, user_id, new JournalMessages(), "JournalMessages", JournalMessagesOnLoad, login);
};

function JournalMessagesOnLoad(req, tab) {
	if (tab) {
		ObjectOnLoad(req, tab, "JournalMessages");

		var jm = tab.JournalMessages;
		jm.GroupSelfAssign(["buttonSearch", "ResetFilter"]);
		BindEnterTo(jm.Inputs["SEARCH"], jm.Inputs["buttonSearch"]);
	}
};

/* Actions */

function EditRecord(a, post_id) {
	EditJournalPost(a.obj, post_id)
};

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

