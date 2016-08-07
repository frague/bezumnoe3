import {settings} from './settings';

/*
  List of admin comments to user (ban, rights changes etc.)
*/

class AdminComments extends PagedGrid {
  constructor() {
    super();
    this.fields = ["ADMIN_COMMENT", "DATE", "SEARCH", "SEVERITY_NORMAL", "SEVERITY_WARNING", "SEVERITY_ERROR"];
    this.ServicePath = settings.servicesPath + "admin.comments.service.php";
    this.ClassName = "AdminComments";
    this.Template = "admin_comments";
    this.GridId = "AdminCommentsGrid";
    this.Columns = 2;
  }

  BaseBind() {}

  InitPager() {
    this.Pager = new Pager(
      this.Inputs[this.PagerId], 
      () => this.Tab.AdminComments.SwitchPage(), 
      this.PerPage
    );
  }

  RequestCallback(req, obj) {
    if (obj) {
      obj.RequestBaseCallback(req, obj);
      obj.Bind(obj.data, obj.Total);
    }
  }

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);

    Wait(1000); // TODO: Wtf remove!

    this.AssignTabTo("AddComment");
    this.GroupSelfAssign(["RefreshAdminComments", "ResetFilter"]);

    new DatePicker(this.Inputs["DATE"]);

    BindEnterTo(this.Inputs["ADMIN_COMMENT"], this.Inputs["AddComment"]);
    BindEnterTo(this.Inputs["DATE"], this.Inputs["RefreshAdminComments"]);
    BindEnterTo(this.Inputs["SEARCH"], this.Inputs["RefreshAdminComments"]);

    // System log checkboxes
    BindEnterTo(this.Inputs["SEVERITY_NORMAL"], this.Inputs["RefreshAdminComments"]);
    BindEnterTo(this.Inputs["SEVERITY_WARNING"], this.Inputs["RefreshAdminComments"]);
    BindEnterTo(this.Inputs["SEVERITY_ERROR"], this.Inputs["RefreshAdminComments"]);

    if (this.Init) {
      this.Init();
    }
  }

  CustomReset() {
    this.SetTabElementValue("SEVERITY_NORMAL", 1);
    this.SetTabElementValue("SEVERITY_WARNING", 1);
    this.SetTabElementValue("SEVERITY_ERROR", 1);
  }
}

/* Admin comment Data Transfer Object */

var lastCommentDate;

class acdto extends DTO {
  constructor(date, content, login, severity, user) {
    super(arguments);
    this.fields = ["Date", "Content", "Login", "Severity", "User"];
    this.Init(arguments);
  }

  ToString(index, obj, holder) {
    if (!index) {
      lastCommentDate = "";
    }
    var date = ParseDate(this.Date);
    var dateString = date.ToPrintableString();
    if (date && dateString && dateString != lastCommentDate && holder) {
      lastCommentDate = dateString;
      holder.appendChild(MakeGridSubHeader(index, obj.Columns, dateString));
    }

    var tr = MakeGridRow(index);
    if (this.Severity) {
      tr.className += " " + SeverityCss[this.Severity - 1];
    }

    var td1 = document.createElement("td");
    td1.className = "Centered";
    td1.innerHTML = date.time() + "<br><b>" + this.Login + "</b>";
    tr.appendChild(td1);

    var td2 = document.createElement("td");
    td2.innerHTML = (this.User ? "Пользователь  <b>" + this.User + "</b>:<br>" : "") + this.Content;
    tr.appendChild(td2);
    
    return tr;
  }
}

/* Helper methods */

function AddComment(img) {
  if (img && img.Tab && img.Tab.AdminComments) {
    img.Tab.AdminComments.Save(AdminCommentSaved);
  }
};

function AdminCommentSaved(req, obj) {
  if (obj) {
    obj.SetTabElementValue("ADMIN_COMMENT", "");
    obj.RequestCallback(req, obj);
  }
};