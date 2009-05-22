//1.4
/*
	List of users who cannot comment messages in your journal
*/	

// Global reference to friendly journals
var forbiddenCommentersTab;

function ForbiddenCommenter(user_id, tab) {
	this.UserId = user_id;
	this.Tab = tab;

	this.ServicePath = servicesPath + "journal.forbidden.commenter.service.php";
	this.Init();
};

ForbiddenCommenter.prototype = new ListActions();

ForbiddenCommenter.prototype.PrintList = function(source) {
	if (forbiddenCommentersTab && forbiddenCommentersTab.ForbiddenCommentersHolder) {
		forbiddenCommentersTab.ForbiddenCommentersHolder.innerHTML = "";
		BindList(source, forbiddenCommentersTab.ForbiddenCommentersHolder, forbiddenCommentersTab, "Нет запретов на комментарии.");
	}
};

ForbiddenCommenter.prototype.TabOperations = function(tab) {
	if (tab && tab.ForbiddenCommentersHolder) {
		forbiddenCommentersTab = tab;
		tab.ForbiddenCommentersHolder.innerHTML = LoadingIndicator;
	}
};

/* Data Transfer Object */

function jfсdto(id, login) {
	this.fields = ["Id", "Login"];
	this.Init(arguments);
};

jfсdto.prototype = new DTO();

jfсdto.prototype.ToString = function(index, tab) {
	var li = d.createElement("li");

	li.appendChild(MakeButton("RemoveForbiddenCommenter("+this.Id+",this)", "icons/delete.gif", tab, "", "Удалить из списка"));
	li.appendChild(d.createTextNode(this.Login));
	return li;
};

/* Actions */

function LoadForbiddenCommenters(tab) {
	var f = new ForbiddenCommenter(0, tab.Tab ? tab.Tab : tab);
	f.Request();
};

function AddForbiddenCommenter(id, a) {
	var f = new ForbiddenCommenter(id, a.obj.Tab);
	f.Save();
};

function RemoveForbiddenCommenter(id, a) {
	var f = new ForbiddenCommenter(id, a.obj);
	f.Delete();
};