//1.5
/*
	Recepient class.

*/
function Recepient(id, title, is_locked) {
	this.Id = id;
	this.Title = title;
	this.IsLocked = is_locked;
};

Recepient.prototype.ToString = function(index) {
	var s = (index ? ", " : "") + this.Title;
	if (!this.IsLocked) {
		s += "<a " + voidHref + " onclick='DR(" + this.Id + ")'>x</a>";
   	}
   	return s;
};

Recepient.prototype.Gather = function(index) {
	return (index ? "," : "") + this.Id;
};