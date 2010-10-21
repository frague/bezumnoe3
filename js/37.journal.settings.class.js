//2.3
/*
	Journal settings of user menu.
*/

function JournalSettings() {
	this.fields = ["ALIAS", "REQUESTED_ALIAS", "TITLE", "DESCRIPTION", "IS_PROTECTED"];
	this.ServicePath = servicesPath + "journal.settings.service.php";
	this.Template = "journal_settings";
	this.ClassName = "JournalSettings";

	this.Forum = new jjdto();
};

JournalSettings.prototype = new OptionsBase();

JournalSettings.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.FillFrom(obj.data);
		obj.Bind(obj.data);
	}
};

JournalSettings.prototype.TemplateLoaded = function(req) {
	this.Forum = this.Tab.Forum;
	if (this.Forum && this.Forum.FORUM_ID) {
		this.FORUM_ID = this.Forum.FORUM_ID;
	}

	this.TemplateBaseLoaded(req);

	this.AssignTabTo("linkRefresh");
	this.FindRelatedControls();
	this.Tab.AddSubmitButton("SaveObject(this)", "", this);
};

JournalSettings.prototype.Request = function(params, callback) {
	if (!params) {
		params = "";
	}
	params += MakeParametersPair("FORUM_ID", this.FORUM_ID);
	this.BaseRequest(params, callback);
};
