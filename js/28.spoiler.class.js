//1.9
/*
	Spoiler class.
	Displays/hides logical piece of content on demand.
*/

function Spoiler(id, title, height, is_opened, on_select) {
	this.Id = id;
	this.Title = title;
	this.Height = height;
	this.IsOpened = is_opened ? 1 : 0;
	this.OnSelect = on_select;
	this.Locked = 0;
};

Spoiler.prototype = new TabBase();

Spoiler.prototype.UpdateTitle = function() {
	this.TitleHolder.innerHTML = this.Title;
};

Spoiler.prototype.ToString = function(holder) {
	this.Holder = d.createElement("div");

	var a = d.createElement("a");
	a.Spoiler = this;
	a.href = voidLink;
	a.className = "Title";
	a.onclick = function() {SwitchSpoiler(this);this.blur();};
			
	this.TitleHolder = d.createElement("h4");
	this.UpdateTitle();
	a.appendChild(this.TitleHolder);
	this.Holder.appendChild(a);

	this.TopicDiv = d.createElement("div");
	this.Holder.appendChild(this.TopicDiv);

	this.RelatedDiv = d.createElement("div");
	this.RelatedDiv.className = "RelatedDiv";
	if (this.Height) {
		this.RelatedDiv.style.height = this.Height;
	}
	this.Holder.appendChild(this.RelatedDiv);

	holder.appendChild(this.Holder);
	this.Display(this.IsOpened);
};

Spoiler.prototype.Display = function(state) {
	if (this.Disabled) {
		return;
	}
	this.IsOpened = state;
	this.Holder.className = "Spoiler " + (this.IsOpened ? "Opened" : "Closed");
};

/* Helper methods */

function SwitchSpoiler(a) {
	if (a.Spoiler) {
		a.Spoiler.Display(!a.Spoiler.IsOpened);
		if (a.Spoiler.OnSelect) {
			a.Spoiler.RelatedDiv.innerHTML = LoadingIndicator;
			a.Spoiler.OnSelect(a.Spoiler);
			a.Spoiler.OnSelect = "";	/* TODO: Treat failure */
		}
	}
};
