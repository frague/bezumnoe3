//1.0
/*
	Base class for those who loads template only (static text)
	with no user actions involved
*/

function StaticText() {
};

StaticText.prototype = new OptionsBase();

StaticText.prototype.Request = function() {
};

StaticText.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);
};