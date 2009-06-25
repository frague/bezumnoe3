//1.0
/*
	Performs single async request with given set of parameters
*/

function Requestor(service, obj) {
	this.ServicePath = service;
	this.obj = obj;
};

Requestor.prototype.Request = function(names, values) {
	var params = MakeParametersPair("no-cache", Math.random(1000));
	for (var i = 0, l = names.length; i < l; i++) {
		params += MakeParametersPair(names[i], values[i]);
	}
	sendRequest(this.ServicePath, this.RequestCallback, params, this.obj);
};

Requestor.prototype.RequestCallback = function(req, obj) {
	if (obj.Tab) {
		var tabObject = obj.Tab;
		tabObject.Alerts.Clear();
		eval(req.responseText);
	}
};
