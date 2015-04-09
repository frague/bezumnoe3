//2.4
/*
    System log
*/

function SystemLog() {
    this.fields = ["DATE", "SEARCH", "SEVERITY_NORMAL", "SEVERITY_WARNING", "SEVERITY_ERROR"];
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

SystemLog.prototype.InitPager = function() {
    this.Pager = new Pager(this.Inputs[this.PagerId], function(){this.Tab.SystemLog.SwitchPage()}, this.PerPage);
};

