//5.3
/*
	List of admin comments to user (ban, rights changes etc.)
*/

function AdminComments() {
	this.fields = ["ADMIN_COMMENT", "DATE", "SEARCH", "SEVERITY_NORMAL", "SEVERITY_WARNING", "SEVERITY_ERROR"];
	this.ServicePath = servicesPath + "admin.comments.service.php";
	this.ClassName = "AdminComments";
	this.Template = "admin_comments";
	this.GridId = "AdminCommentsGrid";
	this.Columns = 2;
};

AdminComments.prototype = new PagedGrid();

AdminComments.prototype.BaseBind = function() {};

AdminComments.prototype.InitPager = function() {
	this.Pager = new Pager(this.inputs[this.PagerId], function(){this.Tab.AdminComments.SwitchPage()}, this.PerPage);
};

AdminComments.prototype.requestCallback = function(req, obj) {
	if (obj) {
		obj.requestBaseCallback(req, obj);
		obj.Bind(obj.data, obj.Total);
	}
};

// Template loading
AdminComments.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);

	Wait(1000);

	this.AssignTabTo("AddComment");
	this.GroupSelfAssign(["RefreshAdminComments", "ResetFilter"]);

	new DatePicker(this.inputs["DATE"]);

	BindEnterTo(this.inputs["ADMIN_COMMENT"], this.inputs["AddComment"]);
	BindEnterTo(this.inputs["DATE"], this.inputs["RefreshAdminComments"]);
	BindEnterTo(this.inputs["SEARCH"], this.inputs["RefreshAdminComments"]);

	// System log checkboxes
	BindEnterTo(this.inputs["SEVERITY_NORMAL"], this.inputs["RefreshAdminComments"]);
	BindEnterTo(this.inputs["SEVERITY_WARNING"], this.inputs["RefreshAdminComments"]);
	BindEnterTo(this.inputs["SEVERITY_ERROR"], this.inputs["RefreshAdminComments"]);

	if (this.Init) {
		this.Init();
	}
};

AdminComments.prototype.CustomReset = function() {
	this.SetTabElementValue("SEVERITY_NORMAL", 1);
	this.SetTabElementValue("SEVERITY_WARNING", 1);
	this.SetTabElementValue("SEVERITY_ERROR", 1);
};

/* Admin comment Data Transfer Object */

var lastCommentDate;

function acdto(date, content, login, severity, user) {
	this.fields = ["Date", "Content", "Login", "Severity", "User"];
	this.Init(arguments);
};

acdto.prototype = new DTO();

acdto.prototype.ToString = function(index, obj, holder) {
	if (!index) {
		lastCommentDate = "";
	}
	var date = ParseDate(this.Date);
	var dateString = date.ToPrintableString();
	if (date && dateString && dateString != lastCommentDate && holder) {
		lastCommentDate = dateString;
		holder.appendChild(MakeGridSubHeader(index, obj.Columns, dateString));
	}

	var tr = MakeGridRow(index);
	if (this.Severity) {
		tr.className += " " + severityCss[this.Severity - 1];
	}

	var td1 = d.createElement("td");
	td1.className = "Centered";
	td1.innerHTML = date.Time() + "<br><b>" + this.Login + "</b>";
	tr.appendChild(td1);

	var td2 = d.createElement("td");
	td2.innerHTML = (this.User ? "Пользователь	<b>" + this.User + "</b>:<br>" : "") + this.Content;
	tr.appendChild(td2);
	
	return tr;
};


/* Helper methods */

function AddComment(img) {
	if (img && img.Tab && img.Tab.AdminComments) {
		img.Tab.AdminComments.Save(AdminCommentSaved);
	}
};

function AdminCommentSaved(req, obj) {
	if (obj) {
		obj.SetTabElementValue("ADMIN_COMMENT", "");
		obj.requestCallback(req, obj);
	}
};
