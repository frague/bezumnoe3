/*
  Data Transfer Objects
*/

class DTO {
  constructor() {
  };

  Init() {
    if (!arguments || !this.fields || args.length != this.fields.length) {
      return;
    }
    _.each(this.fields, (field, index) => this[field] = arguments[index]);
  }

  ToString(index, grid) {
    return this.ToShowView(index, grid);
  }

  ToShowView() {
    return;
  }
}


/* Editable DTO */

class EditableDTO extends DTO {
  constructor() {
    super(arguments)
    this.EditView = false;
  }

  ToString(index, grid) {
    if (this.EditView) {
      return this.ToEditView(index, grid);
    } else {
      return this.ToShowView(index, grid);
    }
  }

  ToEditView() { // To override
    return "";
  }

  Gather() {
    for (var i = 0, l = this.fields.length; i < l; i++) {
      var input = this[this.fields[i] + "Input"];
      if (input) {
        if (input.type == "checkbox" || input.type == "radio") {
          this[this.fields[i]] = input.checked ? 1 : "";
        } else {
          this[this.fields[i]] = input.value;
        }
      }
    }
  }

  CancelEditing() {
    if (this.Grid) {
      this.Grid.CancelEditing();
    }
  }

  Edit() {
    if (this.Grid) {
      this.Grid.Edit(this.Id);
    }
  }

  Save() {
    if (this.Grid) {
      this.Gather();
      if (this.Grid.GatherDTO(this)) {
        this.Grid.Save();
      }
    }
  }

  Delete() {
    if (this.Grid) {
      this.Gather();
      if (this.Grid.GatherDTO(this)) {
        this.Grid.Delete();
      }
    }
  }

  MakeButtonsCell(hideEdit) {
    var td = d.createElement("td");
    td.className = "Middle Centered";
    if (this.EditView) {
      td.appendChild(MakeButton("this.obj.Save()", "icons/done.gif", this, "", "Сохранить"));
      td.appendChild(MakeButton("this.obj.CancelEditing()", "icons/cancel.gif", this, "", "Отмена"));
    } else {
      if (!hideEdit) {
        td.appendChild(MakeButton("this.obj.Edit()", "icons/edit.gif", this, "", "Править"));
      }
      td.appendChild(MakeButton("this.obj.Delete()", "delete_icon.gif", this, "", "Удалить"));
    }
    return td;
  }
}