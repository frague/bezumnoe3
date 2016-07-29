/*
  Forum access functionality.
  Allows to manage users access to forums/journals/galleries.
*/

class ForumAccess extends OptionsBase {
  constructor(user_id, tab) {
    super();
    this.UserId = user_id;
    this.Tab = tab;

    this.fields = ["WHITE_LIST", "BLACK_LIST", "FRIENDS_LIST"];
    this.Template = "forum_access";
    this.ClassName = "ForumAccess";
    this.ServicePath = servicesPath + "forum_access.service.php";

    this.Forum = new jjdto();
  }

  TemplateLoaded(req) {
    this.Forum = this.Tab.Forum;
    this.FORUM_ID = this.Tab.FORUM_ID;

    this.TemplateBaseLoaded(req);

    this.FindRelatedControls();

    this.BlackListGrid = new UserList("BLACK_LIST", this, new fadto());
    this.WhiteListGrid = new UserList("WHITE_LIST", this, new fadto());
    this.FriendsListGrid = new UserList("FRIENDS_LIST", this, new fjdto());

    this.AssignSelfTo("RefreshForumAccess");

    var a = new DelayedRequestor(this, this.Inputs["ADD_USER"], GetJournalUsers);
  }

  BaseBind() {
    this.BlackListGrid.ClearRecords();
    this.WhiteListGrid.ClearRecords();
    this.FriendsListGrid.ClearRecords();

    for (var i = 0, l = this.data.length; i < l; i++) {
      var dtoItem = this.data[i];
      switch (dtoItem.ACCESS) {
        case forumAccess.FULL_ACCESS:
        case forumAccess.READ_ADD_ACCESS:
        case forumAccess.FRIENDLY_ACCESS:
        case forumAccess.READ_ONLY_ACCESS:
          this.WhiteListGrid.AddItem(dtoItem);
          break;
        case forumAccess.NO_ACCESS:
          this.BlackListGrid.AddItem(dtoItem);
          break;
      }
    }

    for (var i = 0, l = this.friends.length; i < l; i++) {
      var dtoItem = this.friends[i];
      this.FriendsListGrid.AddItem(dtoItem);
    }

    this.BlackListGrid.Refresh();
    this.WhiteListGrid.Refresh();
    this.FriendsListGrid.Refresh();
  }

  Request(params, callback) {
    if (!params) {
      params = "";
    }
    params += MakeParametersPair("FORUM_ID", this.Forum.FORUM_ID);
    this.BaseRequest(params, callback);
  }

  RequestCallback(req, obj) {
    if (obj) {
      obj.friends = [];
      obj.RequestBaseCallback(req, obj);
      obj.Bind(obj.data);
    }
  }
}

/* Data Transfer Object */

class fadto extends EditableDTO {
  constructor(forum_id, target_user_id, login, access) {
    super(arguments);
    this.fields = ["FORUM_ID", "TARGET_USER_ID", "LOGIN", "ACCESS"];
    this.Init(arguments);
    this.Id = this.FORUM_ID + "_" + this.TARGET_USER_ID;
  }

  ToShowView(index, obj) {
    var tr = MakeGridRow(index);
    tr.className = (index % 2 ? "Dark" : "");

    var a = forumAccessName[this.ACCESS];
    var td1 = d.createElement("td");
      td1.innerHTML = this.LOGIN + (a ? "&nbsp;(" + a + ")" : "");
      if (this.ACCESS == forumAccess.FULL_ACCESS) {
        td1.className = "Bold";
      }
    tr.appendChild(td1);
    tr.appendChild(this.MakeButtonsCell(1));
    return tr;
  }

  ToEditView() {}
}

/* Journal user DTO */

class judto extends DTO {
  constructor($id, $login, $nick, $journal_id, $title) {
    super(arguments);
    this.fields = ["USER_ID", "LOGIN", "NICKNAME", "JOURNAL_ID", "TITLE"];
    this.Init(arguments);
  };

