//2.2
/*
	Collection of entities (users, rooms etc.)
*/

function Collection() {
	this.Base = new Array();
};

Collection.prototype.Get = function(id) {
	if (this.Base['_'+id]) {
		return this.Base['_'+id];
	}
	return false;
};
	
Collection.prototype.Add = function(e) {
	this.Base['_'+e.Id] = e;
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

Collection.prototype.ToString = function() {
	var i = 0;
	var s = '';
	for (var id in this.Base) {
		if (id && this.Base[id].ToString) {
			s += this.Base[id].ToString(i++);
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