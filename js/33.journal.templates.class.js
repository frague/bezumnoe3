//3.0
/*
	Journal templates: Global markup, single message & stylesheets.
*/

function JournalTemplates() {
	this.fields = new Array("BODY", "MESSAGE", "CSS", "SKIN_TEMPLATE_ID");
	this.ServicePath = servicesPath + "journal.templates.service.php";
	this.Template = "journal_templates";
	this.ClassName = "JournalTemplates";

	this.Forum = new fldto();
};

JournalTemplates.prototype = new OptionsBase();

JournalTemplates.prototype.Request = function(params, callback) {
	if (!params) {
		params = "";
	}
	params += MakeParametersPair("FORUM_ID", this.Forum.FORUM_ID);
	this.BaseRequest(params, callback);
};

JournalTemplates.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.skinTemplateId = "";
		obj.RequestBaseCallback(req, obj);

		if (obj.data) {
			obj.FillFrom(obj.data);
			obj.Bind();
		}
		button = SetRadioValue(obj.Inputs["SKIN_TEMPLATE_ID"], obj.skinTemplateId);
		if (button) {
			button.click();
		}
	}
	if (obj.Forum) {
		obj.SetTabElementValue("TITLE", obj.Forum.MakeTitle());
	}
};

JournalTemplates.prototype.TemplateLoaded = function(req) {
	// Bind tab react
	this.Tab.Reactor = this;
	this.Forum = this.Tab.Forum;
	this.FORUM_ID = this.Tab.FORUM_ID;

	this.TemplateBaseLoaded(req);

	this.AssignTabTo("SKIN_TEMPLATE_ID");
	this.Tab.AddSubmitButton("SaveObject(this)", "", this);
};

JournalTemplates.prototype.React = function() {
	this.Forum = this.Tab.Forum;
	this.FORUM_ID = this.Forum.FORUM_ID;
	this.Request();
};

/* Actions */

function Maximize(el) {
	el.className = "Maximized";
};


