//4.1
/*
	SuperAdmin only functionality.
	Will be loaded only if server rights checking is > adminRights.
*/


/* Usermanager admins' section */

function umAdditionalExtraButtons(el, id, login, obj) {
	el.appendChild(MakeUserMenuLink(MakeButtonLink("ShowSettings(" + id + ",\"" + login + "\")", "���������", obj, "")));
	el.appendChild(MakeUserMenuLink(MakeButtonLink("ShowBlog(" + id + ",\"" + login + "\")", "������", obj, "")));
};

function ShowBlog(id, name) {
	CustomTab(id, name, JournalMessages, "j", "������");
};

function ShowSettings(id, name) {
	CustomTab(id, name, Settings, "s", "���������");
};

/* Admin Options */

var spoilerNames = [
	"������ ��������������", 
	"������� ����", 
	"�������", 
	"���� �������", 
	"��� ��������� ����", 
	"������������ �������",
	"�������",
	"����",
	"������ �� ����������"
];

var spoilerInits = [
	function(tab) {new LawCode().LoadTemplate(tab)},
	function(tab) {new News().LoadTemplate(tab)},
	function(tab) {new BannedAddresses().LoadTemplate(tab)},
	function(tab) {new SystemLog().LoadTemplate(tab)},
	function(tab) {new MessagesLog().LoadTemplate(tab)},
	function(tab) {new Statuses().LoadTemplate(tab)},
	function(tab) {new Rooms().LoadTemplate(tab)},
	function(tab) {new Bots().LoadTemplate(tab)},
	function(tab) {new ScheduledTasks().LoadTemplate(tab)}
];



