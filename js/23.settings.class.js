//3.5
/*
	Represents settings entity on client-side.
*/

function Settings(status, ignore_colors, ignore_sizes, ignore_fonts, ignore_styles, receive_wakes, frameset, font) {
	// Properties
	this.Status = status;
	this.IgnoreColors = ignore_colors;
	this.IgnoreSizes = ignore_sizes;
	this.IgnoreFonts = ignore_fonts;
	this.IgnoreStyles = ignore_styles;
	this.ReceiveWakeups = receive_wakes;
	this.Frameset = frameset;

	this.Font = font;

	this.fields = new Array("LOGIN", "STATUS", "IGNORE_COLORS", "IGNORE_FONT_SIZE", "IGNORE_FONTS", "IGNORE_FONT_STYLE", "RECEIVE_WAKEUPS", "FRAMESET", "ENTER_MESSAGE", "QUIT_MESSAGE", "FONT_COLOR", "FONT_SIZE", "FONT_FACE", "FONT_BOLD", "FONT_ITALIC", "FONT_UNDERLINED");
	this.ServicePath = servicesPath + "settings.service.php";
	this.Template = "usersettings";
};

Settings.prototype = new OptionsBase();

// Methods
Settings.prototype.CheckSum = function() {
	var cs = CheckSum(this.Status);
	cs += CheckSum(this.IgnoreColors);
	cs += CheckSum(this.IgnoreSizes);
	cs += CheckSum(this.IgnoreFonts);
	cs += CheckSum(this.IgnoreStyles);
	cs += CheckSum(this.ReceiveWakeups);
	cs += CheckSum(this.Frameset);

	if (this.Font && this.Font.CheckSum) {
		cs += this.Font.CheckSum();
	}

	return cs;
};

Settings.prototype.Bind = function() {
	this.BaseBind();
	UpdateFontView();
};

Settings.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind();
	}
};


/* Load & bind user profile to Tab */

function LoadAndBindSettingsToTab(tab, user_id) {
	LoadAndBindObjectToTab(tab, user_id, new Settings(), "Settings", SettingsOnLoad);
};

function SettingsOnLoad(req, tab) {
	if (tab) {
		ObjectOnLoad(req, tab, "Settings");
		tab.Settings.AssignSelfTo("linkRefresh");

		// Create Font object
		font = new Font();
		font.Inputs = tab.Settings.Inputs;
		setTimeout("UpdateFontView()", 1000);	// Set delay for IE

		// Init ColorPicker
		var cp = new ColorPicker("FONT_COLOR");
		
		/* Submit button */
		tab.AddSubmitButton("SaveSettings(this)");
	}
};

/* Save settings */

function SaveSettings(a) {
	if (a.obj) {
		a.obj.Alerts.Clear();
		a.obj.Settings.Save();
	}
};


/* Links actions */

function RefreshSettings(a) {
	if (a.Tab) {
		a.Tab.Alerts.Clear();
		a.Tab.Settings.Request();
	}
};

var font;
function UpdateFontView() {
	if (font && font.Inputs) {
		var el = font.Inputs["fontExample"];
		if (el) {
			font.Gather();
			font.ApplyTo(el);
		}
	}
};