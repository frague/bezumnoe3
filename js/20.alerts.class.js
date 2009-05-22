//1.3
/*
	Displays messages on given container.
*/

function Alerts(container) {
	this.Holder = container;
	this.Holder.className = "AlertsHolder";
	this.Container = RoundCorners(container, "corners_med.gif", 3);
	this.Clear();
};

Alerts.prototype.Add = function(message, isError) {
	DisplayElement(this.Holder, true);
	this.Container.innerHTML += "<p class='" + (isError ? "Error" : "") + "'>" + message + "</p>";
	this.HasErrors = this.HasErrors || isError;
	this.IsEmpty = 0;
};

Alerts.prototype.Clear = function() {
	DisplayElement(this.Holder, false);
	this.Container.innerHTML = "";
	this.HasErrors = 0;
	this.IsEmpty = 1;
};

