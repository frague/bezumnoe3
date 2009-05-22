//3.1
/*
	Journal comments grid. Edit & delete buttons.
*/

function JournalComments() {
	this.ServicePath = servicesPath + "journal.comments.service.php";
	this.Template = "journal_comments";
	this.Columns = 3;
};

JournalComments.prototype = new Comments();

JournalComments.prototype.Gather = function() {
	return MakeParametersPair("RECORD_ID", this.jrdto.Id) + this.BaseGather();
};

JournalComments.prototype.InitPager = function() {
	this.Pager = new Pager(this.Inputs[this.PagerId], function(){this.Tab.JournalComments.SwitchPage()}, this.PerPage);
};

JournalComments.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data, 0);
	}
};

/* Journal Record Data Transfer Object */

function jcdto(id, user_id, name, title, content, date, is_hidden, is_deleted) {
	this.fields = ["Id", "UserId", "Name", "Title", "Content", "Date", "IsHidden", "IsDeleted"];
	this.Init(arguments);
};

jcdto.prototype = new DTO();

jcdto.prototype.ToString = function(index, obj) {
	var tr = MakeGridRow(index);
	if (this.IsHidden) {
		tr.className += " Hidden";
	}
	if (this.IsDeleted) {
		tr.className += " Deleted";
	}

	var td1 = d.createElement("td");
	var h2 = d.createElement("h2");
	h2.innerHTML = "&laquo;" + this.Title + "&raquo;";
	td1.appendChild(h2);

	var span = d.createElement("span");
	span.innerHTML = this.Content;
	td1.appendChild(span);

	var div = d.createElement("div");
	div.innerHTML = (this.UserId ? "<b>" : "") + this.Name + (this.UserId ? "</b>" : "") + ",	" + this.Date;
	td1.appendChild(div);
	tr.appendChild(td1);

	var td3 = d.createElement("td");
	td3.className = "Centered";
		td3.appendChild(MakeButton("DeleteComment(this,"+this.Id+")", "delete_icon.gif", obj));
		if (this.UserId) {
			td3.appendChild(MakeButton("AddForbiddenCommenter("+this.UserId+",this)", "icons/remove_user.gif", obj, "", "Запретить комментарии"));
		}
	tr.appendChild(td3);
	
	return tr;
};

/* Helper methods */

function LoadAndBindJournalCommentsToTab(tab, user_id, login) {
	var jc = new JournalComments();
	// Important!
	jc.jrdto = tab.PARAMETER;
	jc.TITLE = jc.jrdto.Title;

	/* Update tab title */
	tab.Title = "Комментарии к&nbsp;&laquo;" + jc.jrdto.Title.substr(0, 10) + "...&raquo;";
	tab.Alt = jc.jrdto.Title;
	tabs.Print();

	LoadAndBindObjectToTab(tab, user_id, jc, "JournalComments", JournalCommentsOnLoad, login);
};

function JournalCommentsOnLoad(req, tab) {
	if (tab) {
		ObjectOnLoad(req, tab, "JournalComments");

		var jc = tab.JournalComments;
		jc.AssignTabTo("buttonSearch");
		BindEnterTo(jc.Inputs["SEARCH"], jc.Inputs["buttonSearch"]);
	}
};

/* Actions */

function DeleteCommentConfirmed(obj, id) {
	var params = MakeParametersPair("go", "delete");
	params += MakeParametersPair("id", id);
	obj.Request(params);
};

/*function SwitchPage(a) {	// Not sure it's necessary...
	if (a.Tab) {
		var jc = a.Tab.JournalComments;
		jc.Pager.Current = 0;
		jc.Request();
	}
};*/

function ShowMessageComments(a) {
	var tab_id = "c" + a.jrdto.Id;
	CreateUserTab(a.obj.USER_ID, a.obj.LOGIN, LoadAndBindJournalCommentsToTab, "Комментарии в журнале", a.jrdto, tab_id);
};

/* Confirms */

function DeleteComment(a, id) {
	co.Show(function() {DeleteCommentConfirmed(a.obj, id)}, "Удалить комментарий?", "Комментарий будет удалён.<br>Продолжить?");
};

