//2.0
/*
	Rooms management
*/

function Rooms() {
	this.fields = ["ROOM_ID", "TITLE", "IS_DELETED", "IS_LOCKED"];
	this.ServicePath = servicesPath + "rooms.service.php";
	this.Template = "rooms";
	this.ClassName = "Rooms";
	this.GridId = "RoomsGrid";
	this.Columns = 2;
};

Rooms.prototype = new EditableGrid();

Rooms.prototype.BaseBind = function() {};

Rooms.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data);
	}
};

Rooms.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);
	this.GroupSelfAssign(["AddRoom", "RefreshRooms"]);
};

/* Room Data Transfer Object editable methods */

rdto.prototype.ToShowView = function(index, obj) {
	var tr = MakeGridRow(index);

	var td2 = d.createElement("td");
	if (this.IsDeleted) {
		td2.className = "Striked";
	}
	if (this.IsLocked) {
		td2.className += " Red";
	}
	td2.innerHTML = this.Title;
	tr.appendChild(td2);
	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

rdto.prototype.ToEditView = function(index, obj) {
	var tr = MakeGridRow(index);

	var td2 = d.createElement("td");
		td2.className = "Wide";
		this.TitleInput = d.createElement("input");
		this.TitleInput.value = this.Title;
		td2.appendChild(this.TitleInput);
	tr.appendChild(td2);

	tr.appendChild(this.MakeButtonsCell());
	return tr;
};


/* Helper methods */

function AddRoom(a) {
	if (a.obj) {
		a.obj.AddRow(new rdto(0, "", 0, 0));
	}
};