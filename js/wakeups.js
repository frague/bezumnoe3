/*
  Wakeup messages grid. Edit & delete buttons.
*/

class Wakeups extends PagedGrid {
  constructor() {
    super();
    this.fields = ["SEARCH", "DATE", "IS_INCOMING", "IS_OUTGOING"];
    this.ServicePath = servicesPath + "wakeups.service.php";
    this.Template = "wakeups";
    this.ClassName = "Wakeups";
    this.Columns = 3;
    this.PerPage = 20;

    this.GridId = "WakeupsGrid";
  }

  InitPager() {
    this.Pager = new Pager(
      this.Inputs[this.PagerId], 
      () => {this.Tab.Wakeups.SwitchPage()}, 
      this.PerPage
    );
  }

  RequestCallback(req, obj) {
    if (obj) {
      obj.RequestBaseCallback(req, obj);
      obj.Bind(obj.data, obj.Total);
    }
  }

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);

    this.GroupSelfAssign(["buttonSearch", "ResetFilter", "linkRefresh"]);

    BindEnterTo(this.Inputs["SEARCH"], this.Inputs["buttonSearch"]);
    BindEnterTo(this.Inputs["IS_INCOMING"], this.Inputs["buttonSearch"]);
    BindEnterTo(this.Inputs["IS_OUTGOING"], this.Inputs["buttonSearch"]);
    new DatePicker(this.Inputs["DATE"]);
  }

  CustomReset() {
    this.SetTabElementValue("IS_INCOMING", 1);
    this.SetTabElementValue("IS_OUTGOING", 1);
  }
};

/* Wakeup Record Data Transfer Object */

var lastWakeDate;

class wdto extends DTO {
  constructor(id, user_id, user_name, is_incoming, date, content, is_read) {
    super(arguments);
    this.fields = ["Id", "UserId", "UserName", "IsIncoming", "Date", "Content", "IsRead"];
    this.Init(arguments);
  };

  ToString(index, obj, holder) {
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

    var td0 = document.createElement("td");
    td0.className = "Centered";
    td0.innerHTML = date.Time();
    tr.appendChild(td0);

    var td1 = document.createElement("td");
    td1.className = "Centered";
    var sender = "<i>вы сами (" + this.UserName + ")</i>";
    if (!me || this.UserId != me.Id) {
      sender = (this.IsIncoming == "1" ? "от " : "для ") + this.UserName;
    }
    td1.innerHTML = sender;
    tr.appendChild(td1);

    var td2 = document.createElement("td");
    td2.innerHTML = this.Content;
    tr.appendChild(td2);

    return tr;
  }
}