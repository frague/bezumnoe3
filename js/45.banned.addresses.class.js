//6.5
/*
	List of forbidden addresses
*/

var BannedAddrsList;
function BannedAddresses() {
	this.fields = ["BAN_ID", "BAN_CHAT", "BAN_FORUM", "BAN_JOURNAL", "TYPE", "CONTENT", "COMMENT", "TILL"];
	this.defaultValues = ["", "1", "1", "1", "ip", "", "", ""];
	this.ServicePath = servicesPath + "banned.addresses.service.php";
	this.Template = "banned_addresses";
	this.GridId = "BannedAddresses";
	this.ClassName = this.GridId;
	this.Columns = 3;
};

BannedAddresses.prototype = new Grid();

BannedAddresses.prototype.BaseBind = function() {};

BannedAddresses.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		if (BannedAddrsList) {
			BannedAddrsList.Tab.Alerts.Clear();
		}

		obj.banData = [];
		obj.RequestBaseCallback(req, obj);

		if (BannedAddrsList) {
			// Adding entry from bans tab
			BannedAddrsList.Bind(obj.data);
			if (obj.banData.length) {
				BannedAddrsList.FillFrom(obj.banData);
				BannedAddrsList.BindFields(BannedAddrsList.fields);
				BannedAddrsList.SetFormName("Редактировать запрет	" + BannedAddrsList["CONTENT"] + ":");
			}
		} else {
			// Adding entry from user profile
			obj.Bind(obj.data);
		}
		if (BannedAddrsList && !BannedAddrsList.Tab.Alerts.HasErrors && !BannedAddrsList.Tab.Alerts.IsEmpty) {
			BannedAddrsList.Reset();
			BannedAddrsList.SetFormName("");
		}
	}
};

BannedAddresses.prototype.SetFormName = function(name) {
	if (!name) {
		name = "Добавить запрет:";
	}
	this.SetTabElementValue("FORM_TITLE", name);
};

BannedAddresses.prototype.TemplateLoaded = function(req) {
	BannedAddrsList = this;
	this.TemplateBaseLoaded(req);

	this.GroupSelfAssign(["RefreshBannedAddresses", "ResetBannedAddresses"]);

	new DatePicker(this.Inputs["TILL"]);

	/* Submit button */
	this.Tab.AddSubmitButton("SaveBan(this)", "", this);
	BindEnterTo(this.Inputs["CONTENT"], this.Tab.SubmitButton);
	BindEnterTo(this.Inputs["TILL"], this.Tab.SubmitButton);
};

/* Banned Address Data Transfer Object */

function badto(id, content, type, comment, admin, date, till, chat, forum, journal) {
	this.fields = ["Id", "Content", "Type", "Comment", "Admin", "Date", "Till", "Chat", "Forum", "Journal"];
	this.Init(arguments);

	this.Date = ParseDate(this.Date);
	this.Till = ParseDate(this.Till);

	this.BanNames = ["чат", "форум", "журналы"];
	this.Bans = [this.Chat, this.Forum, this.Journal];
};

badto.prototype = new DTO();

badto.prototype.ToString = function(index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
	var h2 = d.createElement("h2");
	h2.innerHTML = this.Content;
	td1.appendChild(h2);
	td1.appendChild(d.createTextNode("(" + this.Type + ")"));
	tr.appendChild(td1);

	var td2 = d.createElement("td");
	td2.appendChild(MakeDiv((this.Comment ? "&laquo;" + this.Comment + "&raquo;" + (this.Admin ? ",	" : "") : "") + (this.Admin ? this.Admin : ""), "h2"));
	td2.appendChild(MakeDiv("c " + this.Date.ToPrintableString() + (!this.Till.IsEmpty ? " по " + this.Till.ToPrintableString() : "")));

	result = "";
	var comma = false;
	for (var i = 0; i < 3; i++) {
		if (this.Bans[i]) {
			result += (comma ? ",	" : "") + this.BanNames[i];
			comma = true;
		}
	}
		
	td2.appendChild(MakeDiv("Запрет:	<b>" + result + "</b>"));
	tr.appendChild(td2);

	var td3 = d.createElement("td");
	td3.className = "Centered";
	td3.appendChild(MakeButton("EditBan(this," + this.Id + ")", "icons/edit.gif", obj));
	td3.appendChild(MakeButton("DeleteBan(this," + this.Id + ")", "delete_icon.gif", obj));
	tr.appendChild(td3);
	
	return tr;
};

/* Client events methods */

function SaveBan(a) {
	a.obj.Save();
};

function SendItemRequest(a, id, go) {
	var params = MakeParametersPair("go", go);
	params += MakeParametersPair("id", id);
	a.obj.Request(params);
};

function DeleteBan(a, id) {
	SendItemRequest(a, id, "delete");
};

function EditBan(a, id) {
	SendItemRequest(a, id, "edit");
};

function ResetBanForm(a) {
	var jc = a.obj;
	if (jc) {
		jc.Reset();
		jc.SetFormName("");
	}
};

/* Helper method */

var ipPattern = new RegExp("^([0-9]{1,3}\.){3}[0-9]{1,3}$");
function LockIP(a) {
	if (a.obj) {
		var profile = a.obj;

		var addr = profile["SESSION_ADDRESS"];
		if (addr) {
			var pos = addr.indexOf("[");
			if (pos > 0) {
				addr = addr.substr(0, pos - 1);
			}
			var obj = new BannedAddresses();
			obj.Tab = profile.Tab;
			profile.Tab.BannedAddresses = obj;

			var comment = "Доступ к чату для пользователя " + profile["LOGIN"];
			obj.FillFrom([-1, 1, 0, 0, (addr.match(ipPattern) ? "ip" : "host"), addr, comment, ""]);
			obj.USER_ID = profile["USER_ID"];
			obj.Save();
		}
	}
};
