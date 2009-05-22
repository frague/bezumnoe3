//1.3
/*
	Admin only functionality.
	Will be loaded only if server rights checking is == adminRights.
*/

/* Usermanager admins' section */

function umViewProfile(id, login, obj) {
	return MakeButtonLink("ShowUser(" + id + ",'" + login + "')", "Џа®дЁ«м", obj, "");
};


function umAddUserButtons(userDTO, holder) {
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


