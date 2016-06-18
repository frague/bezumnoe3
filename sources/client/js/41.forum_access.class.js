//6.11
/*
	Forum access functionality.
	Allows to manage users access to forums/journals/galleries.
*/

var NO_ACCESS			= 0;
var READ_ONLY_ACCESS	= 1;
var FRIENDLY_ACCESS		= 2;
var READ_ADD_ACCESS		= 3;
var FULL_ACCESS			= 4;

var accesses = ["доступ закрыт", "только чтение", "дружественный доступ", "чтение/запись", "полный доступ"];

function ForumAccess(user_id, tab) {
	this.UserId = user_id;
	this.Tab = tab;

	this.fields = ["WHITE_LIST", "BLACK_LIST", "FRIENDS_LIST"];
	this.Template = "forum_access";
	this.ClassName = "ForumAccess";
	this.ServicePath = servicesPath + "forum_access.service.php";

	this.Forum = new jjdto();
};

ForumAccess.prototype = new OptionsBase();

ForumAccess.prototype.TemplateLoaded = function(req) {
	this.Forum = this.Tab.Forum;
	this.FORUM_ID = this.Tab.FORUM_ID;

	this.TemplateBaseLoaded(req);

	this.FindRelatedControls();

	this.BlackListGrid = new UserList("BLACK_LIST", this, new fadto());
	this.WhiteListGrid = new UserList("WHITE_LIST", this, new fadto());
	this.FriendsListGrid = new UserList("FRIENDS_LIST", this, new fjdto());

	this.AssignSelfTo("RefreshForumAccess");

	var a = new delayedRequestor(this, this.inputs["ADD_USER"], GetJournalUsers);
//	a.GetParams = function() {};
};

ForumAccess.prototype.BaseBind = function() {
	this.BlackListGrid.ClearRecords();
	this.WhiteListGrid.ClearRecords();
	this.FriendsListGrid.ClearRecords();

	for (var i = 0, l = this.data.length; i < l; i++) {
		var dtoItem = this.data[i];
		switch (dtoItem.ACCESS) {
			case FULL_ACCESS:
			case READ_ADD_ACCESS:
			case FRIENDLY_ACCESS:
			case READ_ONLY_ACCESS:
				this.WhiteListGrid.AddItem(dtoItem);
				break;
			case NO_ACCESS:
				this.BlackListGrid.AddItem(dtoItem);
				break;
		}
	}

	for (var i = 0, l = this.friends.length; i < l; i++) {
		var dtoItem = this.friends[i];
		this.FriendsListGrid.AddItem(dtoItem);
	}

	this.BlackListGrid.Refresh();
	this.WhiteListGrid.Refresh();
	this.FriendsListGrid.Refresh();
};

ForumAccess.prototype.request = function(params, callback) {
	var s = new ParamsBuilder(params)
		.add('FORUM_ID', this.Forum.FORUM_ID);
	this.BaseRequest(s.build(), callback);
};

ForumAccess.prototype.requestCallback = function(req) {
	this.friends = [];
	this.requestBaseCallback(req);
	this.Bind(this.data);
};


/* Data Transfer Object */

function fadto(forum_id, target_user_id, login, access) {
	this.fields = ["FORUM_ID", "TARGET_USER_ID", "LOGIN", "ACCESS"];
	this.Init(arguments);
	this.Id = this.FORUM_ID + "_" + this.TARGET_USER_ID;
};

fadto.prototype = new EditableDTO();

fadto.prototype.ToShowView = function(index, obj) {
	var tr = MakeGridRow(index);
	tr.className = (index % 2 ? "Dark" : "");

	var a = accesses[this.ACCESS];
	var td1 = d.createElement("td");
		td1.innerHTML = this.LOGIN + (a ? "&nbsp;(" + a + ")" : "");
		if (this.ACCESS == FULL_ACCESS) {
			td1.className = "Bold";
		}
	tr.appendChild(td1);
	tr.appendChild(this.MakeButtonsCell(1));
	return tr;
};

fadto.prototype.ToEditView = function() {};

/* Journal user DTO */

function judto($id, $login, $nick, $journal_id, $title) {
	this.fields = ["USER_ID", "LOGIN", "NICKNAME", "JOURNAL_ID", "TITLE"];
	this.Init(arguments);
};

judto.prototype = new DTO();

