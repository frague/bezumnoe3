//1.8
/*
	SuperAdmin only functionality.
	Will be loaded only if server rights checking is > adminRights.
*/


/* Usermanager admins' section */

function umAdditionalExtraButtons(el, id, login, obj) {
	el.appendChild(MakeUserMenuLink(MakeButtonLink("ShowBlog(" + id + ",\"" + login + "\")", "������", obj, "")));
};

/* Journal */

function ShowBlog(id, name) {
	var tab_id = "j" + id;
	CreateUserTab(id, name, new JournalMessages(), "������", "", tab_id);
};

/* Admin Options */

var spoilerNames = ["������� ����", "�������", "���� �������", "������������ �������"];
var spoilerInits = [
	function(tab) {new News().LoadTemplate(tab)},
	function(tab) {new BannedAddresses().LoadTemplate(tab)},
	function(tab) {new SystemLog().LoadTemplate(tab)},
	function(tab) {new Statuses().LoadTemplate(tab)}
];



