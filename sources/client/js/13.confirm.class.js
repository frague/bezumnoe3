//3.1
/*
	Displays inline confirmation window
	blocking all content behind and handling callback.
*/

function Confirm() {
	var Cover;
	var Holder;
	var callbackObject;

	var AlertType = false;
};

Confirm.prototype.Init = function(coverId, holderId) {
	this.Cover = $(getElement(coverId))[0];
	this.Holder = $(getElement(holderId))[0];

	this.Holder.className = "ConfirmContainer";
};

Confirm.prototype.Display = function(state) {
	displayElement(this.Holder, state);
	displayElement(this.Cover, state);
};

Confirm.prototype.SetBodyOverflow = function(state) {
	return;
//	var b = d.documentElement ? d.documentElement : d.body;
//	b.style.overflow = state ? "auto" : "hidden";
};

Confirm.prototype.Show = function(callback, title, message, customContent, keep_opened) {
	if (!this.Holder) {
		return false;
	} else {
		this.Holder.innerHTML = "";
	}

	this.SetBodyOverflow(0);

	title = title ? title : 'Confirmation';

	this.callbackObject = callback;
	this.KeepOpened = keep_opened;

	var h1 = d.createElement("h1");
	h1.innerHTML = title;
	this.Holder.appendChild(h1);

	var m = d.createElement("div");
	m.innerHTML = message;
	this.Holder.appendChild(m);

	if (customContent && customContent.CreateControls) {
		customContent.CreateControls(this.Holder);
		if (customContent.requestData) {
			customContent.requestData();
		}
	}

	var index = CheckEmpty(this.ButtonUrlIndex);
	var m1 = d.createElement("div");
	m1.className = "ConfirmButtons";
	if (this.AlertType) {
		m1.appendChild(MakeButton("ConfirmObject.Ok()", "ok_button"+index+".gif"));
	} else {
		m1.appendChild(MakeButton("ConfirmObject.Ok()", "yes_button"+index+".gif"));
		m1.appendChild(MakeButton("ConfirmObject.Cancel()", "no_button"+index+".gif"));
	}

	this.Holder.appendChild(m1);

	this.Display(true);
	window.ConfirmObject = this;

	return false;
};

Confirm.prototype.Hide = function() {
	if (!this.Holder) {
		return;
	}
	this.Display(false);
	this.Holder.innerHTML = "";
	this.SetBodyOverflow(1);
};

Confirm.prototype.Cancel = function() {
	if (!this.Holder) {
		return;
	}
	this.Hide();
	this.callbackObject = null;
};

Confirm.prototype.Ok = function() {
	if (!this.Holder) {
		return;
	}
	if (!this.KeepOpened) {
		this.Hide();
	}
	if (this.callbackObject) {
		if (typeof(this.callbackObject) == 'function') {
			this.callbackObject();
		} else {
			if (this.callbackObject.click) {
				this.callbackObject.click();
			} else {
				location.href = this.callbackObject;
			}
		}
	}
};
