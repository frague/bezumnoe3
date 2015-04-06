//2.2
/*
	Helper methods to work with dates.
*/

var dayMsec = 1000 * 60 * 60 * 24;
var months = new Array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
var monthsNames = new Array("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");

function ParseDate(str) {
	var datePat = /^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})(\s(\d{1,2}):(\d{1,2})){0,1}/;
	var matchArray = str.match(datePat);

	var date = new Date(); 
	date.IsEmpty = false;
	if (matchArray == null) {
		date.IsEmpty = true;
		return date;
	}

	var year = matchArray[1];
	var month = matchArray[3];
	var day = matchArray[5];

	var hours = date.getHours();
	var minutes = date.getMinutes();
	if (matchArray[6]) {
		hours = matchArray[7];
		minutes = matchArray[8];
	}

	return new Date(year, month - 1, day, hours, minutes, 0);
};

Date.prototype.ToString = function(add_time) {
	var result = this.getFullYear() + "-" + TwoDigits(1 + this.getMonth()) + "-" + TwoDigits(this.getDate());
	if (add_time) {
		 result += " " + TwoDigits(this.getHours()) + ":" + TwoDigits(this.getMinutes());
	}
	return result;
};

Date.prototype.ToPrintableString = function(add_time) {
	var result = this.getDate() + " " + monthsNames[this.getMonth()] + " " + this.getFullYear();
	if (add_time) {
		 result += ",	" + this.Time();
	}
	return result;
};

Date.prototype.Time = function() {
	return TwoDigits(this.getHours()) + ":" + TwoDigits(this.getMinutes());
};