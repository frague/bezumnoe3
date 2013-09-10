//3.8
/*
	Sliding panel class
*/

var panels = new Array();

function Panel() {
	this.IsOpened = 1;
	this.Step = 20;
	this.MinPosition = 10;

	this.BaseLinkClass = "Panel";
};

Panel.prototype.Init = function(id, size) {
	this.Id = id;
	this.Holder = $(GetElement(id))[0];
	this.Position = size;
	this.CurrentSize = size;

	this.CreateLink();

	if (this.Holder) {
		this.Resize(this.Position);
		if (this.SwitchLink) {
			this.SwitchLink.href = voidLink;
			this.SwitchLink.Panel = this;
			this.SwitchLink.onclick = function(){Slide(this.Panel)};

			this.LinkSwitcher();
		}
		panels[id] = this;
	}
};

Panel.prototype.CreateLink = function () {
	this.SwitchLink = d.createElement("a");
	this.SwitchLink.onfocus = function(){this.blur()};
	if (this.Holder.hasChildNodes()) {
		this.Holder.insertBefore(this.SwitchLink, this.Holder.firstChild);
	} else {
		this.Holder.appendChild(this.SwitchLink);
	}
};

Panel.prototype.LinkSwitcher = function () {
	this.SwitchLink.className = this.BaseLinkClass + " " + (this.IsOpened ? "Opened" : "Closed");
	if (AdjustDivs) {
		AdjustDivs();
	}
};

Panel.prototype.ResizeBy = function (to) {
	var result = true;
	var size = this.CurrentSize + to;

	if (size < this.MinPosition) {
		size = this.MinPosition;
		this.IsOpened = false;
		result = false;
	}
	if (size > this.Position) {
		size = this.Position;
		this.IsOpened = true;
		result = false;
	}
	if (!result) {
		this.LinkSwitcher();
	}
	this.Resize(size);
	return result;
};

Panel.prototype.Resize = function (size) {	// Method to override
};

function Slide(panel) {
	if (panel.ResizeBy(panel.IsOpened ? -panel.Step : panel.Step)) {
		setTimeout(function() {Slide(panel)}, 1);
	}
};



/* Left Panel derived class */

function LeftPanel(id, size) {
	this.BaseLinkClass = "PanelLeft";

	this.Init(id, size);
};

LeftPanel.prototype = new Panel();

LeftPanel.prototype.Resize = function (size) {
//	this.Holder.style.width = size + "px";
	this.Holder.style.left = (size - this.Position) + "px";
	this.CurrentSize = size;
};

/* Right Panel derived class */

function RightPanel(id, size) {
	this.BaseLinkClass = "PanelRight";

	this.MinPosition = 10;
	this.Init(id, size);
};

RightPanel.prototype = new Panel();

RightPanel.prototype.Resize = function (size) {
	this.Holder.style.width = size + "px";
	this.Holder.style.right = "10px";
	this.CurrentSize = size;
};

