//2.8
/*
	Rooms management
*/

function Rooms() {
	this.fields = ["ROOM_ID", "TITLE", "IS_DELETED", "IS_LOCKED", "IS_INVITATION_REQUIRED", "locked", "by_invitation", "deleted"];
	this.ServicePath = servicesPath + "rooms.service.php";
	this.Template = "rooms";
	this.ClassName = "Rooms";
	this.GridId = "RoomsGrid";
	this.Columns = 2;
};

Rooms.prototype = new EditableGrid();

Rooms.prototype.BaseBind = function() {};

Rooms.prototype.requestCallback = function(req, obj) {
	if (obj) {
		obj.requestBaseCallback(req, obj);
		obj.Bind(obj.data);
	}
};

Rooms.prototype.request = function(params, callback) {
	if (!params) {
		params = this.Gather();
	}
	this.ClearRecords(true);
	this.BaseRequest(params, callback);
	this.HasEmptyRow = false;
};

Rooms.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);
	this.GroupSelfAssign(["AddRoom", "RefreshRooms"]);

	// System log checkboxes
	BindEnterTo(this.inputs["locked"], this.inputs["RefreshRooms"]);
	BindEnterTo(this.inputs["by_invitation"], this.inputs["RefreshRooms"]);
	BindEnterTo(this.inputs["deleted"], this.inputs["RefreshRooms"]);
};

/* Room Data Transfer Object editable methods */

rdto.prototype.ToShowView = function(index, obj) {
	var tr = MakeGridRow(index);

	var td2 = d.createElement("td");
	if (this.IsDeleted) {
		td2.className = "Deleted";
	}
	if (this.IsInvitationRequired) {
		td2.className += " Dude";
	} else if (this.IsLocked) {
		td2.className += " Locked";
	}
	td2.innerHTML = this.Title;
	tr.appendChild(td2);
	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

rdto.prototype.ToEditView = function(index, obj) {
	var tr = MakeGridRow(index);

	var td2 = d.createElement("td");
		this.TitleInput = d.createElement("input");
		this.TitleInput.className = "Wide";
		this.TitleInput.value = this.Title;
		td2.appendChild(this.TitleInput);

		this.IsLockedInput = CreateCheckBox("IsLocked", this.IsLocked);
		td2.appendChild(this.IsLockedInput);
		td2.appendChild(CreateLabel(this.IsLockedInput, "заблокированная"));

		this.IsInvitationRequiredInput = CreateCheckBox("IsInvitationRequired", this.IsInvitationRequired);
		td2.appendChild(this.IsInvitationRequiredInput);
		td2.appendChild(CreateLabel(this.IsInvitationRequiredInput, "по приглашению"));

		this.IsDeletedInput = CreateCheckBox("IsDeleted", this.IsDeleted);
		td2.appendChild(this.IsDeletedInput);
		td2.appendChild(CreateLabel(this.IsDeletedInput, "удалённая"));

	tr.appendChild(td2);

	tr.appendChild(this.MakeButtonsCell());
	return tr;
};


/* Helper methods */

function AddNewRoom(a) {
	if (a.obj) {
		a.obj.AddRow(new rdto(0, "", 0, 0, 0));
	}
};
