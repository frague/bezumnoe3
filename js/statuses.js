import {utils} from './utils';
import {settings} from './settings';

/*
  Special statuses management
*/

class Statuses extends EditableGrid {
  constructor() {
    super();
    this.fields = ["STATUS_ID", "RIGHTS", "COLOR", "TITLE"];
    this.ServicePath = settings.servicesPath + "statuses.service.php";
    this.Template = "statuses";
    this.ClassName = "Statuses";
    this.GridId = "StatusesGrid";
    this.Columns = 4;
  }

  BaseBind() {}

  RequestCallback(req, obj) {
    if (obj) {
      obj.RequestBaseCallback(req, obj);
      obj.Bind(obj.data);
    }
  }

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);
    this.GroupSelfAssign(["AddStatus", "RefreshStatuses"]);
  }
}

/* Status Data Transfer Object */

class sdto extends EditableDTO {
  constructor(id, rights, color, title) {
    super(arguments);
    this.fields = ["Id", "Rights", "Color", "Title"];
    this.Init(arguments);
  }

  ToShowView(index, obj) {
    var tr = MakeGridRow(index);

    // Rights
    var td1 = document.createElement("td");
      td1.className = "Centered";
      td1.innerHTML = this.Rights;
    tr.appendChild(td1);

    var td2 = document.createElement("td");
        td2.colSpan = 2;
        var div = utils.makeDiv("&nbsp;");
        div.className = "StatusColor";
        div.style.backgroundColor = this.Color;
        td2.appendChild(div);
        td2.appendChild(utils.makeDiv(this.Title));
    tr.appendChild(td2);
    tr.appendChild(this.utils.makeButtonsCell());
    return tr;
  }

  ToEditView(index, obj) {
    var tr = MakeGridRow(index);

    // Rights
    var td1 = document.createElement("td");
      this.RightsInput = document.createElement("input");
      this.RightsInput.value = this.Rights;
      this.RightsInput.style.width = "30px";
      td1.appendChild(this.RightsInput);
    tr.appendChild(td1);

    var td2 = document.createElement("td");
      td2.style.width = "50px";
      td2.className = "Nowrap";
      this.ColorInput = document.createElement("input");
      this.ColorInput.value = this.Color;
      td2.appendChild(this.ColorInput);
      new ColorPicker(this.ColorInput);
    tr.appendChild(td2);

    var td22 = document.createElement("td");
      td22.style.width = "100%";
      this.TitleInput = document.createElement("input");
      this.TitleInput.value = this.Title;
      this.TitleInput.className = "Wide";
      td22.appendChild(this.TitleInput);
    tr.appendChild(td22);
    tr.appendChild(this.utils.makeButtonsCell());
    return tr;
  }
}

/* Helper methods */

function AddStatus(a) {
  if (a.obj) {
    a.obj.AddRow(new sdto(0, 1, "White", "Новый статус"));
  }
}