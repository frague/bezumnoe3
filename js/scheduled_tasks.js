/*
  Scheduled Tasks management
*/

class ScheduledTasks extends EditablePagedGrid {
  constructor() {
    super();
    this.fields = ["SCHEDULED_TASK_ID", "TYPE", "EXECUTION_DATE", "PERIODICITY", "IS_ACTIVE", "inactivated", "status", "unban", "expired_sessions", "ratings"];
    this.ServicePath = servicesPath + "scheduled_tasks.service.php";
    this.Template = "scheduled_tasks";
    this.ClassName = "ScheduledTasks";
    this.GridId = "ScheduledTasksGrid";
    this.Columns = 5;
  }

  BaseBind() {}

  InitPager() {
    this.Pager = new Pager(
      this.Inputs[this.PagerId], 
      () => {this.Tab.ScheduledTasks.SwitchPage()}, 
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
    this.GroupSelfAssign(["RefreshScheduledTasks"]);

    // System log checkboxes
    BindEnterTo(this.Inputs["status"], this.Inputs["RefreshScheduledTasks"]);
    BindEnterTo(this.Inputs["unban"], this.Inputs["RefreshScheduledTasks"]);
    BindEnterTo(this.Inputs["expired_sessions"], this.Inputs["RefreshScheduledTasks"]);
    BindEnterTo(this.Inputs["ratings"], this.Inputs["RefreshScheduledTasks"]);
    BindEnterTo(this.Inputs["inactivated"], this.Inputs["RefreshScheduledTasks"]);
  }
}

/* Status Data Transfer Object */

class stdto extends EditableDTO {
  constructor(id, rights, color, title) {
    super(arguments);
    this.fields = ["Id", "Type", "ExecutionDate", "Periodicity", "IsActive"];
    this.Init(arguments);
  }

  ToShowView(index, obj) {
    var tr = MakeGridRow(index);

    var td1 = document.createElement("td");
    td1.className = "Centered";
    td1.appendChild(CreateBooleanImage(this.IsActive));
    tr.appendChild(td1);

    var td2 = document.createElement("td");
    td2.innerHTML = this.Type;
    tr.appendChild(td2);

    var td3 = document.createElement("td");
    td3.innerHTML = this.ExecutionDate;
    tr.appendChild(td3);

    var td4 = document.createElement("td");
    td4.innerHTML = this.Periodicity;
    tr.appendChild(td4);

    tr.appendChild(this.MakeButtonsCell());
    return tr;
  }

  ToEditView(index, obj) {
    var tr = MakeGridRow(index);

    // Rights
    var td1 = document.createElement("td");
    td1.className = "Centered";
    this.IsActiveInput = CreateCheckBox("IsActive", this.IsActive);
    td1.appendChild(this.IsActiveInput);
    tr.appendChild(td1);

    var td2 = document.createElement("td");
    td2.innerHTML = this.Type;
    tr.appendChild(td2);

    var td3 = document.createElement("td");

    this.ExecutionDateInput = document.createElement("input");
    this.ExecutionDateInput.value = this.ExecutionDate;
    td3.appendChild(this.ExecutionDateInput);
    new DatePicker(this.ExecutionDateInput, 1);

    tr.appendChild(td3);

    var td4 = document.createElement("td");
    this.PeriodicityInput = document.createElement("input");
    this.PeriodicityInput.value = this.Periodicity;
    this.PeriodicityInput.className = "Wide";
    td4.appendChild(this.PeriodicityInput);
    tr.appendChild(td4);

    tr.appendChild(this.MakeButtonsCell());
    return tr;
  }
}