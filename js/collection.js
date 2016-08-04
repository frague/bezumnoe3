import _ from 'lodash';

/*
  Collection of entities (users, rooms etc.)
*/

export class Collection {
  constructor() {
    this.Base = {};
    this.LastId = null;
  }

  Get(id) {
    return _.find(this.Base, {Id: '_' + id});
  }

  Add(element) {
    if (!element.Id) element.Id = _.uniqueId();
    this.Base['_' + element.Id] = element;
    this.LastId = element.Id;
  }

  BulkAdd(elements) {
    elements.forEach((element) => {
      this.Add(element);
    })
  }

  Delete(id) {
    if (this.Base['_' + id]) delete this.Base['_' + id];
  }

  Clear() {
    this.Base = {};
  }

  Count() {
    return _.size(this.Base);
  }

  invoke(method, holder, ...params) {
    var index = 0;
    return _.reduce(
      this.Base,
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

  ToString(holder, ...params) {
    return this.invoke('ToString', holder, ...params);
  }

  Gather(holder, ...params) {
    return this.invoke('Gather', holder, ...params);
  }

  render(holder, ...params) {
    return this.invoke('render', holder, ...params);
  }
}