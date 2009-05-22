//4.0
/*
	User Manager admin functionality
*/

function Userman() {
	this.fields = new Array("BY_NAME", "BY_ROOM", "BY_STATUS");
	this.ServicePath = servicesPath + "users.service.php";
	this.Template = "userman";

	this.GridId = "UsersContainer";
	this.Columns = 2;
};

Userman.prototype = new Grid();

Userman.prototype.BaseBind = function() {
	return;
};

Userman.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.more = 0;
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data);
		if (obj.more) {
			obj.Tab.Alerts.Add("Более	20	результатов	-	уточните критерий поиска.", 1);
		} else {
			if (!obj.data || !obj.data.length) {
				obj.Tab.Alerts.Add("Пользователи не найдены.");
			}
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

/*	var td2 = d.createElement("td");
	td2.className = "Centered";

	td2.appendChild(MakeButton("AddFriendlyJournal(" + this.Id + ",this)", "icons/add_friend.gif", obj, "", "Добавить дружественный журнал"));
	td2.appendChild(MakeButton("AddForbiddenCommenter(" + this.Id + ",this)", "icons/remove_user.gif", obj, "", "Запретить комментировать"));

	if (me && me.Rights >= adminRights) {
		umAddUserButtons(this, td2);
	}
	tr.appendChild(td2);*/

	return tr;
};

/* Helper methods */

function umDisplayName(userDTO, name, td, obj) {
	var a = d.createElement("a");
	a.innerHTML = name;
	a.href = voidLink;
	a.onclick = function(){ShowUserMenu(this, userDTO.Id, userDTO.Login, td, obj)};
	return a;
};

var userMenu;
function ShowUserMenu(a, id, login, container, obj) {
	if (HideUserMenu(id)) {
		return;
	};

	userMenu = d.createElement("ul");
	userMenu.Container = container;
	userMenu.Id = id;
	userMenu.onclick = HideUserMenu;

	if (me && me.Rights >= adminRights) {
		userMenu.appendChild(MakeUserMenuLink(umViewProfile(id, login, obj)));
	}

	userMenu.appendChild(MakeUserMenuLink(MakeButtonLink("AddFriendlyJournal(" + id + ",this)", "Добавить дружественный журнал", obj, "")));
	userMenu.appendChild(MakeUserMenuLink(MakeButtonLink("AddForbiddenCommenter(" + id + ",this)", "Запретить комментировать", obj, "")));

	insertAfter(userMenu, a);
};

function HideUserMenu(id) {
	if (userMenu) {
		var i = userMenu.Id;
		userMenu.Container.removeChild(userMenu);
		userMenu = "";
		return id == i;
	}
	return false;
};

function MakeUserMenuLink(el) {
	var li = d.createElement("li");
	li.appendChild(el);
	return li;
};

function LoadAndBindUsermanToTab(tab, user_id) {
	LoadAndBindObjectToTab(tab, user_id, new Userman(), "Userman", UsermanOnLoad);
};

function UsermanOnLoad(req, tab) {
	if (tab) {
		ObjectOnLoad(req, tab, "Userman");

		tab.Userman.AssignTabTo("BY_NAME");
		var by_room = tab.Userman.Inputs["BY_ROOM"];
		if (by_room) {
			tab.Userman.AssignTabTo("BY_ROOM");
			if (opener.rooms) {
				opener.rooms.Gather(by_room);
			} else {
				/* TODO: Request via Ajax or something */
			}
		}
	}
};

var lastValue = " ";
var usersTimer;

function GetUsers(input) {
	if (usersTimer) {
		clearTimeout(usersTimer);
	}
	usersTimer = setTimeout(function(){DoRequest(input)}, 500);
};

function DoRequest(input) {
	var userManager = input.Tab.Userman;
	if (userManager) {
		if (input) {
			if (input.value == lastValue) {
				return;
			}
			usersParams = MakeParametersPair("type", input.name);
			usersParams+= MakeParametersPair("value", input.value);
			lastValue = input.value;
			userManager.Request(usersParams);
		}
	}
};
