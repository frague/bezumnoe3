//2.3
/*
	Journal functionality: Blog templates, messages, settings
*/

function JournalsManager() {
	this.fields = ["SEARCH", "SHOW_FORUMS", "SHOW_JOURNALS", "SHOW_GALLERIES"];
	this.ServicePath = servicesPath + "journal_manager.service.php";
	this.Template = "journal_manager";
	this.ClassName = "JournalManager";
};

JournalsManager.prototype = new OptionsBase();

JournalsManager.prototype.request = function(params, callback) {
	this.BaseRequest(this.Gather(), callback);
};

JournalsManager.prototype.requestCallback = function(req) {
	this.requestBaseCallback(req);
	this.Bind();
};

JournalsManager.prototype.Bind = function() {
	var container = this.inputs["ForumsContainer"];
	if (container) {
		container.innerHTML = "";
		if (this.data && this.data.length) {
			for (var i = 0, l = this.data.length; i < l; i++) {
				this.data[i].ToString(i, container);
			}
		}
	}

	this.DisplayTabElement("CreateJournal", !this.HasJournal);
	this.SetTabElementValue("linkNewForum", (this.HasJournal ? "" : "Создать журнал"));
};

JournalsManager.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);

	this.FindRelatedControls();
	this.GroupSelfAssign(this.fields);
	this.GroupSelfAssign(["linkRefresh", "linkNewForum"]);
};

/* Forum line DTO */

var ForumTypes = {"f": "Форум", "g": "Фотогалерея", "j": "Журнал"};
function jjdto(id, title, type, access) {
	this.fields = ["FORUM_ID", "TITLE", "TYPE", "ACCESS"];
	this.Init(arguments);
};

jjdto.prototype = new DTO();

jjdto.prototype.ToShowView = function(index, container) {
	var t = this.MakeTitle();
	s = new Spoiler("_j" + (i + 1), t, 0, 0, function(tab) {new Journal().loadTemplate(tab, me.Id, me.Login)});
	s.Forum = this;
	s.ToString(container);
	if (!index) {
		s.Switch();
	}
};

jjdto.prototype.MakeTitle = function() {
	return ForumTypes[this.TYPE] + "	&laquo;" + this.TITLE + "&raquo;";
};

/* New Forum Creation */
function CreateForum(obj) {
	obj.BaseRequest("go=create&");
};
