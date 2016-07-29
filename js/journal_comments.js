/*
  Journal comments grid. Edit & delete buttons.
*/

class JournalComments extends Comments {
  constructor(forum) {
    super();
    this.ServicePath = servicesPath + "journal.comments.service.php";
    this.Template = "journal_comments";
    this.ClassName = "JournalComments";
    this.Columns = 3;

    this.Forum = forum;
  }

  Gather() {
    return MakeParametersPair("RECORD_ID", this.jrdto.Id) + this.BaseGather();
  }

  InitPager() {
    this.Pager = new Pager(
      this.Inputs[this.PagerId], 
      () => this.Tab.JournalComments.SwitchPage(), 
      this.PerPage
    )
  }

  RequestCallback(req, obj) {
    if (obj) {
      obj.RequestBaseCallback(req, obj);
      obj.Bind(obj.data, obj.Total);
    }
    if (obj.Forum) {
      obj.SetTabElementValue("FORUM", obj.Forum.MakeTitle());
    }
  }

  LoadTemplate(tab, user_id, login) {
    // Important!
    this.jrdto = tab.PARAMETER;
    this.TITLE = this.jrdto.Title;

    /* Update tab title */
    tab.Title = "Комментарии к&nbsp;&laquo;" + this.jrdto.Title.substr(0, 10) + "...&raquo;";
    tab.Alt = this.jrdto.Title;
    tabs.Print();

    this.LoadBaseTemplate(tab, user_id, login);
  }

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);

    this.AssignSelfTo("buttonSearch");
    BindEnterTo(this.Inputs["SEARCH"], this.Inputs["buttonSearch"]);
  }

  DeleteComment(a, id) {
    co.Show(
      () => {DeleteCommentConfirmed(a.obj, id)}, 
      "Удалить комментарий?", 
      "Комментарий будет удалён.<br>Продолжить?"
    );
  }
};

/* Journal Record Data Transfer Object */

class jcdto extends DTO {
  constructor(id, user_id, name, title, content, date, is_hidden, is_deleted) {
    super();
    this.fields = ["Id", "UserId", "Name", "Title", "Content", "Date", "IsHidden", "IsDeleted"];
    this.Init(arguments);
  };

  ToString(index, obj) {
    var tr = MakeGridRow(index);
    if (this.IsHidden) {
      tr.className += " Hidden";
    }
    if (this.IsDeleted) {
      tr.className += " Deleted";
    }

    var td1 = d.createElement("td");
    var h2 = d.createElement("h2");
    h2.innerHTML = "&laquo;" + this.Title + "&raquo;";
    td1.appendChild(h2);

    var span = d.createElement("span");
    span.innerHTML = this.Content;
    td1.appendChild(span);

    var div = d.createElement("div");
    div.innerHTML = (this.UserId ? "<b>" : "") + this.Name + (this.UserId ? "</b>" : "") + ", " + this.Date;
    td1.appendChild(div);
    tr.appendChild(td1);

    var td3 = d.createElement("td");
    td3.className = "Centered";
      td3.appendChild(MakeButton("EditRecord(this,"+this.Id+")", "icons/edit.gif", obj, "", "Править"));
      td3.appendChild(
        MakeButton("DeleteComment(this,"+this.Id+")", "delete_icon.gif", obj, "", "Удалить")
      );
    tr.appendChild(td3);
    
    return tr;
  }
}

/* Actions */

function DeleteCommentConfirmed(obj, id) {
  var params = MakeParametersPair("go", "delete");
  params += MakeParametersPair("id", id);
  obj.Request(params);
};

function ShowMessageComments(a) {
  var tab_id = "c" + a.jrdto.Id;
  CreateUserTab(a.obj.USER_ID, a.obj.LOGIN, new JournalComments(a.obj.Forum), "Комментарии в журнале", a.jrdto, tab_id);
};
