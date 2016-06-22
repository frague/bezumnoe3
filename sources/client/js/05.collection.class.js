//2.6
/*
    Collection of entities (users, rooms etc.)
*/

function Collection() {
    this.Base = new Array();
    this.LastId = 0;
};

Collection.prototype.Get = function(id) {
    if (this.Base['_'+id]) {
        return this.Base['_'+id];
    }
    return false;
};

Collection.prototype.Add = function(e) {
    this.Base['_'+e.Id] = e;
    this.LastId = e.Id;
};

Collection.prototype.BulkAdd = function(arr) {
    for (var i = 0, l = arr.length; i < l; i++) {
        el = arr[i];
        if (!el.Id) {
            el.Id = i + 1;
        }
        this.Add(el)
    }
};

Collection.prototype.Delete = function(id) {
    id = '_'+id;
    if (this.Base[id]) {
        var a = new Array();
        for (var cid in this.Base) {
            if (cid != id) {
                a[cid] = this.Base[cid];
            }
        }
        this.Base = a;
    }
};

Collection.prototype.Clear = function() {
    this.Base = new Array();
};

Collection.prototype.Count = function() {
    var l = 0;
    for (var k in this.Base) {
        l += k ? 1 : 0;
    }
    return l;
};

Collection.prototype.ToString = function(holder) {
    var i = 0;
    var s = '';
    for (var id in this.Base) {
        if (id && this.Base[id].ToString) {
            if (holder) {
                this.Base[id].ToString(holder, i++);
            } else {
                s += this.Base[id].ToString(i++);
            }
        }
    }
    return s;
};

Collection.prototype.Gather = function(holder) {
    var i = 0;
    var s = '';
    for (var id in this.Base) {
        if (id && this.Base[id].Gather) {
            if (holder) {
                this.Base[id].Gather(holder);
            } else {
                s += this.Base[id].Gather(i++);
            }
        }
    }
    return s;
};
