//1.8
/*
	SuperAdmin only functionality.
	Will be loaded only if server rights checking is > adminRights.
*/


/* Usermanager admins' section */

function umAdditionalExtraButtons(el, id, login, obj) {
	el.appendChild(MakeUserMenuLink(MakeButtonLink("ShowBlog(" + id + ",\"" + login + "\")", "Журнал", obj, "")));
};

/* Journal */

function ShowBlog(id, name) {
	var tab_id = "j" + id;
	CreateUserTab(id, name, new JournalMessages(), "Журнал", "", tab_id);
};

/* Admin Options */

var spoilerNames = ["Новости чата", "Запреты", "Логи системы", "Персональные статусы"];
var spoilerInits = [
	function(tab) {new News().LoadTemplate(tab)},
	function(tab) {new BannedAddresses().LoadTemplate(tab)},
	function(tab) {new SystemLog().LoadTemplate(tab)},
	function(tab) {new Statuses().LoadTemplate(tab)}
];



