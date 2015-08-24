//2.0
/*
    OpenIDs associated with this user
*/

var OpenIdProviders = [];

function OpenIds() {
    this.fields = ["USER_OPENID_ID", "OPENID_PROVIDER_ID", "LOGIN"];
    this.ServicePath = servicesPath + "openids.service.php";
    this.Template = "openids";
    this.ClassName = "OpenId";
    this.GridId = "OpenIdsGrid";
    this.Columns = 3;
};

OpenIds.prototype = new EditableGrid();

OpenIds.prototype.BaseBind = function() {};

OpenIds.prototype.requestCallback = function(req, obj) {
    if (obj) {
        obj.requestBaseCallback(req, obj);
        obj.Bind(obj.data);
    }
};

OpenIds.prototype.TemplateLoaded = function(req) {
    this.TemplateBaseLoaded(req);
    this.GroupSelfAssign(["AddOpenId", "RefreshOpenIds"]);
};

/* UserOpenId Data Transfer Object */

function oidto(id, provider_id, login) {
    this.fields = ["Id", "ProviderId", "Login"];
    this.Init(arguments);
    if (provider_id && OpenIdProviders[provider_id]) {
        this.Provider = OpenIdProviders[provider_id];
    }  else {
        this.Provider = new oipdto(0, "Unknown", "");
    }
};

oidto.prototype = new EditableDTO();

oidto.prototype.ToShowView = function(index, obj) {
    var tr = MakeGridRow(index);

    // Rights
    var td1 = d.createElement("td");
        td1.className = "Centered";
        td1.appendChild(this.Provider.ToPrint());
    tr.appendChild(td1);

    var td2 = d.createElement("td");
        td2.style.verticalAlign = "middle";
        td2.innerHTML = this.Login;
    tr.appendChild(td2);

    tr.appendChild(this.MakeButtonsCell());
    return tr;
};

oidto.prototype.ToEditView = function(index, obj) {
    var tr = MakeGridRow(index);

    // Rights
    var td1 = d.createElement("td");
        var input = d.createElement("input");
        input.type = "hidden";
        input.value = this.ProviderId;
        td1.appendChild(input);

        for (p in OpenIdProviders) {
            td1.appendChild(OpenIdProviders[p].ToSelect(this.ProviderId, input));
        }
        this.ProviderIdInput = input;
    tr.appendChild(td1);

    var td2 = d.createElement("td");
        this.LoginInput = d.createElement("input");
        this.LoginInput.value = this.Login;
        this.LoginInput.className = "Wide";
        td2.appendChild(this.LoginInput);
    tr.appendChild(td2);

    tr.appendChild(this.MakeButtonsCell());
    return tr;
};


/* OpenId Provider Data Transfer Object */

function oipdto(id, title, image) {
    this.fields = ["Id", "Title", "Image"];
    this.Init(arguments);
};

oipdto.prototype = new DTO();

oipdto.prototype.ToPrint = function() {
    var img = new Image();
    img.src = openIdPath + this.Image;
    img.title = this.Title;
    return img;
};

var selectedLink;

oipdto.prototype.ToSelect = function (id, input) {
    this.Input = input;

    var a = d.createElement("a");
    a.href = voidLink;
    if (id == this.Id) {
        a.className = "Selected";
        selectedLink = a;
    }
    a.obj = this;
    a.onclick = this.Select;
    a.appendChild(this.ToPrint());
    return a;
};

oipdto.prototype.Select = function() {
    this.obj.Input.value = this.obj.Id;
    if (selectedLink) {
        selectedLink.className = "";
    }
    selectedLink = this;
    this.className = "Selected";
    this.blur();
};

/* Helper methods */

function AddOpenId(a) {
    if (a.obj) {
        a.obj.AddRow(new oidto(0, 1, "login"));
    }
};
