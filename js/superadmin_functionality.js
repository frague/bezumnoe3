//4.1
/*
	SuperAdmin only functionality.
	Will be loaded only if server rights checking is > adminRights.
*/


/* Usermanager admins' section */

function umAdditionalExtraButtons(el, id, login, obj) {
    el.appendChild(makeUserMenuLink(makeButtonLink("showSettings(" + id + ",\"" + login + "\")", "Настройки", obj, "")));
    el.appendChild(makeUserMenuLink(makeButtonLink("showBlog(" + id + ",\"" + login + "\")", "Журнал", obj, "")));
};

function showBlog(id, name) {
    customTab(id, name, JournalMessages, "j", "Журнал");
};

function showSettings(id, name) {
    customTab(id, name, Settings, "s", "Настройки");
};

/* Admin Options */

var spoilerNames = [
    "Кодекс администратора",
    "Новости чата",
    "Запреты",
    "Логи системы",
    "Лог сообщений чата",
    "Персональные статусы",
    "Комнаты",
    "Боты",
    "Задачи по расписанию"
];

var spoilerInits = [
    function(tab) {new LawCode().loadTemplate(tab)},
    function(tab) {new News().loadTemplate(tab)},
    function(tab) {new BannedAddresses().loadTemplate(tab)},
    function(tab) {new SystemLog().loadTemplate(tab)},
    function(tab) {new MessagesLog().loadTemplate(tab)},
    function(tab) {new Statuses().loadTemplate(tab)},
    function(tab) {new Rooms().loadTemplate(tab)},
    function(tab) {new Bots().loadTemplate(tab)},
    function(tab) {new ScheduledTasks().loadTemplate(tab)}
];
