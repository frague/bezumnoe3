//1.4
/*
	Admin only functionality.
	Will be loaded only if server rights checking is == adminRights.
*/

/* Options Admin tab */

function CreateAdminTab() {
	return new Tab(7, "Администрирование", 1, "", function(tab){LoadAndBindAdminOptionsToTab(tab,me.Id)})
};

/* Usermanager admins' section */

function umViewProfile(id, login, obj) {
	return MakeButtonLink("ShowUser(" + id + ",'" + login + "')", "Профиль", obj, "");
};

function ShowUser(id, name) {
	var tab_id = "u" + id;
	CreateUserTab(id, name, LoadAndBindProfileToTab, "", "", tab_id);
};

/* Admin Options */

var spoilerNames = ["Запреты"];
var spoilerInits = [
	function(tab) {LoadAndBindBannedAddressesToTab(tab)}
];


