import {utils} from './utils';
/*
  Helper methods to work with dates.
*/

export var dateHelper = {
  dayMsec: 1000 * 60 * 60 * 24,
  months: ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"],
  monthsNames: ["января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря"],

  ParseDate(str) {
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
  },

  ToString(date, addTime) {
    var result = date.getFullYear() + "-" + utils.twoDigits(1 + date.getMonth()) + "-" + utils.twoDigits(date.getDate());
    if (addTime) {
       result += " " + utils.twoDigits(date.getHours()) + ":" + utils.twoDigits(date.getMinutes());
    }
    return result;
  },

  ToPrintableString(date, addTime) {
    var result = [date.getDate(), monthsNames[this.getMonth()], date.getFullYear()];
    if (addTime) {
      result.push(',', this.Time(date));
    }
    return result.join(' ');
  },

  time(date) {
    return utils.twoDigits(date.getHours()) + ":" + utils.twoDigits(date.getMinutes());
  }
}
