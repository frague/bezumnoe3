/*
  Performs single async request with given set of parameters
*/

class Requestor {
  constructor(service, obj) {
    this.ServicePath = service;
    this.obj = obj;
  }

  Request(names, values) {
    var params = MakeParametersPair("no-cache", Math.random(1000));
    for (var i = 0, l = names.length; i < l; i++) {
      params += MakeParametersPair(names[i], values[i]);
    }
    sendRequest(this.ServicePath, this.RequestCallback, params, this);
  }

  Callback() {};

  BaseCallback(req) {
    var obj = this.obj;
    var tabObject = this.obj.Tab;
    tabObject.Alerts.Clear();
    eval(req);
    this.req = req;
    this.Callback(this);
  }

  RequestCallback(req, sender) {
    sender.BaseCallback(req);
  }
};

/*
  Delayed Requestor class.
  Performs delayed async request by reaction to user-entered "needle"
*/

class DelayedRequestor {
  constructor(obj, input, request_method) {
    this.Timer = "";
    this.LastValue = "";
    this.obj = obj;
    input.DelayedRequestor = this;
    this.Input = input;
    this.RequestMethod = request_method;

    this.Submitter = "";    // Treating enter button

    input.onkeypress = (e) => GetData(this, e);
    input.onchange = () => GetData(this);
  }

  Request() {
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
  }

  GetParams() {
    return MakeParametersPair(this.Input.name, this.Input.value);
  }
};

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
