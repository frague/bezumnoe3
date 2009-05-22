//1.7
/*
	SuperAdmin only functionality.
	Will be loaded only if server rights checking is > adminRights.
*/


/* Usermanager admins' section */

function umAddUserButtons(id, login, obj) {
	return MakeUserMenuLink(MakeButtonLink("ShowBlog(" + id + ",\"" + login + "\")", "������", obj, ""));
};

/* Journal */

function ShowBlog(id, name) {
	var tab_id = "j" + id;
	CreateUserTab(id, name, LoadAndBindJournalMessagesToTab, "������", "", tab_id);
};

/* Admin Options */

var spoilerNames = ["������� ����", "�������", "���� �������", "������������ �������"];
var spoilerInits = [
	function(tab) {LoadAndBindNewsToTab(tab)},
	function(tab) {LoadAndBindBannedAddressesToTab(tab)},
	function(tab) {LoadAndBindSystemLogToTab(tab)},
	function(tab) {LoadAndBindStatusesToTab(tab)}
];



