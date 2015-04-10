//3.5
/*
	Journal comments grid. Edit & delete buttons.
*/

function JournalComments(forum) {
	this.ServicePath = servicesPath + "journal.comments.service.php";
	this.Template = "journal_comments";
	this.ClassName = "JournalComments";
	this.Columns = 3;

	this.Forum = forum;
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
		obj.Bind(obj.data, obj.Total);
	}
	if (obj.Forum) {
		obj.SetTabElementValue("FORUM", obj.Forum.MakeTitle());
	}
};

JournalComments.prototype.LoadTemplate = function(tab, user_id, login) {
	// Important!
	this.jrdto = tab.PARAMETER;
	this.TITLE = this.jrdto.Title;

	/* Update tab title */
	tab.Title = "Комментарии к&nbsp;&laquo;" + this.jrdto.Title.substr(0, 10) + "...&raquo;";
	tab.Alt = this.jrdto.Title;
	tabs.Print();

	this.LoadBaseTemplate(tab, user_id, login);
};

JournalComments.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);

	this.AssignSelfTo("buttonSearch");
	BindEnterTo(this.Inputs["SEARCH"], this.Inputs["buttonSearch"]);
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
		td3.appendChild(MakeButton("EditRecord(this,"+this.Id+")", "icons/edit.gif", obj, "", "Править"));
		td3.appendChild(MakeButton("DeleteComment(this,"+this.Id+")", "delete_icon.gif", obj, "", "Удалить"));
	tr.appendChild(td3);
	
	return tr;
};

/* Actions */

function DeleteCommentConfirmed(obj, id) {
	var params = MakeParametersPair("go", "delete");
	params += MakeParametersPair("id", id);
	obj.Request(params);
};

function ShowMessageComments(a) {
	var tab_id = "c" + a.jrdto.Id;
	CreateUserTab(a.obj.USER_ID, a.obj.LOGIN, new JournalComments(a.obj.Forum), "Комментарии в журнале", a.jrdto, tab_id);
};

/* Confirms */

function DeleteComment(a, id) {
	co.Show(function() {DeleteCommentConfirmed(a.obj, id)}, "Удалить комментарий?", "Комментарий будет удалён.<br>Продолжить?");
};

