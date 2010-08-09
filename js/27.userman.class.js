//5.4
/*
	User Manager admin functionality
*/

function Userman() {
	this.fields = ["BY_NAME", "BY_ROOM", "FILTER_BANNED", "FILTER_EXPIRED", "FILTER_TODAY", "FILTER_YESTERDAY"];
	this.ServicePath = servicesPath + "users.service.php";
	this.Template = "userman";
	this.ClassName = "Userman";

	this.GridId = "UsersContainer";
	this.Columns = 2;
};

Userman.prototype = new Grid();

Userman.prototype.BaseBind = function() {};

Userman.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.more = 0;
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data);
		if (obj.more) {
			obj.Tab.Alerts.Add("Более	20	результатов	-	уточните критерий поиска.", 1);
		} else {
			if ((!obj.data || !obj.data.length) && userSearched) {
				obj.Tab.Alerts.Add("Пользователи не найдены.");
			}
		}
	}
};

Userman.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);

	var assignee = ["BY_NAME", "BY_ROOM", "FILTER_BANNED", "FILTER_EXPIRED", "FILTER_TODAY", "FILTER_YESTERDAY"];
	for (var i = 0, l = assignee.length; i <l; i++) {
		var el = this.Inputs[assignee[i]];
		if (el) {
			var a = new DelayedRequestor(this, el);
			a.GetParams = GatherUsersParameters;
		}
	}

	var by_room = this.Inputs["BY_ROOM"];
	if (by_room) {
		if (opener.rooms) {
			opener.rooms.Gather(by_room);
		} else {
			/* TODO: Request via Ajax or something */
		}
	}
};



/* User DTO */

function udto(id, login, nickname) {
	this.fields = ["Id", "Login", "Nickname"];
	this.Init(arguments);
};

udto.prototype = new DTO();

udto.prototype.ToString = function(index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
	var name = this.Login + (this.Nickname ? "&nbsp;(" + this.Nickname + ")" : "");
	td1.appendChild(umDisplayName(this, name, td1, obj));
	tr.appendChild(td1);
	return tr;
};

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
		tr.appendChild(umExtraButtons(id, login, obj));
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

function MakeSection(title) {
	var td = d.createElement("td");
	var h3 = d.createElement("h4");
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
