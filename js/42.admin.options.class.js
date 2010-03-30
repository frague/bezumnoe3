//1.7
/*
	Admin options
*/

var MessagesSpoiler, TemplatesSpoiler, SettingsSpoiler;

function AdminOptions() {
	this.Template = "admin_options";
	this.ClassName = "AdminOptions";
};

AdminOptions.prototype = new OptionsBase();

AdminOptions.prototype.Request = function() {
	DebugLine("Request");
};

AdminOptions.prototype.TemplateLoaded = function(req) {
	DebugLine("1");
	this.TemplateBaseLoaded(req);

	DebugLine("2");

	this.FindRelatedControls();
	DebugLine("3");
	var spoilers = this.Inputs["Spoilers"];
	if (spoilers) {
		DebugLine("4");
		for (var i = 0, l = spoilerInits.length; i < l; i++) {
			DebugLine(i);
			var s = new Spoiler(i + 1, spoilerNames[i], 0, 0, spoilerInits[i]);
			s.ToString(spoilers);
		}
	}
};
