//1.0
/*
	Bots creation
*/

function Bots() {
	this.fields = ["TYPE", "USER", "ROOM"];
	this.ServicePath = servicesPath + "bots.service.php";
	this.Template = "bots";
	this.ClassName = "Bots";
};

Bots.prototype = new OptionsBase();

Bots.prototype.BaseBind = function() {};

Bots.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data);
	}
};

Bots.prototype.Request = function(params, callback) {
	if (!params) {
		params = this.Gather();
	}
	this.ClearRecords(true);
	this.BaseRequest(params, callback);
	this.HasEmptyRow = false;
};

Bots.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);
	this.GroupSelfAssign(["AddRoom", "RefreshBots"]);

	// System log checkboxes
	BindEnterTo(this.Inputs["locked"], this.Inputs["RefreshBots"]);
	BindEnterTo(this.Inputs["by_invitation"], this.Inputs["RefreshBots"]);
	BindEnterTo(this.Inputs["deleted"], this.Inputs["RefreshBots"]);
};

