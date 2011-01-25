//1.0
/*
	Service class for authorization pop-up.
*/


/* Change Nickname class */

function AuthForm() {
	this.Template = "auth_form";
};

AuthForm.prototype.CreateControls = function(container) {
	this.Holder = d.createElement("div");
	this.Holder.className = "AuthForm";

	this.Holder.innerHTML = LoadingIndicator;

	container.appendChild(this.Holder);
};

AuthForm.prototype.TemplateLoaded = function(req) {
	text = req;
	if (req.responseText) {
		text = req.responseText;
		KeepRequestedContent(this.Template, text);
	}
	this.Holder.innerHTML = text;
};

AuthForm.prototype.RequestData = function() {
	RequestContent(this);
};

