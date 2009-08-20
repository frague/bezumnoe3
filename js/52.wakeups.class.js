//2.5
/*
	Wakeup messages grid. Edit & delete buttons.
*/

function Wakeups() {
	this.fields = new Array("SEARCH", "DATE", "IS_INCOMING", "IS_OUTGOING");
	this.ServicePath = servicesPath + "wakeups.service.php";
	this.Template = "wakeups";
	this.ClassName = "Wakeups";
	this.Columns = 2;

	this.GridId = "WakeupsGrid";
};

Wakeups.prototype = new PagedGrid();

Wakeups.prototype.InitPager = function() {
	this.Pager = new Pager(this.Inputs[this.PagerId], function(){this.Tab.Wakeups.SwitchPage()}, this.PerPage);
};

Wakeups.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data, obj.Total);
	}
};

Wakeups.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);

	this.GroupSelfAssign(["buttonSearch", "ResetFilter", "linkRefresh"]);

	BindEnterTo(this.Inputs["SEARCH"], this.Inputs["buttonSearch"]);
	BindEnterTo(this.Inputs["IS_INCOMING"], this.Inputs["buttonSearch"]);
	BindEnterTo(this.Inputs["IS_OUTGOING"], this.Inputs["buttonSearch"]);
	new DatePicker(this.Inputs["DATE"]);
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

	var td0 = d.createElement("td");
	td0.className = "Centered";
	td0.innerHTML = date.Time();
	tr.appendChild(td0);


	var td1 = d.createElement("td");
	var h2 = d.createElement("h2");
	h2.className = (this.IsIncoming == "1" ? "Incoming" : "Outgoing");
	h2.innerHTML = (this.IsIncoming == "1" ? "от " : "для ") + this.UserName;

	td1.appendChild(h2);
	td1.innerHTML += this.Content;
	tr.appendChild(td1);

	return tr;
};