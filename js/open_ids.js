import {utils} from './utils';
import {settings} from './settings';

/*
  OpenIDs associated with this user
*/

var OpenIdProviders = [];

class OpenIds extends EditableGrid {
  constructor() {
    super();

    this.fields = ["USER_OPENID_ID", "OPENID_PROVIDER_ID", "LOGIN"];
    this.ServicePath = settings.servicesPath + "openids.service.php";
    this.Template = "openids";
    this.ClassName = "OpenId";
    this.GridId = "OpenIdsGrid";
    this.Columns = 3;
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
    this.GroupSelfAssign(["AddOpenId", "RefreshOpenIds"]);
  }
}

/* UserOpenId Data Transfer Object */

class oidto extends EditableDTO {
  constructor(id, provider_id, login) {
    super(arguments);
    this.fields = ["Id", "ProviderId", "Login"];
    this.Init(arguments);
    if (provider_id && OpenIdProviders[provider_id]) {
      this.Provider = OpenIdProviders[provider_id];
    }  else {
      this.Provider = new oipdto(0, "Unknown", "");
    }
  }

  ToShowView(index, obj) {
    var tr = MakeGridRow(index);

    // Rights
    var td1 = document.createElement("td");
    td1.className = "Centered";
    td1.appendChild(this.Provider.ToPrint());
    tr.appendChild(td1);

    var td2 = document.createElement("td");
    td2.style.verticalAlign = "middle";
    td2.innerHTML = this.Login;
    tr.appendChild(td2);

    tr.appendChild(this.utils.makeButtonsCell());
    return tr;
  }

  ToEditView(index, obj) {
    var tr = MakeGridRow(index);

    // Rights
    var td1 = document.createElement("td");
    var input = document.createElement("input");
    input.type = "hidden";
    input.value = this.ProviderId;
    td1.appendChild(input);

    for (p in OpenIdProviders) {
      td1.appendChild(OpenIdProviders[p].ToSelect(this.ProviderId, input));
    }
    this.ProviderIdInput = input;
    tr.appendChild(td1);

    var td2 = document.createElement("td");
    this.LoginInput = document.createElement("input");
    this.LoginInput.value = this.Login;
    this.LoginInput.className = "Wide";
    td2.appendChild(this.LoginInput);
    tr.appendChild(td2);

    tr.appendChild(this.utils.makeButtonsCell());
    return tr;
  }
}

/* OpenId Provider Data Transfer Object */

var selectedLink;
class oipdto extends DTO {
  constructor(id, title, image) {
    super(arguments);
    this.fields = ["Id", "Title", "Image"];
    this.Init(arguments);
  }

  ToPrint() {
    var img = new Image();
    img.src = openIdPath + this.Image;
    img.title = this.Title;
    return img;
  }

  ToSelect(id, input) {
    this.Input = input;

    var a = document.createElement("a");
    if (id == this.Id) {
      a.className = "Selected";
      selectedLink = a;
    }
    a.obj = this;
    a.onclick = this.Select;
    a.appendChild(this.ToPrint());
    return a;
  }

  Select() {
    this.obj.Input.value = this.obj.Id;
    if (selectedLink) {
      selectedLink.className = "";
    }
    selectedLink = this;
    this.className = "Selected";
    this.blur();
  }
}

/* Helper methods */

function AddOpenId(a) {
  if (a.obj) {
    a.obj.AddRow(new oidto(0, 1, "login"));
  }
};