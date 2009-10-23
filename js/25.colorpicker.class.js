//2.3
/*
	Allows color selection with mouse click
*/

var bw = new Array("0","2","4","6","8","A","C","D","E","F");
var ones = new Array("19","33","4C","66","99", "99", "99","B2","CC","E5");
var twos = new Array("33","66","99","CC","E5", "FF", "33","66","99","CC");
var line = new Array("_002","_012","_022","_021","_020","_120","_220","_210","_200","_201","_202","_102");

function ColorPicker(input) {
	this.Visible = false;
	if (input.tagName) {
		this.Input = input;
	} else {
		this.Input = $(input);
	}
	if (this.Input) {
		this.Input.className = "Color";
		this.Table = d.createElement("table");
		DisplayElement(this.Table, false);
		var t = d.createElement("tbody");
		this.Table.appendChild(t);
		this.Table.className = "ColorPicker";
		this.Table.obj = this;
		this.Table.onclick = function() {this.obj.ColorSelected()};
		//this.Table.onmouseout = function() {this.obj.SwitchVisibility()};

		for (var i = 0, l = bw.length; i < l; i++) {
			this.MakeRow(i);
		}
		insertAfter(this.Table, this.Input);
		insertAfter(MakeButton("SwitchPicker(this)", "icons/palette.gif", this, "PickerButton", "Выбрать цвет"), this.Input);
	}
};

ColorPicker.prototype.MakeCell = function(row, i, color) {
	var cell = row.insertCell(i);
	cell.style.backgroundColor = color;
	cell.Color = color;
	cell.onclick = function() {pc(this)};
};

ColorPicker.prototype.MakeRow = function(index) {
	var row = this.Table.insertRow(index);
	this.MakeCell(row, 0, "#" + bw[index] + bw[index] + bw[index] + bw[index] + bw[index] + bw[index]);

	var zero = "00";
	if (index > 5) {
		zero = "FF";
	}
	for (var i = 0, l = line.length; i < l; i++) {
		var color = "#";
		var rgb = line[i];
		for (var j = 1; j <=3; j++) {
			var comp = (zero == "FF" ? 2 - 1 * rgb.charAt(j) : 1 * rgb.charAt(j));
			color += (comp ? (comp == 1 ? ones[index] : twos[index]) : zero);
		}
		this.MakeCell(row, i + 1, color);
	}
};

ColorPicker.prototype.SwitchVisibility = function() {
	this.Visible = !this.Visible;
	DisplayElement(this.Table, this.Visible);
};

ColorPicker.prototype.ColorSelected = function() {
	this.Input.value = this.Table.SelectedColor;
	this.SwitchVisibility();
	if (top.UpdateFontView) {
		UpdateFontView();
	}
};

function SwitchPicker(a) {
	if (a && a.obj) {
		a.obj.SwitchVisibility();
	}
};

function pc(td) {
	var table = td.parentNode.parentNode.parentNode;
	table.SelectedColor = td.Color;
};