//2.0
/*
	Journal settings of user menu.
*/

function JournalSettings() {
	this.fields = ["ALIAS", "REQUESTED_ALIAS", "TITLE", "DESCRIPTION"];
	this.ServicePath = servicesPath + "journal.settings.service.php";
	this.Template = "journal_settings";
	this.ClassName = "JournalSettings";
};

JournalSettings.prototype = new OptionsBase();

JournalSettings.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.FillFrom(obj.data);
		obj.Bind(obj.data);

//		obj.DisplayTabElement("gallery", obj.type != "gallery");
//		obj.DisplayTabElement("forum", obj.type == "journal");
	}
};

JournalSettings.prototype.TemplateLoaded = function(req) {
	// Bind tab react
	this.Tab.Reactor = this;
	this.FORUM_ID = this.Tab.FORUM_ID;

	this.TemplateBaseLoaded(req);

	this.AssignTabTo("linkRefresh");
	this.FindRelatedControls();
	this.Tab.AddSubmitButton("SaveJournalSettings(this)", "", this);
};

JournalSettings.prototype.Request = function(params, callback) {
	if (!params) {
		params = "";
	}
	params += MakeParametersPair("FORUM_ID", this.FORUM_ID);
	this.BaseRequest(params, callback);
};

JournalSettings.prototype.React = function() {
	this.FORUM_ID = this.Tab.FORUM_ID;
	this.Request();
};

/* Helper methods */


function SaveJournalSettings(a) {
	if (a.obj) {
		a.obj.Save();
	}
};

