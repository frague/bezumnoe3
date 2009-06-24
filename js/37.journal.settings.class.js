//1.7
/*
	Journal settings of user menu.
*/

function JournalSettings() {
	this.fields = new Array("ALIAS", "REQUESTED_ALIAS");
	this.ServicePath = servicesPath + "journal.settings.service.php";
	this.Template = "journal_settings";
	this.ClassName = "JournalSettings";
};

JournalSettings.prototype = new OptionsBase();

JournalSettings.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		if (obj.data) {
			obj.FillFrom(obj.data);
			obj.Bind();
		}
	}
};

JournalSettings.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);

	this.AssignTabTo("linkRefresh");

	/* Request friendly journals & forbidden commenters lists */
	this.FindRelatedControls();

	/*
	this.Tab.FriendlyJournalsHolder = this.Inputs["friendlyBlogs"];
	LoadFriendlyJournals(this.Tab);
	this.Tab.ForbiddenCommentersHolder = this.Inputs["forbiddenCommenters"];
	LoadForbiddenCommenters(this.Tab);*/

	/* Submit button */
	this.Tab.AddSubmitButton("SaveJournalSettings(this)", "", this);
};


/* Helper methods */


function SaveJournalSettings(a) {
	if (a.obj) {
		a.obj.Save();
	}
};

