//2.6
/*
  Collection of entities (users, rooms etc.)
*/

function Collection() {
  this.Base = {};
  this.LastId = null;
};

Collection.prototype.Get = function(id) {
  if (this.Base['_' + id]) {
    return this.Base['_' + id];
  }
  return null;
};

Collection.prototype.Add = function(e) {
  this.Base['_' + e.Id] = e;
  this.LastId = e.Id;
};

Collection.prototype.BulkAdd = function(elements) {
  elements.forEach(function(e) {
    if (!e.Id) e.Id = _.uniqueId();
    this.Add(e);
  });
};

Collection.prototype.Delete = function(id) {
  if (this.Base['_' + id]) delete this.Base['_' + id];
};

Collection.prototype.Clear = function() {
  this.Base = {};
};

Collection.prototype.Count = function() {
  return _.size(this.Base);
};

Collection.prototype.invoke = function(method, holder) {
  var index = 0;
  return _.reduce(
    this.Base,
    function (result, element) {
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
};

Collection.prototype.ToString = function(holder) {
  return this.invoke('ToString', holder);
};

Collection.prototype.Gather = function(holder) {
  return this.invoke('Gather', holder);
};

Collection.prototype.render = function(holder) {
  return this.invoke('render', holder);
};
