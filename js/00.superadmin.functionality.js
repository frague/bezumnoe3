//1.5
/*
	SuperAdmin only functionality.
	Will be loaded only if server rights checking is > adminRights.
*/

/* Usermanager admins' section */


function umViewProfile(id, login, obj) {
	return MakeButtonLink("ShowUser(" + id + ",'" + login + "')", "Профиль", obj, "");
};

function umAddUserButtons(userDTO, holder) {
	holder.appendChild(MakeButton("ShowBlog(" + userDTO.Id + ",\"" + userDTO.Login + "\")", "icons/journal.gif", "", "", "Журнал"));
};

function ShowUser(id, name) {
	var tab_id = "u" + id;
	CreateUserTab(id, name, LoadAndBindProfileToTab, "", "", tab_id);
};


/* Journal */

function ShowBlog(id, name) {
	var tab_id = "j" + id;
	CreateUserTab(id, name, LoadAndBindJournalMessagesToTab, "Журнал", "", tab_id);
};

/* Admin Options */

var spoilerNames = ["Новости чата", "Запреты", "Логи системы", "Персональные статусы"];
var spoilerInits = [
	function(tab) {LoadAndBindNewsToTab(tab)},
	function(tab) {LoadAndBindBannedAddressesToTab(tab)},
	function(tab) {LoadAndBindSystemLogToTab(tab)},
	function(tab) {LoadAndBindStatusesToTab(tab)}
];


