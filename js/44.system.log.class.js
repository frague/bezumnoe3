//2.1
/*
	System log
*/

function SystemLog() {
	this.fields = ["DATE", "SEARCH"];
	this.Template = "system_log";
	this.GridId = "AdminCommentsGrid";
	this.Columns = 2;
	this.PerPage = 50;
};

SystemLog.prototype = new AdminComments();

SystemLog.prototype.Init = function() {
    this.FindRelatedControls();
};

/* Helper methods */

function LoadAndBindSystemLogToTab(tab) {
	LoadAndBindObjectToTab(tab, "", new SystemLog(), "AdminComments", AdminCommentsOnLoad);
};

