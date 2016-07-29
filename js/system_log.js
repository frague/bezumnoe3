/*
  System log
*/

class SystemLog extends AdminComments {
  constructor() {
    super();
    this.fields = ["DATE", "SEARCH", "SEVERITY_NORMAL", "SEVERITY_WARNING", "SEVERITY_ERROR"];
    this.Template = "system_log";
    this.ClassName = "SystemLog";
    this.GridId = "AdminCommentsGrid";
    this.Columns = 2;
    this.PerPage = 50;
  }

  Init() {
    this.FindRelatedControls();
  }

  InitPager() {
    this.Pager = new Pager(
      this.Inputs[this.PagerId], 
      () => this.Tab.SystemLog.SwitchPage(),
      this.PerPage
    );
  }
}