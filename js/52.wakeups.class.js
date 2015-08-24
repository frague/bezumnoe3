//2.8
/*
	Wakeup messages grid. Edit & delete buttons.
*/

function Wakeups() {
	this.fields = new Array("SEARCH", "DATE", "IS_INCOMING", "IS_OUTGOING");
	this.ServicePath = servicesPath + "wakeups.service.php";
	this.Template = "wakeups";
	this.ClassName = "Wakeups";
	this.Columns = 3;
	this.PerPage = 20;

	this.GridId = "WakeupsGrid";
};

Wakeups.prototype = new PagedGrid();

Wakeups.prototype.InitPager = function() {
	this.Pager = new Pager(this.inputs[this.PagerId], function(){this.Tab.Wakeups.SwitchPage()}, this.PerPage);
};

Wakeups.prototype.requestCallback = function(req, obj) {
	if (obj) {
		obj.requestBaseCallback(req, obj);
		obj.Bind(obj.data, obj.Total);
	}
};

Wakeups.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);

	this.GroupSelfAssign(["buttonSearch", "ResetFilter", "linkRefresh"]);

	BindEnterTo(this.inputs["SEARCH"], this.inputs["buttonSearch"]);
	BindEnterTo(this.inputs["IS_INCOMING"], this.inputs["buttonSearch"]);
	BindEnterTo(this.inputs["IS_OUTGOING"], this.inputs["buttonSearch"]);
	new DatePicker(this.inputs["DATE"]);
};

Wakeups.prototype.CustomReset = function() {
	this.SetTabElementValue("IS_INCOMING", 1);
	this.SetTabElementValue("IS_OUTGOING", 1);
};

/* Wakeup Record Data Transfer Object */

var lastWakeDate;

function wdto(id, user_id, user_name, is_incoming, date, content, is_read) {
	this.fields = ["Id", "UserId", "UserName", "IsIncoming", "Date", "Content", "IsRead"];
	this.Init(arguments);
};

wdto.prototype = new DTO();

wdto.prototype.ToString = function(index, obj, holder) {
	if (!index) {
		lastWakeDate = "";
	}

	var date = ParseDate(this.Date);
	var dateString = date.ToPrintableString();
	if (date && dateString && dateString != lastWakeDate && holder) {
		lastWakeDate = dateString;
		holder.appendChild(MakeGridSubHeader(index, obj.Columns, dateString));
	}

	var tr = MakeGridRow(index);
	tr.className += this.IsRead ? "" : " Unread";
	tr.className += (this.IsIncoming == "1" ? " Incoming" : " Outgoing");

	var td0 = d.createElement("td");
	td0.className = "Centered";
	td0.innerHTML = date.Time();
	tr.appendChild(td0);

	var td1 = d.createElement("td");
	td1.className = "Centered";
	var sender = "<i>вы сами (" + this.UserName + ")</i>";
	if (!me || this.UserId != me.Id) {
		sender = (this.IsIncoming == "1" ? "от " : "для ") + this.UserName;
	}
	td1.innerHTML = sender;
	tr.appendChild(td1);

	var td2 = d.createElement("td");
	td2.innerHTML = this.Content;
	tr.appendChild(td2);


	return tr;
};
