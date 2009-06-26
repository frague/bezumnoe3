//4þ0
/*
	Forum access functionality.
	Allows to manage users access to forums/journals/galleries.
*/

var NO_ACCESS			= 0;
var READ_ONLY_ACCESS	= 1;
var FRIENDLY_ACCESS		= 1;
var READ_ADD_ACCESS		= 2;
var FULL_ACCESS			= 3;

function ForumAccess(user_id, tab) {
	this.UserId = user_id;
	this.Tab = tab;

	this.fields = ["WHITE_LIST", "BLACK_LIST", "FRIENDS_LIST"];
	this.Template = "forum_access";
	this.ClassName = "ForumAccess";
	this.ServicePath = servicesPath + "forum_access.service.php";
};

ForumAccess.prototype = new OptionsBase();

ForumAccess.prototype.TemplateLoaded = function(req) {
	// Bind tab react
	this.Tab.Reactor = this;
	this.FORUM_ID = this.Tab.FORUM_ID;

	this.TemplateBaseLoaded(req);

	this.FindRelatedControls();

	this.BlackListGrid = new UserList("BLACK_LIST", this);
	this.WhiteListGrid = new UserList("WHITE_LIST", this);
	this.FriendsListGrid = new UserList("FRIENDS_LIST", this);

	this.AssignSelfTo("RefreshForumAccess");
};

ForumAccess.prototype.BaseBind = function() {
	if (me.IsSuperAdmin()) {
		this.SetTabElementValue("TITLE", this.TITLE);
		this.SetTabElementValue("DESCRIPTION", this.DESCRIPTION);
	}
	
	this.BlackListGrid.ClearRecords();
	this.WhiteListGrid.ClearRecords();
	this.FriendsListGrid.ClearRecords();

	for (var i = 0, l = this.data.length; i < l; i++) {
		var dtoItem = this.data[i];
		switch (dtoItem.ACCESS) {
			case FULL_ACCESS:
			case READ_ADD_ACCESS:
				this.WhiteListGrid.AddItem(dtoItem);
				break;
			case FRIENDLY_ACCESS:
			case READ_ONLY_ACCESS:
				this.FriendsListGrid.AddItem(dtoItem);
				break;
			case NO_ACCESS:
				this.BlackListGrid.AddItem(dtoItem);
				break;
		}
	}

	this.BlackListGrid.Refresh();
	this.WhiteListGrid.Refresh();
	this.FriendsListGrid.Refresh();
};

ForumAccess.prototype.Request = function(params, callback) {
	if (!params) {
		params = "";
	}
	params += MakeParametersPair("FORUM_ID", this.FORUM_ID);
	this.BaseRequest(params, callback);
};

ForumAccess.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data);
	}
};

ForumAccess.prototype.React = function() {
	this.FORUM_ID = this.Tab.FORUM_ID;
	this.Request();
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

	var td1 = d.createElement("td");
		td1.innerHTML = this.LOGIN;
	tr.appendChild(td1);
	tr.appendChild(this.MakeButtonsCell(1));
	return tr;
};

fadto.prototype.ToEditView = function() {};


/* Userlist Grid */

function UserList(id, relatedObject) {
	var dt = new fadto();
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

UserList.prototype.RequestCallback = function(req, obj) {
	if (obj.obj) {
		obj.obj.RequestBaseCallback(req, obj);
		obj.obj.Bind(obj.data);
	}
};


/* Helper methods */

function AddForumAccess(user_id, forum_id, access, obj) {
	var req = new Requestor(servicesPath + "forum_access.service.php", obj);
	req.Request(["go", "FORUM_ID", "TARGET_USER_ID", "ACCESS"], ["add", forum_id, user_id, access]);
};