judto.prototype.ToString = function(index, obj, prev_id, holder, className) {

    if (prev_id != this.USER_ID) {
		var li = d.createElement("li");
		li.className = className;
		li.appendChild(MakeButton("AddForumAccess('" + this.USER_ID + "',''," + FULL_ACCESS + ", this.obj)", "icons/add_gold.gif", obj, "", "Дать полный доступ"));
		li.appendChild(MakeButton("AddForumAccess('" + this.USER_ID + "',''," + READ_ADD_ACCESS + ", this.obj)", "icons/add_white.gif", obj, "", "Дать доступ на чтение/запись"));
		li.appendChild(MakeButton("AddForumAccess('" + this.USER_ID + "',''," + FRIENDLY_ACCESS + ", this.obj)", "icons/add_magenta.gif", obj, "", "Дать дружественный доступ"));
		li.appendChild(MakeButton("AddForumAccess('" + this.USER_ID + "',''," + NO_ACCESS + ", this.obj)", "icons/add_black.gif", obj, "", "Закрыть доступ"));
		li.appendChild(MakeDiv(this.LOGIN + (this.NICKNAME ? "&nbsp;(" + this.NICKNAME + ")" : ""), "span"));
		holder.appendChild(li);
	}
	if (this.JOURNAL_ID) {
		li = d.createElement("li");
		li.className = className + " Journal";
		li.appendChild(MakeButton("AddForumAccess('','" + this.JOURNAL_ID + "', " + FRIENDLY_ACCESS + ", this.obj)", "icons/add_green.gif", obj, "", "Добавить дружественный журнал"));
		li.appendChild(MakeDiv("Журнал &laquo;" + this.TITLE + "&raquo;&nbsp;(" + this.LOGIN + ")", "span"));
		holder.appendChild(li);
	}
};

/* Friendly Journal DTO */

function fjdto(forum_id, title, login, target_forum_id) {
	this.fields = ["FORUM_ID", "TITLE", "LOGIN", "TARGET_FORUM_ID"];
	this.Init(arguments);
	this.Id = this.TARGET_FORUM_ID;
};

fjdto.prototype = new EditableDTO();

fjdto.prototype.ToShowView = function(index, obj) {
	var tr = MakeGridRow(index);
	tr.className = (index % 2 ? "Dark" : "");

	var td1 = d.createElement("td");
		td1.innerHTML = "&laquo;" + this.TITLE + "&raquo;&nbsp;(" + this.LOGIN + ")";
	tr.appendChild(td1);
	tr.appendChild(this.MakeButtonsCell(1));
	return tr;
};

fjdto.prototype.ToEditView = function() {};

/* Userlist Grid */

function UserList(id, relatedObject, dtObject) {
	var dt = dtObject;
	this.fields = dt.fields;

	this.ServicePath = servicesPath + "forum_access.service.php";
	this.GridId = id;
	this.Columns = 2;

	this.USER_ID = relatedObject.USER_ID;
	this.Tab = relatedObject.Tab;

	this.FindTable();
	this.ClearRecords();

	this.obj = relatedObject;
};

UserList.prototype = new EditableGrid();

UserList.prototype.BaseBind = function(){};

UserList.prototype.requestCallback = function(req) {
	if (this.obj) {
		this.obj.requestBaseCallback(req);
		this.obj.Bind(this.data);
	}
};


/* Helper methods */

function AddForumAccess(user_id, target_forum_id, access, obj) {
	var req = new Requestor(servicesPath + "forum_access.service.php", obj);
	req.callback = refreshList;
	req.request(["go", "FORUM_ID", "TARGET_USER_ID", "TARGET_FORUM_ID", "ACCESS"], ["add", obj.Forum.FORUM_ID, user_id, target_forum_id, access]);
};

function refreshList(sender) {
	sender.obj.requestCallback(sender.req, sender.obj);
};

function GetJournalUsers(input) {
	input.delayedRequestor.obj.SetTabElementValue("FOUND_USERS", loadingIndicator);
	var juRequest = new Requestor(servicesPath + "journal_users.service.php", input.delayedRequestor.obj);
	juRequest.callback = DrawUsers;
	juRequest.request(["value"], [input.value]);
};

function DrawUsers(sender) {
	var el = sender.obj.inputs["FOUND_USERS"];
	if (el) {
		el.innerHTML = "";
		var ul = d.createElement("ul");
		var prev_id = 0;
		var className = "";
		for (var i = 0, l = sender.data.length; i < l; i++) {
			var item = sender.data[i];
			className = ((prev_id == item.USER_ID) ? className : (className ? "" : "Dark"));
			item.ToString(i, sender.obj, prev_id, ul, className);
			prev_id = item.USER_ID;
		}
		el.appendChild(ul);
		sender.obj.Tab.Alerts.Clear();
		if (sender.more) {
			sender.obj.Tab.Alerts.Add("Более	20	результатов	-	уточните критерий поиска.", 1);
		}
	}
};
