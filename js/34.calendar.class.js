//3.6
/*
	Date picker fuctionality.
*/

function Calendar(holder, date, with_time) {
	this.Holder = holder;
	this.WithTime = with_time;
	this.Callback = "";
	this.Date = date ? date : new Date();
	this.SelectDate();
};

Calendar.prototype.SelectDate = function() {
	this.SelectedDate = new Date(this.Date.getFullYear(), this.Date.getMonth(), this.Date.getDate());
};

Calendar.prototype.MakeHeaderLink = function(is_selectable, holder) {
	if (holder.hasChildNodes()) {
		holder.removeChild(holder.firstChild);
	}
	var result;
	if (is_selectable) {
		var select = d.createElement("select");
		select.Calendar = this;
		result = select;
	} else {
		var a = d.createElement("a");
		a.href = voidLink;
		a.Calendar = this;
		result = a;
	}
	holder.appendChild(result);
	return result;
};

Calendar.prototype.MakeMonth = function(is_selectable) {
	var el = this.MakeHeaderLink(is_selectable, this.MonthHolder);
	this.MonthSelect = is_selectable ? el : "";
	if (is_selectable) {
		el.onchange = function(){this.Calendar.UpdateMonth()};
		el.onblur = el.onchange;
		el.className = "Right";

		var month = this.Date.getMonth();
		for (var i = 0, l = months.length; i < l; i++) {
			var o = d.createElement("option");
			o.text = months[i];
			o.value = i;
			if (i == month) {
				o.selected = true;
			}
			el.options.add(o, i);
		}
		el.focus();
	} else {
		el.innerHTML = months[this.Date.getMonth()];
		el.onclick = function(){this.Calendar.MakeMonth(true)}
	}
};

Calendar.prototype.MakeYear = function(is_selectable) {
	var el = this.MakeHeaderLink(is_selectable, this.YearHolder);
	this.YearSelect = is_selectable ? el : "";
	var year = this.Date.getFullYear();

	if (is_selectable) {
		el.onchange = function(){this.Calendar.UpdateYear()};
		el.onblur = el.onchange;

		var k = 0;
		for (var i = year - 20; i < year + 20; i++) {
			var o = d.createElement("option");
			o.text = i;
			o.value = i;
			if (i == year) {
				o.selected = true;
			}
			el.options.add(o, k++);
		}
		el.focus();
	} else {
		el.innerHTML = year;
		el.onclick = function(){this.Calendar.MakeYear(true)}
	}
};

Calendar.prototype.MakeHours = function(is_selectable) {
	var el = this.MakeHeaderLink(is_selectable, this.HoursHolder);
	this.HoursSelect = is_selectable ? el : "";
	var hours = this.Date.getHours();

	if (is_selectable) {
		el.onchange = function(){this.Calendar.UpdateTime()};
		el.onblur = el.onchange;

		var k = 0;
		for (var i = 0; i < 24; i++) {
			var o = d.createElement("option");
			o.text = TwoDigits(i);
			o.value = i;
			if (i == hours) {
				o.selected = true;
			}
			el.options.add(o, k++);
		}
		el.focus();
	} else {
		el.innerHTML = TwoDigits(hours);
		el.onclick = function(){this.Calendar.MakeHours(true)}
	}
};

Calendar.prototype.MakeMinutes = function(is_selectable) {
	var el = this.MakeHeaderLink(is_selectable, this.MinutesHolder);
	this.MinutesSelect = is_selectable ? el : "";
	var minutes = this.Date.getMinutes();

	if (is_selectable) {
		el.onchange = function(){this.Calendar.UpdateTime()};
		el.onblur = el.onchange;

		var k = 0;
		for (var i = 0; i < 60; i++) {
			var o = d.createElement("option");
			o.text = TwoDigits(i);
			o.value = i;
			if (i == minutes) {
				o.selected = true;
			}
			el.options.add(o, k++);
		}
		el.focus();
	} else {
		el.innerHTML = TwoDigits(minutes);
		el.onclick = function(){this.Calendar.MakeMinutes(true)}
	}
};

Calendar.prototype.UpdateMonth = function() {
	if (this.MonthSelect) {
		this.Date.setMonth(this.MonthSelect.value);
		this.Print();
	} else {
		this.MakeMonth(false);
	}
};

Calendar.prototype.UpdateYear = function() {
	if (this.YearSelect) {
		this.Date.setYear(this.YearSelect.value);
		this.Print();
	} else {
		this.MakeYear(false);
	}
};

Calendar.prototype.UpdateTime = function(flag) {
	if (this.HoursSelect) {
		this.Date.setHours(this.HoursSelect.value);
		this.Print();
	} else if (this.MinutesSelect) {
		this.Date.setMinutes(this.MinutesSelect.value);
		this.Print();
	}
};

