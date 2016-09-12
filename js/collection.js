import _ from 'lodash';

/*
  Collection of entities (users, rooms etc.)
*/

export class Collection {
  constructor(initialElements = []) {
    this.base = {};
    this.lastId = null;
    this.bulkAdd(initialElements);
  }

  get(id) {
    return _.get(this.base, '_' + id, null);
  }

  add(element) {
    let id = element.id || element.props.id;
    if (!id) id = _.uniqueId();
    this.base['_' + id] = element;
    this.lastId = id;
  }

  bulkAdd(elements) {
    _.each(elements, (element) => {
      this.add(element);
    })
  }

  delete(id) {
    if (this.base['_' + id]) {
      delete this.base['_' + id];
    }
  }

  clear() {
    this.base = {};
  }

  count() {
    return _.size(this.base);
  }

  invoke(method, holder, ...params) {
    var index = 0;
    return _.reduce(
      this.base,
      (result, element) => {
        if (element[method]) {
          if (holder) {
            element[method](holder, index++, ...params);
          } else {
            result += element[method](index++, ...params);
          }
        }
        return result;
      },
      ''
    );
  }

  toString(holder, ...params) {
    return this.invoke('ToString', holder, ...params);
  }

  gather(holder, ...params) {
    return this.invoke('Gather', holder, ...params);
  }

  render() {
    return _.map(this.base, (element) => element.render());
  }
}