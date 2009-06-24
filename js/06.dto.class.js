//3.4
/*
	DTOs with edit functionality
*/

/* Vase DTO */

function DTO() {
	this.fields = [];
};

DTO.prototype.Init = function(args) {
	for (var i = 0, l = this.fields.length; i < l; i++) {
		this[this.fields[i]] = args[i];
	}
};

DTO.prototype.ToString = function(index, grid) {
	return this.ToShowView(index, grid);
};

DTO.prototype.ToShowView = function() {	// To override
	return "";
};




/* Editable DTO */

function EditableDTO() {
	this.EditView = false;
};

EditableDTO.prototype = new DTO();

EditableDTO.prototype.ToString = function(index, grid) {
	if (this.EditView) {
		return this.ToEditView(index, grid);
	} else {
		return this.ToShowView(index, grid);
	}
};

EditableDTO.prototype.ToEditView = function() {	// To override
	return "";
};

EditableDTO.prototype.Gather = function() {
	for (var i = 0, l = this.fields.length; i < l; i++) {
		var input = this[this.fields[i] + "Input"];
		if (input) {
			this[this.fields[i]] = input.value;
		}
	}
};

/* Buttons events */

EditableDTO.prototype.CancelEditing = function() {
	if (this.Grid) {
		this.Grid.CancelEditing();
	}
};

EditableDTO.prototype.Edit = function() {
	if (this.Grid) {
		this.Grid.Edit(this.Id);
	}
};

EditableDTO.prototype.Save = function() {
	if (this.Grid) {
		this.Gather();
		if (this.Grid.GatherDTO(this)) {
			this.Grid.Save();
		}
	}
};

EditableDTO.prototype.Delete = function() {
	if (this.Grid) {
		this.Gather();
		if (this.Grid.GatherDTO(this)) {
			this.Grid.Delete();
		}
	}
};

/* ---------------------------- */

EditableDTO.prototype.MakeButtonsCell = function(hideEdit) {
	var td = d.createElement("td");
	td.className = "Middle Centered";
	if (this.EditView) {
		td.appendChild(MakeButton("this.obj.Save()", "icons/done.gif", this));
		td.appendChild(MakeButton("this.obj.CancelEditing()", "icons/cancel.gif", this));
	} else {
		if (!hideEdit) {
			td.appendChild(MakeButton("this.obj.Edit()", "icons/edit.gif", this));
		}
		td.appendChild(MakeButton("this.obj.Delete()", "delete_icon.gif", this));
	}
	return td;
};