//2.7
/*
	Custom confirm contents.
*/

/* Nickname class */

var nicknames = new Collection();
var nicknames1;
var max_names = 5;
var name_length = 20;

function Nickname(index, id, name) {
	this.Index = index;
	this.Id = id;
	this.OldName = name;
	this.Name = name;
	this.Mode = "show";
};

Nickname.prototype.IsEmpty = function() {
	return (this.Name == "");
};

Nickname.prototype.HasChanged = function() {
	return (this.OldName != this.Name);
};

Nickname.prototype.CreateButton = function(src, action) {
	var button = d.createElement("input");
	button.type = "image";
	button.RelatedItem = this;
	eval("button.onclick = function(){" + action + "}");
	button.className = "Button";
	button.style.width = "15px";
	button.style.height = "15px";
	button.src = imagesPath + src;
	return button;
};

Nickname.prototype.CreateViewControls = function() {
	this.Div.innerHTML = "";
	if (this.Mode == "show") {
		this.Div.innerHTML += (this.Name ? this.Name + (this.Name == me.Login ? "&nbsp;(ваш логин)" : "") : "&lt;не задано&gt;") + "&nbsp;";
		if (this.Id) {
			this.Div.appendChild(this.CreateButton("edit_icon.gif", "Edit(this)"));
			if (this.Name) {
			this.Div.appendChild(this.CreateButton("delete_icon.gif", "Clear(this)"));
		}
		}
	} else {
		this.Input = d.createElement("input");
		this.Input.className = "NewNick";
		this.Input.value = this.Name;
		this.Input.setAttribute("maxlength", name_length);
		this.Div.appendChild(this.Input);
		if (this.Id) {
			this.Div.appendChild(this.CreateButton("icons/done.gif", "StopEditing(true)"));
			this.Div.appendChild(this.CreateButton("icons/cancel.gif", "StopEditing(false)"));
		}
	}
};

Nickname.prototype.ToString = function(holder) {
	if (!this.Li) {
		this.Li = d.createElement("li");
	} else {
		this.Li.innerHTML = "";
	}
	this.Radio = CreateRadio("nickname", ((!me.Nickname && this.Name == me.Login) || (me.Nickname && this.Name == me.Nickname)));
	this.Radio.RelatedItem = this;
	eval("this.Radio.onclick = function(){Select(this)}");

	this.Li.appendChild(this.Radio);
	this.Div = d.createElement("span");
	
	this.CreateViewControls();

	this.Li.appendChild(this.Div);
	holder.appendChild(this.Li);
};

Nickname.prototype.Gather = function(index) {
	var s = "";
	s += MakeParametersPair("id" + index, this.Id > 0 ? this.Id : "");
	s += MakeParametersPair("name" + index, this.Name);
	if (this.Radio.checked) {
		s += MakeParametersPair("selected", index);
	}
	return s;
};

/* Change Nickname class */

function ChangeNickname() {
};

ChangeNickname.prototype.CreateControls = function(container) {
	this.Holder = d.createElement("ul");
	this.Holder.className = "NamesList";

	this.Holder.innerHTML = LoadingIndicator;

	container.appendChild(this.Holder);

	this.Status = d.createElement("div");
	this.Status.className = "Status";
	container.appendChild(this.Status);
	nicknames1 = this;
};

ChangeNickname.prototype.RequestData = function() {
	sendRequest(servicesPath + "nickname.service.php", NamesResponse, "");
};

function NamesResponse(responseText) {
	if (nicknames1.Holder) {

		nicknames.Clear();
		nicknames.Add(new Nickname(0, 0, me.Login));

		try {
			eval(responseText);
		} catch (e) {
		}
		for (var i = nicknames.Count(); i <= max_names; i++) {
			nicknames.Add(new Nickname(i + 1, - (i + 1), ""));
		}
		if (NewNickname != "-1") {
			me.Nickname = NewNickname;
			if (PrintRooms) {
				PrintRooms();
			}
		}
		nicknames1.Holder.innerHTML = "";
		nicknames.ToString(nicknames1.Holder);
	}
};

var activeItem;
function Select(e) {
	if (e.RelatedItem) {
		StopEditing(true);
		var item = e.RelatedItem;
		if (item.IsEmpty()) {
			Edit(e);
		}
	}
};

function Edit(e) {
	if (e.RelatedItem) {
		StopEditing(true);
		var item = e.RelatedItem;
		item.Mode = "edit";
		item.CreateViewControls();
		item.Input.focus();
		activeItem = item;
	}
};

function Clear(e) {
	if (e.RelatedItem) {
		var item = e.RelatedItem;
		item.Name = "";
		item.CreateViewControls();
	}
};

function StopEditing(acceptChanges) {
	if (activeItem) {
		activeItem.Mode = "show";
		if (acceptChanges) {
			activeItem.Name = activeItem.Input.value;
		}
		activeItem.CreateViewControls();
	}
};


var nicknameSaving = 0;
function SaveNicknameChanges() {
	if (nicknameSaving) {
		return;
	}
	StopEditing(true);
	nicknameSaving = 1;
	setTimeout("UnLockSaving()", 10000);
	sendRequest(servicesPath + "nickname.service.php", SavingResults, nicknames.Gather());
};

function UnLockSaving() {
	nicknameSaving = 0;
};

function SavingResults(req) {
	UnLockSaving();
	status = "";
	NamesResponse(req);
	if (!status) {
		SetStatus("Изменения сохранены.");
		setTimeout("co.Hide()", 2000);
	}
	ForcePing();
};

var status;
function SetStatus(text) {
	nicknames1.Status.innerHTML = text;
	status = text;
};