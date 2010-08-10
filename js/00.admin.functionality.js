//2.4
/*
	Admin only functionality.
	Will be loaded only if server rights checking is == adminRights.
*/

/* Options Admin tab */

function CreateAdminTab() {
	return new Tab(7, "Администрирование", 1, "", function(tab) {new AdminOptions().LoadTemplate(tab, me.Id)})
};

/* Usermanager admins' section */

function umExtraButtons(id, login, obj) {
 	var td = MakeSection("Опции администратора:");
 	var ul = d.createElement("ul");

	ul.appendChild(MakeUserMenuLink(MakeButtonLink("ShowUser(" + id + ",'" + login + "')", "Профиль", obj, "")));
	ul.appendChild(MakeUserMenuLink(MakeButtonLink("DeleteUser(" + id + ",'" + login + "', this)", "Удалить", obj, "Red")));
	if (me.IsSuperAdmin()) {
		umAdditionalExtraButtons(ul, id, login, obj);
	}
	td.appendChild(ul);
	return td;
};

function ShowUser(id, name) {
	var tab_id = "u" + id;
	CreateUserTab(id, name, new Profile(), "", "", tab_id);
};

function DeleteUser(id, name, a) {
	co.Show(function() {DeleteUserConfirmed(id, a.obj)}, "Удалить пользователя?", "Пользователь <b>" + name + "</b> и все данные, относящиеся к нему	(фотографии, записи в журнале и форумах, профиль) будут удалены.<br>Вы уверены?");
};

function DeleteUserConfirmed(id, obj) {
	var req = new Requestor(servicesPath + "user_delete.service.php", obj);
	req.Callback = RefreshList;
	req.Request(["user_id"], [id]);
	obj.Inputs["BY_NAME"].DelayedRequestor.Request();
};

/* Admin Options */

var spoilerNames = [
	"Кодекс администратора&nbsp;(обязателен для прочтения)",
	"Запреты",
	"Комнаты"
];

var spoilerInits = [
	function(tab) {new LawCode().LoadTemplate(tab)},
	function(tab) {new BannedAddresses().LoadTemplate(tab)},
	function(tab) {}
];


