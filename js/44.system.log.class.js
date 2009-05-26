//2.2
/*
	System log
*/

function SystemLog() {
	this.fields = ["DATE", "SEARCH"];
	this.Template = "system_log";
	this.ClassName = "SystemLog";
	this.GridId = "AdminCommentsGrid";
	this.Columns = 2;
	this.PerPage = 50;
};

SystemLog.prototype = new AdminComments();

SystemLog.prototype.Init = function() {
    this.FindRelatedControls();
};
