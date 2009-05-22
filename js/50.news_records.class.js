//3.4
/*
	Displaying news/guestbook messages grid
*/

function NewsRecords() {
	this.fields = ["NEWS_RECORD_ID", "OWNER_ID", "TITLE", "CONTENT", "IS_HIDDEN", "DATE"];
	this.ServicePath = servicesPath + "news_records.service.php";
	this.Template = "news_records";
	this.GridId = "NewsRecordsGrid";
	this.Columns = 2;
};

NewsRecords.prototype = new EditablePagedGrid();

NewsRecords.prototype.BaseBind = function() {};

NewsRecords.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data, obj.Total);
	}
};

/* Banned Address Data Transfer Object */

function nrdto(id, owner_id, title, content, is_hidden) {
	this.fields = ["Id", "OwnerId", "Title", "Content", "IsHidden", "Date"];
	this.Init(arguments);
};

nrdto.prototype = new EditableDTO();

nrdto.prototype.ToShowView = function(index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
		var date = ParseDate(this.Date).ToPrintableString();
		td1.appendChild(MakeDiv(date + ":	" + this.Title, "h2"));
		td1.appendChild(MakeDiv(this.Content));
	tr.appendChild(td1);

	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

nrdto.prototype.ToEditView = function(index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
		td1.appendChild(MakeDiv("Дата:"));

		this.DateInput = d.createElement("input");
		this.DateInput.value = this.Date;
		td1.appendChild(this.DateInput);
		new DatePicker(this.DateInput);

		td1.appendChild(MakeDiv("Заголовок:"));

		this.TitleInput = d.createElement("input");
		this.TitleInput.value = this.Title;
		this.TitleInput.className = "Wide";
		td1.appendChild(this.TitleInput);

		td1.appendChild(MakeDiv("Содержание:"));

		this.ContentInput = d.createElement("textarea");
		this.ContentInput.value = this.Content;
		this.ContentInput.className = "Wide NewsDescription";
		td1.appendChild(this.ContentInput);

	tr.appendChild(td1);
	tr.appendChild(this.MakeButtonsCell());
	return tr;
};


/* Helper methods */

function LoadAndBindNewsRecordsToTab(tab, user_id) {
	LoadAndBindObjectToTab(tab, user_id, new NewsRecords(), "NewsRecords", NewsRecordsOnLoad);
};

function NewsRecordsOnLoad(req, tab) {
	if (tab) {
		ObjectOnLoad(req, tab, "NewsRecords");
		tab.NewsRecords.GroupSelfAssign(["AddNewsRecord", "RefreshNewsRecords"]);
	}
};

function AddNewsRecord(a) {
	if (a.obj) {
		a.obj.AddRow(new nrdto(0, a.obj["USER_ID"], "Новое сообщение", "", 0, ""));
	}
};