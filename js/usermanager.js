/*
  User Manager admin functionality
*/

class Userman extends Grid {
  constructor() {
    super();
    this.fields = ["BY_NAME", "BY_ROOM", "FILTER_BANNED", "FILTER_EXPIRED", "FILTER_TODAY", "FILTER_YESTERDAY", "FILTER_REGDATE", "REG_DATE"];
    this.ServicePath = servicesPath + "users.service.php";
    this.Template = "userman";
    this.ClassName = "Userman";

    this.GridId = "UsersContainer";
    this.Columns = 2;
  };

  BaseBind() {}

  RequestCallback(req, obj) {
    if (obj) {
      obj.more = 0;
      obj.RequestBaseCallback(req, obj);
      obj.Bind(obj.data);
      if (obj.more) {
        obj.Tab.Alerts.Add("Более 20  результатов - уточните критерий поиска.", 1);
      } else {
        if ((!obj.data || !obj.data.length) && userSearched) {
          obj.Tab.Alerts.Add("Пользователи не найдены.");
        }
      }
    }
  }

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);

    var assignee = ["BY_NAME", "BY_ROOM", "FILTER_BANNED", "FILTER_EXPIRED", "FILTER_TODAY", "FILTER_YESTERDAY", "FILTER_REGDATE", "REG_DATE"];
    for (var i = 0, l = assignee.length; i <l; i++) {
      var el = this.Inputs[assignee[i]];
      if (el) {
        var a = new DelayedRequestor(this, el);
        a.GetParams = GatherUsersParameters;
      }
    }

    BindRooms(this.Inputs["BY_ROOM"]);
    new DatePicker(this.Inputs["REG_DATE"]);
  }
};


/* User DTO */

class udto extends DTO {
  constructor(id, login, nickname) {
    super(id, login, nickname);
    this.fields = ["Id", "Login", "Nickname"];
    this.Init(arguments);
  }

  MakeTitle() {
    return this.Login + (this.Nickname ? "&nbsp;(" + this.Nickname + ")" : "");
  }

  ToString(index, obj) {
    var tr = MakeGridRow(index);

    var td1 = d.createElement("td");
    var name = this.MakeTitle();
    td1.appendChild(umDisplayName(this, name, td1, obj));
    tr.appendChild(td1);
    if (me.IsAdmin()) {
      td2 = d.createElement("td");
      cb = CreateBitInput("usr" + this.Id, 0);
      td2.appendChild(cb);
      tr.appendChild(td2);
    } else {
      td1.colSpan = 2;
    }
    return tr;
  }

  ToLiString(index, obj, callbackObj) {
    this.CallbackObj = callbackObj;

    var li = d.createElement("li");

    var a = d.createElement("a");
    a.Obj = this;
    a.href = voidLink;
    a.onclick = function() {this.Obj.Select()};
    a.innerHTML = this.MakeTitle();
    
    li.appendChild(a);
    return li;
  }

  Select() {
    if (this.CallbackObj) {
      this.CallbackObj.Select(this);
    };
  };
}

/* Helper methods */

function umDisplayName(userDTO, name, td, obj) {
  var a = d.createElement("a");
  a.innerHTML = name;
  a.href = voidLink;
  a.onclick = function(){ShowUserMenu(this, userDTO.Id, userDTO.Login, td, obj)};
  a.className = "Closed";
  return a;
};

var userMenu;
function ShowUserMenu(a, id, login, container, obj) {
  a.blur();
  if (HideUserMenu(id)) {
    return;
  };

  userMenu = d.createElement("table");
  userMenu.className = "UserMenu";
  userMenu.Container = container;
  userMenu.Id = id;
  userMenu.onclick = HideUserMenu;
  userMenu.Link = a;
  
  var tr = d.createElement("tr");
  if (me.IsAdmin()) {
    umExtraButtons(tr, id, login, obj);
  };
  userMenu.appendChild(tr);

  insertAfter(userMenu, a);
  a.className = "Opened";
};

function HideUserMenu(id) {
  if (userMenu) {
    userMenu.Link.className = "Closed";
    var i = userMenu.Id;
    userMenu.Container.removeChild(userMenu);
    userMenu = "";
    return id == i;
  }
  return false;
};

function MakeSection(title, className) {
  var td = d.createElement("td");
  var h3 = d.createElement("h4");
  if (className) {
    h3.className = className;
  }
  h3.innerHTML = title;
  td.appendChild(h3);
  return td;
};

function MakeUserMenuLink(el) {
  var li = d.createElement("li");
  li.appendChild(el);
  return li;
};

function GatherUsersParameters() {
  var result = this.obj.Gather();
  result += MakeParametersPair("type", this.Input.name);
  return result;
};
