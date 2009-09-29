//1.6
/*
	Admin only functionality.
	Will be loaded only if server rights checking is == adminRights.
*/

/* Options Admin tab */

function CreateAdminTab() {
	return new Tab(7, "�����������������", 1, "", function(tab) {new AdminOptions().LoadTemplate(tab, me.Id)})
};

/* Usermanager admins' section */

function umExtraButtons(id, login, obj) {
 	var td = MakeSection("����� ��������������:");
 	var ul = d.createElement("ul");

	ul.appendChild(MakeUserMenuLink(MakeButtonLink("ShowUser(" + id + ",'" + login + "')", "�������", obj, "")));
	if (window.umAdditionalExtraButtons) {
		umAdditionalExtraButtons(ul, id, login, obj);
	}
	td.appendChild(ul);
	return td;
};

function ShowUser(id, name) {
	var tab_id = "u" + id;
	CreateUserTab(id, name, new Profile(), "", "", tab_id);
};

/* Admin Options */

var spoilerNames = [
	"������ ��������������&nbsp;(���������� ��� ���������)",
	"�������",
	"�������"
];

var spoilerInits = [
	function(tab) {new LawCode().LoadTemplate(tab)},
	function(tab) {new BannedAddresses().LoadTemplate(tab)},
	function(tab) {}
];