  ToString(index, obj, prev_id, holder, className) {
    if (prev_id != this.USER_ID) {
      var li = d.createElement("li");
      li.className = className;
      li.appendChild(MakeButton("AddForumAccess('" + this.USER_ID + "',''," + forumAccess.FULL_ACCESS + ", this.obj)", "icons/add_gold.gif", obj, "", "Дать полный доступ"));
      li.appendChild(MakeButton("AddForumAccess('" + this.USER_ID + "',''," + forumAccess.READ_ADD_ACCESS + ", this.obj)", "icons/add_white.gif", obj, "", "Дать доступ на чтение/запись"));
      li.appendChild(MakeButton("AddForumAccess('" + this.USER_ID + "',''," + forumAccess.FRIENDLY_ACCESS + ", this.obj)", "icons/add_magenta.gif", obj, "", "Дать дружественный доступ"));
      li.appendChild(MakeButton("AddForumAccess('" + this.USER_ID + "',''," + forumAccess.NO_ACCESS + ", this.obj)", "icons/add_black.gif", obj, "", "Закрыть доступ"));
      li.appendChild(MakeDiv(this.LOGIN + (this.NICKNAME ? "&nbsp;(" + this.NICKNAME + ")" : ""), "span"));
      holder.appendChild(li);
    }
    if (this.JOURNAL_ID) {
      li = d.createElement("li");
      li.className = className + " Journal";
      li.appendChild(MakeButton("AddForumAccess('','" + this.JOURNAL_ID + "', " + forumAccess.FRIENDLY_ACCESS + ", this.obj)", "icons/add_green.gif", obj, "", "Добавить дружественный журнал"));
      li.appendChild(MakeDiv("Журнал &laquo;" + this.TITLE + "&raquo;&nbsp;(" + this.LOGIN + ")", "span"));
      holder.appendChild(li);
    }
  };
}

/* Friendly Journal DTO */

class fjdto extends DTO {
  constructor(forum_id, title, login, target_forum_id) {
    super(arguments);
    this.fields = ["FORUM_ID", "TITLE", "LOGIN", "TARGET_FORUM_ID"];
    this.Init(arguments);
    this.Id = this.TARGET_FORUM_ID;
  }

  ToShowView(index, obj) {
    var tr = MakeGridRow(index);
    tr.className = (index % 2 ? "Dark" : "");

    var td1 = d.createElement("td");
      td1.innerHTML = "&laquo;" + this.TITLE + "&raquo;&nbsp;(" + this.LOGIN + ")";
    tr.appendChild(td1);
    tr.appendChild(this.MakeButtonsCell(1));
    return tr;
  }

  ToEditView() {}
}

/* Userlist Grid */

class UserList extends EditableGrid {
  constructor(id, relatedObject, dtObject) {
    super();

    var dt = dtObject;
    this.fields = dt.fields;

    this.ServicePath = servicesPath + "forum_access.service.php";
    this.GridId = id;
    this.Columns = 2;

    this.USER_ID = relatedObject.USER_ID;
    this.Tab = relatedObject.Tab;

    this.FindTable();
    this.ClearRecords();

    this.obj = relatedObject;
  }

  BaseBind() {}

  RequestCallback(req, obj) {
    if (obj.obj) {
      obj.obj.RequestBaseCallback(req, obj);
      obj.obj.Bind(obj.data);
    }
  }
}

/* Helper methods */

function AddForumAccess(user_id, target_forum_id, access, obj) {
  var req = new Requestor(servicesPath + "forum_access.service.php", obj);
  req.Callback = RefreshList;
  req.Request(["go", "FORUM_ID", "TARGET_USER_ID", "TARGET_FORUM_ID", "ACCESS"], ["add", obj.Forum.FORUM_ID, user_id, target_forum_id, access]);
};

function RefreshList(sender) {
  sender.obj.RequestCallback(sender.req, sender.obj);
};

function GetJournalUsers(input) {
  input.DelayedRequestor.obj.SetTabElementValue("FOUND_USERS", LoadingIndicator);
  var juRequest = new Requestor(servicesPath + "journal_users.service.php", input.DelayedRequestor.obj);
  juRequest.Callback = DrawUsers;
  juRequest.Request(["value"], [input.value]);
};

function DrawUsers(sender) {
  var el = sender.obj.Inputs["FOUND_USERS"];
  if (el) {
    el.innerHTML = "";
    var ul = d.createElement("ul");
    var prev_id = 0;
    var className = "";
    for (var i = 0, l = sender.data.length; i < l; i++) {
      var item = sender.data[i];
      className = ((prev_id == item.USER_ID) ? className : (className ? "" : "Dark"));
      item.ToString(i, sender.obj, prev_id, ul, className);
      prev_id = item.USER_ID;
    }
    el.appendChild(ul);
    sender.obj.Tab.Alerts.Clear();
    if (sender.more) {
      sender.obj.Tab.Alerts.Add("Более  20  результатов - уточните критерий поиска.", 1);
    }
  }
}