Calendar.prototype.MakeHeader = function() {
	var tr = d.createElement("tr");
	var th = d.createElement("th");
	th.colSpan = 7;

	this.MonthHolder = d.createElement("span");
	this.MakeMonth(false);
	th.appendChild(this.MonthHolder);

	th.appendChild(d.createTextNode(" "));

	this.YearHolder = d.createElement("span");
	this.MakeYear(false);
	th.appendChild(this.YearHolder);

	tr.appendChild(th);
	return tr;
};

Calendar.prototype.MakeTimePicker = function() {
	var tr = d.createElement("tr");
	var th = d.createElement("th");
	th.colSpan = 7;

	th.appendChild(d.createTextNode("Время: "));

	this.HoursHolder = d.createElement("span");
	this.MakeHours(false);
	th.appendChild(this.HoursHolder);

	th.appendChild(d.createTextNode(":"));

	this.MinutesHolder = d.createElement("span");
	this.MakeMinutes(false);
	th.appendChild(this.MinutesHolder);

	tr.appendChild(th);
	return tr;
};

Calendar.prototype.Clear = function() {
	this.Holder.innerHTML = "";
};

Calendar.prototype.MakeLink = function(day, check_day) {
	var a = d.createElement("a");
	a.href = voidLink;
	a.innerHTML = day;
	a.Calendar = this;
	a.onclick = function(){this.Calendar.Select(this)};
	if (day == check_day) {
		a.className = "Selected";
	}
	return a;
};

Calendar.prototype.Print = function() {
	/* Date calculations */
	var a = new Date(this.Date.getFullYear(), this.Date.getMonth(), 1);	
	var offset = a.getDay() - 1;
	if (offset < 0) {
		offset = 6;
	}
	var days = 31;
	if (this.Date.getMonth() != 11) {
		var b = new Date(this.Date.getFullYear(), this.Date.getMonth() + 1, 1);	
		days = Math.round((b.getTime() - a.getTime()) / dayMsec);
	}

	var t = d.createElement("table");
	var tb = d.createElement("tbody");
	
	/* Header */
	tb.appendChild(this.MakeHeader());

	/* Body */
	var i = 0;
	var day = 1;
	var check_day = 0;
	if (this.Date.getMonth() == this.SelectedDate.getMonth() && this.Date.getYear() == this.SelectedDate.getYear()) {
		check_day = this.SelectedDate.getDate();
	}

	while (day <= days) {
		var tr = d.createElement("tr");
		for (var k = 0; k < 7; k++) {
			var td = d.createElement("td");
			if (i >= offset && day <= days) {
				td.appendChild(this.MakeLink(day, check_day));
				day++;
			} else {
				td.innerHTML = "&nbsp;";
			}
			i++;
			tr.appendChild(td);
		}
		tb.appendChild(tr);
	}

	/* Time */
	if (this.WithTime) {
		tb.appendChild(this.MakeTimePicker());
	}

	this.Holder.innerHTML = "";
	t.appendChild(tb);
	t.className = "Calendar";
	this.Holder.appendChild(t);

};

Calendar.prototype.Select = function(a) {
	this.Date.setDate(a.innerHTML);
	this.SelectDate();
	this.Print();

	if (this.Callback) {
		this.Callback();
	}
};

/*-------------------------------------------------*/

function DatePicker(input, with_time) {
	this.Visible = true;
	this.WithTime = with_time;
	this.Input = input;
	if (!input.type) {
		this.Input = $(input);
	}
	if (this.Input) {
		this.Input.className = (with_time ? "DateTime" : "Date");

		this.Holder = d.createElement("div");
		this.Holder.className = "DatePicker";
		this.SwitchVisibility(false);

		this.Calendar = new Calendar(this.Holder, "", this.WithTime);

		this.Calendar.Picker = this;
		this.Calendar.Callback = function() {this.Picker.Selected()};

		insertAfter(this.Holder, this.Input);
		insertAfter(MakeButton("SwitchDatePicker(this)", "icons/calendar.gif", this, "PickerButton"), this.Input);
	}
};

DatePicker.prototype.Init = function() {
	this.Calendar.Date = ParseDate(this.Input.value);
    this.Calendar.SelectDate();
	this.Calendar.Print();
};

DatePicker.prototype.SwitchVisibility = function() {
	this.Visible = !this.Visible;
	DisplayElement(this.Holder, this.Visible);
	if (this.Visible) {
		this.Init();
	}
};

DatePicker.prototype.Selected = function() {
	this.Visible = true;
	this.SwitchVisibility();
	var date = this.Calendar.Date;
	this.Input.value = date.ToString(this.WithTime);
};

function SwitchDatePicker(a) {
	if (a && a.obj) {
		a.obj.SwitchVisibility();
	}
};

