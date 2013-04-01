//4.1
/*
	Scheduled Tasks management
*/

function ScheduledTasks() {
	this.fields = ["SCHEDULED_TASK_ID", "TYPE", "EXECUTION_DATE", "PERIODICITY", "IS_ACTIVE", "inactivated", "status", "unban", "expired_sessions", "ratings"];
	this.ServicePath = servicesPath + "scheduled_tasks.service.php";
	this.Template = "scheduled_tasks";
	this.ClassName = "ScheduledTasks";
	this.GridId = "ScheduledTasksGrid";
	this.Columns = 5;
};

ScheduledTasks.prototype = new EditablePagedGrid();

ScheduledTasks.prototype.BaseBind = function() {};

ScheduledTasks.prototype.InitPager = function() {
	this.Pager = new Pager(this.Inputs[this.PagerId], function(){this.Tab.ScheduledTasks.SwitchPage()}, this.PerPage);
};

ScheduledTasks.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind(obj.data, obj.Total);
	}
};

ScheduledTasks.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);
	this.GroupSelfAssign(["RefreshScheduledTasks"]);

	// System log checkboxes
	BindEnterTo(this.Inputs["status"], this.Inputs["RefreshScheduledTasks"]);
	BindEnterTo(this.Inputs["unban"], this.Inputs["RefreshScheduledTasks"]);
	BindEnterTo(this.Inputs["expired_sessions"], this.Inputs["RefreshScheduledTasks"]);
	BindEnterTo(this.Inputs["ratings"], this.Inputs["RefreshScheduledTasks"]);
	BindEnterTo(this.Inputs["inactivated"], this.Inputs["RefreshScheduledTasks"]);
};

/* Status Data Transfer Object */

function stdto(id, rights, color, title) {
	this.fields = ["Id", "Type", "ExecutionDate", "Periodicity", "IsActive"];
	this.Init(arguments);
};

stdto.prototype = new EditableDTO();

stdto.prototype.ToShowView = function(index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
		td1.className = "Centered";
		td1.appendChild(CreateBooleanImage(this.IsActive));
	tr.appendChild(td1);

	var td2 = d.createElement("td");
			td2.innerHTML = this.Type;
	tr.appendChild(td2);

	var td3 = d.createElement("td");
			td3.innerHTML = this.ExecutionDate;
	tr.appendChild(td3);

	var td4 = d.createElement("td");
			td4.innerHTML = this.Periodicity;
	tr.appendChild(td4);

	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

stdto.prototype.ToEditView = function(index, obj) {
	var tr = MakeGridRow(index);

	// Rights
	var td1 = d.createElement("td");
		td1.className = "Centered";
		this.IsActiveInput = CreateCheckBox("IsActive", this.IsActive);
		td1.appendChild(this.IsActiveInput);
	tr.appendChild(td1);

	var td2 = d.createElement("td");
			td2.innerHTML = this.Type;
	tr.appendChild(td2);

	var td3 = d.createElement("td");

		this.ExecutionDateInput = d.createElement("input");
		this.ExecutionDateInput.value = this.ExecutionDate;
		td3.appendChild(this.ExecutionDateInput);
		new DatePicker(this.ExecutionDateInput, 1);

	tr.appendChild(td3);

	var td4 = d.createElement("td");
			this.PeriodicityInput = d.createElement("input");
			this.PeriodicityInput.value = this.Periodicity;
			this.PeriodicityInput.className = "Wide";
			td4.appendChild(this.PeriodicityInput);
	tr.appendChild(td4);

	tr.appendChild(this.MakeButtonsCell());
	return tr;
};
