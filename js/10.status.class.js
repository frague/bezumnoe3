//1.0
/*
	Represents status entity on client-side.
*/

function Status(rights, title, color) {
	// Properties

	this.Rights = rights;
	this.Title = title;
	this.Color = color;
};

// Methods
Status.prototype.MakeCSS = function() {
	return "color:" + this.Color + ";";
};

Status.prototype.CheckSum = function() {
	var cs = CheckSum(this.Rights);
	cs+= CheckSum(this.Title);
	cs+= CheckSum(this.Color);

	return cs;
};
