//1.0
/*
	Law code text
*/

function LawCode() {
	this.Template = "law";
	this.ClassName = "LawCode";
};

LawCode.prototype = new OptionsBase();

LawCode.prototype.Request = function() {
};

LawCode.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);
};
