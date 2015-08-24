//4.1
/*
    Performs single async request with given set of parameters
*/

function Requestor(service, obj) {
    this.ServicePath = service;
    this.obj = obj;
};

Requestor.prototype.request = function(names, values) {
    var s = new ParamsBuilder()
        .add('no-cache', _.random(1000, 9999));
    names.forEach(function(name, index) {
        s.add(name, values[index]);
    });
    sendRequest(this.ServicePath, this.requestCallback, s.build(), this);
};

Requestor.prototype.callback = function() {};

Requestor.prototype.Basecallback = function(req) {
    var obj = this.obj;
    var tabObject = this.obj.Tab;
    tabObject.Alerts.Clear();
    eval(req);
    this.req = req;
    this.callback(this);
};

Requestor.prototype.requestCallback = function(req, sender) {
    sender.Basecallback(req);
};


/*
    Delayed Requestor class.
    Performs delayed async request by reaction to user-entered "needle"
*/

function delayedRequestor(obj, input, request_method) {
    this.Timer = "";
    this.LastValue = "";
    this.obj = obj;
    input.delayedRequestor = this;
    this.Input = input;
    this.requestMethod = request_method;

    this.Submitter = "";    // Treating enter button

    input.onkeypress = function(e){GetData(this,e)};
    input.onchange = function(){GetData(this)};
};

delayedRequestor.prototype.request = function() {
    var obj = this.obj;
    if (obj) {
        params = this.GetParams();
        if (params == this.LastValue) {
            return;
        }
        this.LastValue = params;
        if (this.requestMethod) {
            this.requestMethod(this.Input);
        } else {
            obj.request(params);
        }
    }
};

// To be overridden
delayedRequestor.prototype.GetParams = function() {
    return new ParamsBuilder()
        .add(this.Input.name, this.Input.value)
        .build();
};

function GetData(input, e) {
    if (!input || !input.delayedRequestor) {
        return;
    }

    if (e && input.delayedRequestor.Submitter && EnterHandler(e, input.delayedRequestor)) {
        return;
    }

    if (input.delayedRequestor.Timer) {
        clearTimeout(input.delayedRequestor.Timer);
    }
    input.delayedRequestor.Timer = setTimeout(function(){input.delayedRequestor.request()}, 500);
};

