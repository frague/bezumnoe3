import _ from 'lodash';

//2.6
/*
  Collection of entities (users, rooms etc.)
*/

export class Collection {
  constructor() {
    this.Base = {};
    this.LastId = null;
  }

  Get(id) {
    if (this.Base['_' + id]) {
      return this.Base['_' + id];
    }
    return null;
  }

  Add(e) {
    this.Base['_' + e.Id] = e;
    this.LastId = e.Id;
  }

  BulkAdd(elements) {
    elements.forEach(function(e) {
      if (!e.Id) e.Id = _.uniqueId();
      this.Add(e);
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

  invoke(method, holder) {
    var index = 0;
    return _.reduce(
      this.Base,
      (result, element) => {
        if (element[method]) {
          if (holder) {
            element[method](holder, index++);
          } else {
            result += element[method](index++);
          }
        }
        return result;
      },
      ''
    );
  }

  ToString(holder) {
    return this.invoke('ToString', holder);
  }

  Gather(holder) {
    return this.invoke('Gather', holder);
  }

  render(holder) {
    return this.invoke('render', holder);
  }
}