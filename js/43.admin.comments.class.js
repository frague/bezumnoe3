//4.8
/*
	List of admin comments to user (ban, rights changes etc.)
*/

function AdminComments() {
	this.fields = ["ADMIN_COMMENT", "DATE", "SEARCH"];
	this.ServicePath = servicesPath + "admin.comments.service.php";
	this.Template = "admin_comments";
	this.GridId = "AdminCommentsGrid";
	this.Columns = 2;
};

AdminComments.prototype = new PagedGrid();

AdminComments.prototype.BaseBind = function() {};

AdminComments.prototype.InitPager = function() {
	this.Pager = new Pager(this.Inputs[this.PagerId], function(){this.Tab.AdminComments.SwitchPage()}, this.PerPage);
};

AdminComments.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data, obj.Total);
	}
};


/* Admin comment Data Transfer Object */

function acdto(date, content, login, severity, user) {
	this.fields = ["Date", "Content", "Login", "Severity", "User"];
	this.Init(arguments);
};

acdto.prototype = new DTO();

acdto.prototype.ToString = function(index, obj) {
	var tr = MakeGridRow(index);
	if (this.Severity) {
		tr.className += " " + SeverityCss[this.Severity - 1];
	}

	var td1 = d.createElement("td");
	td1.className = "Centered";
	td1.innerHTML = this.Date + "<br><b>" + this.Login + "</b>";
	tr.appendChild(td1);

	var td2 = d.createElement("td");
	td2.innerHTML = (this.User ? "Пользователь	<b>" + this.User + "</b>:<br>" : "") + this.Content;
	tr.appendChild(td2);
	
	return tr;
};


/* Helper methods */

function LoadAndBindAdminCommentsToTab(tab, user_id) {
	LoadAndBindObjectToTab(tab, user_id, new AdminComments(), "AdminComments", AdminCommentsOnLoad);
};

function AdminCommentsOnLoad(req, tab) {
	if (tab) {
		ObjectOnLoad(req, tab, "AdminComments");

		var ac = tab.AdminComments;
		ac.AssignTabTo("AddComment");
		ac.GroupSelfAssign(["RefreshAdminComments", "ResetFilter"]);

		new DatePicker(ac.Inputs["DATE"]);

		BindEnterTo(ac.Inputs["ADMIN_COMMENT"], ac.Inputs["AddComment"]);
		BindEnterTo(ac.Inputs["DATE"], ac.Inputs["RefreshAdminComments"]);
		BindEnterTo(ac.Inputs["SEARCH"], ac.Inputs["RefreshAdminComments"]);

		if (ac.Init) {
			ac.Init();
		}
	}
};

function AddComment(img) {
	if (img && img.Tab && img.Tab.AdminComments) {
		img.Tab.AdminComments.Save(AdminCommentSaved);
	}
};

function AdminCommentSaved(req, obj) {
	if (obj) {
		obj.SetTabElementValue("ADMIN_COMMENT", "");
		obj.RequestCallback(req, obj);
	}
};

function ResetFilter(a) {
// Caution! Used in Journal Messages also
	if (a.obj) {
		var ac = a.obj;
		ac.SetTabElementValue("DATE", "");
		ac.SetTabElementValue("SEARCH", "");
		ac.Request();
	}
};