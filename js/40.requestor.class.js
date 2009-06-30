//2.2
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
	sendRequest(this.ServicePath, this.RequestCallback, params, this);
};

Requestor.prototype.Callback = function() {};

Requestor.prototype.BaseCallback = function(req) {
	var obj = this.obj;
	var tabObject = this.obj.Tab;
	tabObject.Alerts.Clear();
	eval(req.responseText);
	this.req = req;
	this.Callback(this);
};

Requestor.prototype.RequestCallback = function(req, sender) {
	sender.BaseCallback(req);
};
