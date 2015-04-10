//1.9
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
    DebugLine("Admin options request");
};

AdminOptions.prototype.TemplateLoaded = function(req) {
    this.TemplateBaseLoaded(req);

    DebugLine("Related controls");
    this.FindRelatedControls();
    var spoilers = this.Inputs["Spoilers"];
    if (spoilers) {
        DebugLine("Spoilers div. SpoilerInits: " + spoilerInits.length);
        for (var i = 0, l = spoilerInits.length; i < l; i++) {
            DebugLine(i);
            var s = new Spoiler(i + 1, spoilerNames[i], 0, 0, spoilerInits[i]);
            s.ToString(spoilers);
        }
    }
};
