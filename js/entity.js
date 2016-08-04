import _ from 'lodash';

export class Entity {
  constructor() {
    this.ascii = _.reduce(
      "абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ".split(),
      (result, char, index) => {
        result[char] = index + 1;
        return result;
      },
      {}
    );
  }

  checkSum(plainObject) {
    if (!_.isPlainObject(plainObject) || _.isEmpty(plainObject)) {
      return 0;
    }
    return _.sum(_.map(
      ('' + plainObject).split(),
      (char) => this.ascii[char] || char.charCodeAt(0) > 255 ? 1 : char.charCodeAt(0) 
    ));
  }
}