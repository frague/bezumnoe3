import {utils} from './utils';
import {settings} from './settings';

/*
  Rooms management
*/

class Rooms extends EditableGrid {
  constructor() {
    super();
    this.fields = ["ROOM_ID", "TITLE", "IS_DELETED", "IS_LOCKED", "IS_INVITATION_REQUIRED", "locked", "by_invitation", "deleted"];
    this.ServicePath = settings.servicesPath + "rooms.service.php";
    this.Template = "rooms";
    this.ClassName = "Rooms";
    this.GridId = "RoomsGrid";
    this.Columns = 2;
  }

  BaseBind() {};

  RequestCallback(req, obj) {
    if (obj) {
      obj.RequestBaseCallback(req, obj);
      obj.Bind(obj.data);
    }
  }

  Request(params, callback) {
    if (!params) {
      params = this.Gather();
    }
    this.ClearRecords(true);
    this.BaseRequest(params, callback);
    this.HasEmptyRow = false;
  }

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);
    this.GroupSelfAssign(["AddRoom", "RefreshRooms"]);

    // System log checkboxes
    BindEnterTo(this.Inputs["locked"], this.Inputs["RefreshRooms"]);
    BindEnterTo(this.Inputs["by_invitation"], this.Inputs["RefreshRooms"]);
    BindEnterTo(this.Inputs["deleted"], this.Inputs["RefreshRooms"]);
  }
}

/* Room Data Transfer Object editable methods */

rdto.ToShowView = (index, obj) => {
  var tr = MakeGridRow(index);

  var td2 = document.createElement("td");
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
  tr.appendChild(this.utils.makeButtonsCell());
  return tr;
};

rdto.ToEditView = (index, obj) => {
  var tr = MakeGridRow(index);

  var td2 = document.createElement("td");
  this.TitleInput = document.createElement("input");
  this.TitleInput.className = "Wide";
  this.TitleInput.value = this.Title;
  td2.appendChild(this.TitleInput);

  this.IsLockedInput = createCheckBox("IsLocked", this.IsLocked);
  td2.appendChild(this.IsLockedInput);
  td2.appendChild(createLabel(this.IsLockedInput, "заблокированная"));

  this.IsInvitationRequiredInput = createCheckBox("IsInvitationRequired", this.IsInvitationRequired);
  td2.appendChild(this.IsInvitationRequiredInput);
  td2.appendChild(createLabel(this.IsInvitationRequiredInput, "по приглашению"));

  this.IsDeletedInput = createCheckBox("IsDeleted", this.IsDeleted);
  td2.appendChild(this.IsDeletedInput);
  td2.appendChild(createLabel(this.IsDeletedInput, "удалённая"));

  tr.appendChild(td2);

  tr.appendChild(this.utils.makeButtonsCell());
  return tr;
};


/* Helper methods */

function AddNewRoom(a) {
  if (a.obj) {
    a.obj.AddRow(new rdto(0, "", 0, 0, 0));
  }
};