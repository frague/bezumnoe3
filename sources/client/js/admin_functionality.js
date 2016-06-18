//2.7
/*
	Admin only functionality.
	Will be loaded only if server rights checking is == adminRights.
*/

/* Options Admin tab */

function createAdminTab() {
    return new Tab(7, "Администрирование", 1, "", function(tab) {new AdminOptions().loadTemplate(tab, me.Id)})
};

/* Usermanager admins' section */

function umExtraButtons(tr, id, login, obj) {
    var td1 = makeSection("Опции администратора:");
    var ul1 = d.createElement("ul");

    ul1.appendChild(makeUserMenuLink(makeButtonLink("showUser(" + id + ",'" + login + "')", "Профиль", obj, "")));
    if (me.isSuperAdmin()) {
        umAdditionalExtraButtons(ul1, id, login, obj);
    }
    td1.appendChild(ul1);
    tr.appendChild(td1);

    var td2 = makeSection("Операции:", "Red");
    var ul2 = d.createElement("ul");
    ul2.appendChild(makeUserMenuLink(makeButtonLink("deleteUser(" + id + ",'" + login + "', this)", "Удалить", obj, "Red")));
    td2.appendChild(ul2);
    tr.appendChild(td2);
};

/* Custom Tabs creation */

function customTab(id, name, o, tab_prefix, name_prefix) {
    var tab_id = tab_prefix + id;
    createUserTab(id, name, new o(), name_prefix, "", tab_id);
};

function showUser(id, name) {
    customTab(id, name, Profile, "u", "");
};

function deleteUser(id, name, a) {
    co.AlertType = false;
    co.Show(function() {deleteUserConfirmed(id, a.obj)}, "Удалить пользователя?", "Пользователь	<b>" + name + "</b> и все данные,	относящиеся к нему	(фотографии,	записи в журнале и форумах,	профиль) будут удалены.<br>Вы уверены?");
};

function deleteUserConfirmed(id, obj) {
    var req = new Requestor(servicesPath + "user_delete.service.php", obj);
    req.callback = refreshList;
    req.request(["user_id"], [id]);
    obj.inputs["FILTER_BANNED"].delayedRequestor.request();
};

/* Admin Options */

var spoilerNames = [
    "Кодекс администратора	(обязателен для прочтения)",
    "Запреты",
    "Комнаты"
];

var spoilerInits = [
    function(tab) {new LawCode().loadTemplate(tab)},
    function(tab) {new BannedAddresses().loadTemplate(tab)},
    function(tab) {}
];
