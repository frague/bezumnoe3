//4.7
/*
	User Manager admin functionality
*/

function Userman() {
	this.fields = new Array("BY_NAME", "BY_ROOM", "BY_STATUS");
	this.ServicePath = servicesPath + "users.service.php";
	this.Template = "userman";
	this.ClassName = "Userman";

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
			if ((!obj.data || !obj.data.length) && userSearched) {
				obj.Tab.Alerts.Add("Пользователи не найдены.");
			}
		}
	}
};

Userman.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);

	this.AssignTabTo("BY_NAME");
	var by_room = this.Inputs["BY_ROOM"];
	if (by_room) {
		this.AssignTabTo("BY_ROOM");
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

	userMenu = d.createElement("ul");
	userMenu.Container = container;
	userMenu.Id = id;
	userMenu.onclick = HideUserMenu;
	userMenu.Link = a;

	if (window.umViewProfile) {
		userMenu.appendChild(MakeUserMenuLink(umViewProfile(id, login, obj)));
	};
	if (window.umAddUserButtons) {
		userMenu.appendChild(umAddUserButtons(id, login, obj));
	};

	userMenu.appendChild(MakeUserMenuLink(MakeButtonLink("AddFriendlyJournal(" + id + ",this)", "Добавить дружественный журнал", obj, "")));
	userMenu.appendChild(MakeUserMenuLink(MakeButtonLink("AddForbiddenCommenter(" + id + ",this)", "Запретить комментировать", obj, "")));

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

function MakeUserMenuLink(el) {
	var li = d.createElement("li");
	li.appendChild(el);
	return li;
};

var lastValue = " ";
var usersTimer;
var userSearched = 0;

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
			userSearched = 1;
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
