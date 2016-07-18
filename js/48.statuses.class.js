//3.9
/*
	Special statuses management
*/

function Statuses() {
	this.fields = ["STATUS_ID", "RIGHTS", "COLOR", "TITLE"];
	this.ServicePath = servicesPath + "statuses.service.php";
	this.Template = "statuses";
	this.ClassName = "Statuses";
	this.GridId = "StatusesGrid";
	this.Columns = 4;
};

Statuses.prototype = new EditableGrid();

Statuses.prototype.BaseBind = function() {};

Statuses.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data);
	}
};

Statuses.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);
	this.GroupSelfAssign(["AddStatus", "RefreshStatuses"]);
};

/* Status Data Transfer Object */

function sdto(id, rights, color, title) {
	this.fields = ["Id", "Rights", "Color", "Title"];
	this.Init(arguments);
};

sdto.prototype = new EditableDTO();

sdto.prototype.ToShowView = function(index, obj) {
	var tr = MakeGridRow(index);

	// Rights
	var td1 = d.createElement("td");
		td1.className = "Centered";
		td1.innerHTML = this.Rights;
	tr.appendChild(td1);

	var td2 = d.createElement("td");
			td2.colSpan = 2;
			var div = MakeDiv("&nbsp;");
			div.className = "StatusColor";
			div.style.backgroundColor = this.Color;
			td2.appendChild(div);
			td2.appendChild(MakeDiv(this.Title));
	tr.appendChild(td2);
	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

sdto.prototype.ToEditView = function(index, obj) {
	var tr = MakeGridRow(index);

	// Rights
	var td1 = d.createElement("td");
		this.RightsInput = d.createElement("input");
		this.RightsInput.value = this.Rights;
		this.RightsInput.style.width = "30px";
		td1.appendChild(this.RightsInput);
	tr.appendChild(td1);

	var td2 = d.createElement("td");
		td2.style.width = "50px";
		td2.className = "Nowrap";
		this.ColorInput = d.createElement("input");
		this.ColorInput.value = this.Color;
		td2.appendChild(this.ColorInput);
		new ColorPicker(this.ColorInput);
	tr.appendChild(td2);

	var td22 = d.createElement("td");
		td22.style.width = "100%";
		this.TitleInput = d.createElement("input");
		this.TitleInput.value = this.Title;
		this.TitleInput.className = "Wide";
		td22.appendChild(this.TitleInput);
	tr.appendChild(td22);
	tr.appendChild(this.MakeButtonsCell());
	return tr;
};


/* Helper methods */

function AddStatus(a) {
	if (a.obj) {
		a.obj.AddRow(new sdto(0, 1, "White", "Новый статус"));
	}
};