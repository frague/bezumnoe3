//2.7
/*
	Admin only functionality.
	Will be loaded only if server rights checking is == adminRights.
*/

/* Options Admin tab */

function CreateAdminTab() {
	return new Tab(7, "�����������������", 1, "", function(tab) {new AdminOptions().LoadTemplate(tab, me.Id)})
};

/* Usermanager admins' section */

function umExtraButtons(tr, id, login, obj) {
 	var td1 = MakeSection("����� ��������������:");
 	var ul1 = d.createElement("ul");

	ul1.appendChild(MakeUserMenuLink(MakeButtonLink("ShowUser(" + id + ",'" + login + "')", "�������", obj, "")));
	if (me.IsSuperAdmin()) {
		umAdditionalExtraButtons(ul1, id, login, obj);
	}
	td1.appendChild(ul1);
	tr.appendChild(td1);

 	var td2 = MakeSection("��������:", "Red");
 	var ul2 = d.createElement("ul");
	ul2.appendChild(MakeUserMenuLink(MakeButtonLink("DeleteUser(" + id + ",'" + login + "', this)", "�������", obj, "Red")));
	td2.appendChild(ul2);
	tr.appendChild(td2);
};

/* Custom Tabs creation */

function CustomTab(id, name, o, tab_prefix, name_prefix) {
	var tab_id = tab_prefix + id;
	CreateUserTab(id, name, new o(), name_prefix, "", tab_id);
};

function ShowUser(id, name) {
	CustomTab(id, name, Profile, "u", "");
};

function DeleteUser(id, name, a) {
	co.AlertType = false;
	co.Show(function() {DeleteUserConfirmed(id, a.obj)}, "������� ������������?", "������������	<b>" + name + "</b> � ��� ������,	����������� � ����	(����������,	������ � ������� � �������,	�������) ����� �������.<br>�� �������?");
};

function DeleteUserConfirmed(id, obj) {
	var req = new Requestor(servicesPath + "user_delete.service.php", obj);
	req.Callback = RefreshList;
	req.Request(["user_id"], [id]);
	obj.Inputs["FILTER_BANNED"].DelayedRequestor.Request();
};

/* Admin Options */

var spoilerNames = [
	"������ ��������������	(���������� ��� ���������)",
	"�������",
	"�������"
];

var spoilerInits = [
	function(tab) {new LawCode().LoadTemplate(tab)},
	function(tab) {new BannedAddresses().LoadTemplate(tab)},
	function(tab) {}
];


