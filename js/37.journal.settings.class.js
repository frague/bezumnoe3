//1.6
/*
	Journal settings of user menu.
*/

function JournalSettings() {
	this.fields = new Array("ALIAS", "REQUESTED_ALIAS");
	this.ServicePath = servicesPath + "journal.settings.service.php";
	this.Template = "journal_settings";
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



/* Helper methods */

function LoadAndBindJournalSettingsToTab(tab, user_id) {
	LoadAndBindObjectToTab(tab, user_id, new JournalSettings(), "JournalSettings", JournalSettingsOnLoad);
};

function JournalSettingsOnLoad(req, tab) {
	if (tab) {
		ObjectOnLoad(req, tab, "JournalSettings");

		var tjs = tab.JournalSettings;
		tjs.AssignTabTo("linkRefresh");

		/* Request friendly journals & forbidden commenters lists */
		tjs.FindRelatedControls();

		tab.FriendlyJournalsHolder = tjs.Inputs["friendlyBlogs"];
		LoadFriendlyJournals(tab);
		tab.ForbiddenCommentersHolder = tjs.Inputs["forbiddenCommenters"];
		LoadForbiddenCommenters(tab);

		/* Submit button */
		tab.AddSubmitButton("SaveJournalSettings(this)");
	}
};

function SaveJournalSettings(a) {
	if (a.obj) {
		a.obj.JournalSettings.Save();
	}
};

