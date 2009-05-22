//2.4
/*
	Adding/removing user's friendly journals.
*/

// List actions class

function ListActions() {
};

ListActions.prototype.Init = function() {
	this.Tab.ListObject = this;
};

ListActions.prototype.Save = function() {
	this.Request(0);
};

ListActions.prototype.Delete = function() {
	this.Request(1);
};

ListActions.prototype.Request = function(state) {
	var params = MakeParametersPair("no-cache", Math.random(1000));;
	if (Math.round(this.UserId)) {
		params += MakeParametersPair("state", state);
		params += MakeParametersPair("user_id", this.UserId);
	}
	this.TabOperations(this.Tab);
	sendRequest(this.ServicePath, this.RequestCallback, params, this);
};

//ListActions.prototype.TabOperations = function(tab) {};	// Method to override
//ListActions.prototype.PrintList = function(source) {};	// Method to override

ListActions.prototype.RequestCallback = function(req, obj) {
	if (obj.Tab) {
		var tabObject = obj.Tab;
		tabObject.Alerts.Clear();

		var userlist = "";
		eval(req.responseText);
		obj.PrintList(userlist);
	}
};



// Global reference to friendly journals
var friendlyJournalTab;

function FriendlyJournal(friend_id, tab) {
	this.UserId = friend_id;
	this.Tab = tab;

	this.ServicePath = servicesPath + "journal.friend.service.php";
	this.Init();
};

FriendlyJournal.prototype = new ListActions();

FriendlyJournal.prototype.TabOperations = function(tab) {
	if (tab && tab.FriendlyJournalsHolder) {
		friendlyJournalTab = tab;
		tab.FriendlyJournalsHolder.innerHTML = LoadingIndicator;
	}
};

FriendlyJournal.prototype.PrintList = function(source) {
	if (friendlyJournalTab && friendlyJournalTab.FriendlyJournalsHolder) {
		friendlyJournalTab.FriendlyJournalsHolder.innerHTML = "";
		BindList(source, friendlyJournalTab.FriendlyJournalsHolder, friendlyJournalTab, "Дружественных журналов нет.");
	}
};

/* Data Transfer Object */

function jfdto(id, login) {
	this.fields = ["Id", "Login"];
	this.Init(arguments);
};

jfdto.prototype = new DTO();

jfdto.prototype.ToString = function(index, tab) {
	var li = d.createElement("li");

	li.appendChild(MakeButton("RemoveFriendlyJournal("+this.Id+",this)", "icons/delete.gif", tab, "", "Удалить из списка"));
	li.appendChild(d.createTextNode(this.Login));
	return li;
};

/* Actions */

function LoadFriendlyJournals(tab) {
	var f = new FriendlyJournal(0, tab.Tab ? tab.Tab : tab);
	f.Request();
};

function AddFriendlyJournal(id, a) {
	var f = new FriendlyJournal(id, a.obj.Tab);
	f.Save();
};

function RemoveFriendlyJournal(id, a) {
	var f = new FriendlyJournal(id, a.obj);
	f.Delete();
};