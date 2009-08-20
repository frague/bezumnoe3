//3.2
/*
	Display messages log with filter by date, room & keywords
*/

function MessagesLog() {
	this.fields = ["DATE", "SEARCH", "ROOM_ID"];
	this.ServicePath = servicesPath + "messages.log.service.php";
	this.ClassName = "MessagesLog";
	this.Template = "messages_log";
	this.GridId = "MessagesLogGrid";
	this.Columns = 3;
	this.PerPage = 50;
};

MessagesLog.prototype = new PagedGrid();

MessagesLog.prototype.BaseBind = function() {};

MessagesLog.prototype.InitPager = function() {
	this.Pager = new Pager(this.Inputs[this.PagerId], function(){this.Tab.MessagesLog.SwitchPage()}, this.PerPage);
};

MessagesLog.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data, obj.Total);
		obj.BindRooms(obj.Rooms);
	}
};

MessagesLog.prototype.BindRooms = function(rooms) {
	var select = this.Inputs["ROOM_ID"];
	if (select) {
		select.innerHTML = "";
		for (var i = 0, l = rooms.length; i < l; i++) {
			if (!this.ROOM_ID) {
				this.ROOM_ID = rooms[i].Id;
			}
			AddSelectOption(select, rooms[i].Title, rooms[i].Id, this.ROOM_ID == rooms[i].Id);
		}
	}
};

// Template loading
MessagesLog.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);

	this.GroupSelfAssign(["RefreshMessagesLog", "ResetFilter", "ROOM_ID"]);

	new DatePicker(this.Inputs["DATE"]);

	BindEnterTo(this.Inputs["DATE"], this.Inputs["RefreshMessagesLog"]);
	BindEnterTo(this.Inputs["ROOM_ID"], this.Inputs["RefreshMessagesLog"]);
	BindEnterTo(this.Inputs["SEARCH"], this.Inputs["RefreshMessagesLog"]);

	if (this.Init) {
		this.Init();
	}
};

/* Message Data Transfer Object */

var lastMessageDate;

function mdto(date, name, name_to, text, color) {
	this.fields = ["Date", "Name", "NameTo", "Text", "Color"];
	this.Init(arguments);
};

mdto.prototype = new DTO();

mdto.prototype.ToString = function(index, obj, holder) {
	if (!index) {
		lastMessageDate = "";
	}

	var date = ParseDate(this.Date);
	var dateString = date.ToPrintableString();
	if (date && dateString && dateString != lastMessageDate && holder) {
		lastMessageDate = dateString;
		holder.appendChild(MakeGridSubHeader(index, obj.Columns, dateString));
	}

	var tr = MakeGridRow(index);
	if (this.NameTo) {
		tr.className += " Highlight Warning";
	}

	
	var td1 = d.createElement("td");
	td1.className = "Centered";
	td1.innerHTML = date.Time();
	tr.appendChild(td1);

	var td3 = d.createElement("td");
	if (this.Name) {
		var td2 = d.createElement("td");
		td2.innerHTML = this.Name + (this.NameTo ? " для " + this.NameTo : "&nbsp;");
		tr.appendChild(td2);
	} else {
		td3.colSpan = 2;
	}

	if (this.Color) {
		td3.style.color = this.Color;
	}
	td3.innerHTML = this.Text + "&nbsp;";
	tr.appendChild(td3);
	
	return tr;
};

/* Room Data Transfer Object */

function rdto(id, title, is_deleted) {
	this.fields = ["Id", "Title", "IsDeleted"];
	this.Init(arguments);
};

rdto.prototype = new DTO();
