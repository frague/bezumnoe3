//2.3
/*
    Bots creation
*/

function Bots() {
    this.fields = ["TYPE", "FIND_USER", "BOT_USER_ID", "ROOM"];
    this.ServicePath = servicesPath + "bots.service.php";
    this.Template = "bots";
    this.ClassName = "Bots";
};

Bots.prototype = new OptionsBase();

Bots.prototype.Bind = function(data) {
    if (data) {
        var s = "";
        var holder = this.Inputs["FoundUsers"];
        holder.innerHTML = "";

        for (var i = 0,l = data.length; i < l; i++) {
            holder.appendChild(data[i].ToLiString(i, data[i], this));
        }
    }
};

Bots.prototype.RequestCallback = function(req, obj) {
    if (obj) {
        obj.RequestBaseCallback(req, obj);
        obj.FillFrom(obj.data);
        obj.Bind(obj.data);
    }
};

Bots.prototype.TemplateLoaded = function(req) {
    this.TemplateBaseLoaded(req);
    this.FindRelatedControls();

    var a = new DelayedRequestor(this, this.Inputs["FIND_USER"]);

    // Filling Rooms ddl
    BindRooms(this.Inputs["ROOM"]);

    /* Submit button */
    this.Tab.AddSubmitButton("SaveObject(this)", "", this);
};

Bots.prototype.Select = function(obj) {
    this.SetTabElementValue("SELECTED_HOLDER", StripTags(obj.Login) + " (ID: " + obj.Id + ")");
    this.SetTabElementValue("BOT_USER_ID", obj.Id);
    this.SetTabElementValue("FIND_USER", "");
    this.SetTabElementValue("FoundUsers", "");
};

Bots.prototype.Preset = function(input, name) {

};
