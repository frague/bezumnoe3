//1.5
/*
	Admin options
*/

var MessagesSpoiler, TemplatesSpoiler, SettingsSpoiler;

function AdminOptions() {
	this.Template = "admin_options";
};

AdminOptions.prototype = new OptionsBase();

AdminOptions.prototype.Request = function() {
};


function LoadAndBindAdminOptionsToTab(tab, user_id) {
	LoadAndBindObjectToTab(tab, user_id, new AdminOptions(), "AdminOptions", AdminOptionsOnLoad);
};

function AdminOptionsOnLoad(req, tab) {
	if (tab) {
		ObjectOnLoad(req, tab, "AdminOptions");

		var tao = tab.AdminOptions;
		if (tao) {
			tao.FindRelatedControls();
			var spoilers = tao.Inputs["Spoilers"];
			if (spoilers) {
				for (var i = 0, l = spoilerInits.length; i < l; i++) {
					var s = new Spoiler(i + 1, spoilerNames[i], 0, 0, spoilerInits[i]);
					s.ToString(spoilers);
				}
			}
		}
	}
};
