//4.1
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
    eval(req);
    this.req = req;
    this.Callback(this);
};

Requestor.prototype.RequestCallback = function(req, sender) {
    sender.BaseCallback(req);
};


/*
    Delayed Requestor class.
    Performs delayed async request by reaction to user-entered "needle"
*/

function DelayedRequestor(obj, input, request_method) {
    this.Timer = "";
    this.LastValue = "";
    this.obj = obj;
    input.DelayedRequestor = this;
    this.Input = input;
    this.RequestMethod = request_method;

    this.Submitter = "";    // Treating enter button

    input.onkeypress = function(e){GetData(this,e)};
    input.onchange = function(){GetData(this)};
};

DelayedRequestor.prototype.Request = function() {
    var obj = this.obj;
    if (obj) {
        params = this.GetParams();
        if (params == this.LastValue) {
            return;
        }
        this.LastValue = params;
        if (this.RequestMethod) {
            this.RequestMethod(this.Input);
        } else {
            obj.Request(params);
        }
    }
};

// To be overridden
DelayedRequestor.prototype.GetParams = function() {return MakeParametersPair(this.Input.name, this.Input.value);};

function GetData(input, e) {
    if (!input || !input.DelayedRequestor) {
        return;
    }

    if (e && input.DelayedRequestor.Submitter && EnterHandler(e, input.DelayedRequestor)) {
        return;
    }

    if (input.DelayedRequestor.Timer) {
        clearTimeout(input.DelayedRequestor.Timer);
    }
    input.DelayedRequestor.Timer = setTimeout(function(){input.DelayedRequestor.Request()}, 500);
};

