//2.1
/*
	Bots creation
*/

function Bots() {
	this.fields = ["TYPE", "USER", "BOT_USER_ID", "ROOM"];
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
			holder.appendChild(data[i].ToLiString(i, data[i], this.Inputs["USER"], this.Inputs["BOT_USER_ID"]));
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

	var a = new DelayedRequestor(this, this.Inputs["USER"]);

	// Filling Rooms ddl
	BindRooms(this.Inputs["ROOM"]);

	/* Submit button */
	this.Tab.AddSubmitButton("SaveObject(this)", "", this);
};

