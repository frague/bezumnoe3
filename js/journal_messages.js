/*
  Journal messages grid. Edit & delete buttons.
*/

// Global reference to update messages list
var journalMessagesObj;

class JournalMessages extends PagedGrid {
  constructor() {
    super();
    this.fields = ["DATE", "SEARCH", "LOGIN", "FORUM_ID"];
    this.ServicePath = servicesPath + "journal.messages.service.php";
    this.Template = "journal_messages";
    this.ClassName = "JournalMessages";

    this.GridId = "MessagesGrid";

    journalMessagesObj = this;
    this.ForumsLoaded = 0;

    this.Forum = new jjdto();
  }

  InitPager() {
    this.Pager = new Pager(
      this.Inputs[this.PagerId], 
      () => this.Tab.JournalMessages.SwitchPage(),
      this.PerPage
    )
  }

  RequestCallback(req, obj) {
    if (obj) {
      obj.RequestBaseCallback(req, obj);
      obj.Bind(obj.data, obj.Total);
    }
  }

  TemplateLoaded(req) {
    this.Forum = this.Tab.Forum;
    if (this.Forum) {
      this.FORUM_ID = this.Forum.FORUM_ID;
    }

    this.TemplateBaseLoaded(req);

    this.GroupSelfAssign(["buttonSearch", "ResetFilter"]);
    BindEnterTo(this.Inputs["SEARCH"], this.Inputs["buttonSearch"]);
    new DatePicker(this.Inputs["DATE"]);
  }
}

/* Journal Record Data Transfer Object */

class jrdto extends DTO {
  constructor(id, title, content, date, comments, type) {
    super(arguments);
    this.fields = ["Id", "Title", "Content", "Date", "Comments", "Type"];
    this.Init(arguments);
  };

  ToString(index, obj) {
    var tr = MakeGridRow(index);

    var td1 = document.createElement("td");
    var h2 = document.createElement("h2");
    if (this.Type) {
      h2.className = (this.Type == "1" ? "Friends" : "Private");
    }
    h2.innerHTML = this.Title;
    td1.appendChild(h2);
    td1.innerHTML += this.Content;
    tr.appendChild(td1);

    var td2 = document.createElement("td");
    td2.className = "Centered";
      var comments = Math.round(this.Comments);
      if (comments) {
        var a = document.createElement("a");
        a.innerHTML = comments;
        a.jrdto = this;
        a.obj = obj;
        a.href = voidLink;
        a.onclick = function() {ShowMessageComments(this)};
        td2.appendChild(a);
      } else {
        td2.innerHTML = comments;
      }
    tr.appendChild(td2);

    var td3 = document.createElement("td");
    td3.className = "Centered";
      td3.appendChild(
        MakeButton("EditRecord(this,"+this.Id+")", "icons/edit.gif", obj, "", "Править")
      );
      td3.appendChild(
        MakeButton("DeleteRecord(this,"+this.Id+")", "delete_icon.gif", obj, "", "Удалить")
      );
    tr.appendChild(td3);
    
    return tr;
  }
};

/* Actions */

function EditRecord(a, post_id) {
  EditJournalPost(a.obj, post_id)
};


// TODO: Rewrite using Requestor
function DeleteRecordConfirmed(obj, id) {
  obj.Tab.Alerts.Clear();
  var params = MakeParametersPair("go", "delete");
  params += MakeParametersPair("USER_ID", obj.USER_ID);
  params += MakeParametersPair("RECORD_ID", id);
  sendRequest(servicesPath + "journal.post.service.php", DeleteCallback, params, obj);
};

function DeleteCallback(req, obj) {
  if (obj) {
    obj.RequestBaseCallback(req, obj);
    if (!obj.Tab.Alerts.HasErrors) {
      obj.Request();
    }
  }
};


/* Confirms */

function DeleteRecord(a, id) {
  co.Show(function() {DeleteRecordConfirmed(a.obj, id)}, "Удалить запись?", "Запись в блоге и все комментарии к ней будут удалены.<br>Продолжить?");
};
