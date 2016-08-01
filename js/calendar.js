/*
  Date picker fuctionality.
*/

class Calendar {
  constructor(holder, date, with_time) {
    this.Holder = holder;
    this.WithTime = with_time;
    this.callback = "";
    this.Date = date ? date : new Date();
    this.SelectDate();
  }

  SelectDate() {
    this.SelectedDate = new Date(
      this.Date.getFullYear(), this.Date.getMonth(), this.Date.getDate()
    );
  }

  MakeHeaderLink(is_selectable, holder) {
    if (holder.hasChildNodes()) {
      holder.removeChild(holder.firstChild);
    }
    var result;
    if (is_selectable) {
      var select = document.createElement("select");
      select.Calendar = this;
      result = select;
    } else {
      var a = document.createElement("a");
      a.href = voidLink;
      a.Calendar = this;
      result = a;
    }
    holder.appendChild(result);
    return result;
  }

  MakeMonth(is_selectable) {
    var el = this.MakeHeaderLink(is_selectable, this.MonthHolder);
    this.MonthSelect = is_selectable ? el : "";
    if (is_selectable) {
      el.onchange = () => {this.Calendar.UpdateMonth()};
      el.onblur = el.onchange;
      el.className = "Right";

      var month = this.Date.getMonth();
      for (var i = 0, l = months.length; i < l; i++) {
        var o = document.createElement("option");
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
      el.onclick = () => {this.Calendar.MakeMonth(true)};
    }
  }

  MakeYear(is_selectable) {
    var el = this.MakeHeaderLink(is_selectable, this.YearHolder);
    this.YearSelect = is_selectable ? el : "";
    var year = this.Date.getFullYear();

    if (is_selectable) {
      el.onchange = () => {this.Calendar.UpdateYear()};
      el.onblur = el.onchange;

      var k = 0;
      for (var i = year - 20; i < year + 20; i++) {
        var o = document.createElement("option");
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
      el.onclick = () => {this.Calendar.MakeYear(true)};
    }
  }

  MakeHours(is_selectable) {
    var el = this.MakeHeaderLink(is_selectable, this.HoursHolder);
    this.HoursSelect = is_selectable ? el : "";
    var hours = this.Date.getHours();

    if (is_selectable) {
      el.onchange = () => {this.Calendar.UpdateTime()};
      el.onblur = el.onchange;

      var k = 0;
      for (var i = 0; i < 24; i++) {
        var o = document.createElement("option");
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
      el.onclick = () => {this.Calendar.MakeHours(true)};
    }
  }

  MakeMinutes(is_selectable) {
    var el = this.MakeHeaderLink(is_selectable, this.MinutesHolder);
    this.MinutesSelect = is_selectable ? el : "";
    var minutes = this.Date.getMinutes();

    if (is_selectable) {
      el.onchange = () => {this.Calendar.UpdateTime()};
      el.onblur = el.onchange;

      var k = 0;
      for (var i = 0; i < 60; i++) {
        var o = document.createElement("option");
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
      el.onclick = () => {this.Calendar.MakeMinutes(true)};
    }
  }

  UpdateMonth() {
    if (this.MonthSelect) {
      this.Date.setMonth(this.MonthSelect.value);
      this.Print();
    } else {
      this.MakeMonth(false);
    }
  }

  UpdateYear() {
    if (this.YearSelect) {
      this.Date.setYear(this.YearSelect.value);
      this.Print();
    } else {
      this.MakeYear(false);
    }
  }

  UpdateTime(flag) {
    if (this.HoursSelect) {
      this.Date.setHours(this.HoursSelect.value);
      this.Print();
    } else if (this.MinutesSelect) {
      this.Date.setMinutes(this.MinutesSelect.value);
      this.Print();
    }
  }

  MakeHeader() {
    var tr = document.createElement("tr");
    var th = document.createElement("th");
    th.colSpan = 7;

    this.MonthHolder = document.createElement("span");
    this.MakeMonth(false);
    th.appendChild(this.MonthHolder);

    th.appendChild(document.createTextNode(" "));

    this.YearHolder = document.createElement("span");
    this.MakeYear(false);
    th.appendChild(this.YearHolder);

    tr.appendChild(th);
    return tr;
  }

  MakeTimePicker() {
    var tr = document.createElement("tr");
    var th = document.createElement("th");
    th.colSpan = 7;

    th.appendChild(document.createTextNode("Время: "));

    this.HoursHolder = document.createElement("span");
    this.MakeHours(false);
    th.appendChild(this.HoursHolder);

    th.appendChild(document.createTextNode(":"));

    this.MinutesHolder = document.createElement("span");
    this.MakeMinutes(false);
    th.appendChild(this.MinutesHolder);

    tr.appendChild(th);
    return tr;
  }

  Clear() {
    this.Holder.innerHTML = "";
  }

  MakeLink(day, check_day) {
    var a = document.createElement("a");
    a.innerHTML = day;
    a.Calendar = this;
    a.onclick = () => {this.Calendar.Select(this)};
    if (day == check_day) {
      a.className = "Selected";
    }
    return a;
  }

  Print() {
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

    var t = document.createElement("table");
    var tb = document.createElement("tbody");
    
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
      var tr = document.createElement("tr");
      for (var k = 0; k < 7; k++) {
        var td = document.createElement("td");
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
  }

  Select(a) {
    this.Date.setDate(a.innerHTML);
    this.SelectDate();
    this.Print();

    if (this.callback) {
      this.callback();
    }
  }
}

/*-------------------------------------------------*/

class DatePicker {
  constructor(input, with_time) {
    if (!input) {
      return;
    }
    this.Visible = true;
    this.WithTime = with_time;
    this.Input = input;
    if (!input.type) {
      this.Input = $(getElement(input))[0];
    }
    if (this.Input) {
      this.Input.className = (with_time ? "DateTime" : "Date");
      /*this.Input.type = with_time ? "datetime" : "date"; */

      this.Holder = document.createElement("div");
      this.Holder.className = "DatePicker";
      this.switchVisibility(false);

      this.Calendar = new Calendar(this.Holder, "", this.WithTime);

      this.Calendar.Picker = this;
      this.Calendar.callback = () => {this.Picker.Selected()};

      insertAfter(this.Holder, this.Input);
      insertAfter(
        MakeButton("SwitchDatePicker(this)", "icons/calendar.gif", this, "PickerButton", "Выбрать дату"),
        this.Input
      );
    }
  }

  Init() {
    this.Calendar.Date = ParseDate(this.Input.value);
    this.Calendar.SelectDate();
    this.Calendar.Print();
  }

  switchVisibility() {
    this.Visible = !this.Visible;
    displayElement(this.Holder, this.Visible);
    if (this.Visible) {
      this.Init();
    }
  }

  Selected() {
    this.Visible = true;
    this.switchVisibility();
    var date = this.Calendar.Date;
    this.Input.value = date.ToString(this.WithTime);
  }
};

function SwitchDatePicker(a) {
  if (a && a.obj) {
    a.obj.switchVisibility();
  }
};

