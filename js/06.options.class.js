//7.10
/*
	User options UI and helper methods
*/

// Options base class

function OptionsBase() {
	this.defaultValues = [];
};

// Loading Template
OptionsBase.prototype.LoadTemplate = function(tab, user_id, login) {	// To be overriden
	this.LoadBaseTemplate(tab, user_id, login);
};

OptionsBase.prototype.LoadBaseTemplate = function(tab, user_id, login) {
	this.USER_ID = Math.round(user_id);
	this.LOGIN = login;

	tab[this.ClassName] = this;
	tab.Alerts = new Alerts(tab.TopicDiv);
	this.Tab = tab;

	if (this.Template) {
		RequestContent(this);
	}
};

// Template Callbacks
OptionsBase.prototype.TemplateLoaded = function(req) {	// To be overriden
	this.TemplateBaseLoaded(req);
};

OptionsBase.prototype.TemplateBaseLoaded = function(req) {
	text = req;
	if (req.responseText) {
		DebugLine("Template " + this.Template + " caching");
		text = req.responseText;
		KeepRequestedContent(this.Template, text);
	}
	DebugLine("Template publishing");
	this.Tab.RelatedDiv.innerHTML = text;
	this.Tab.InitUploadFrame();
	DebugLine("Data request");
	this.Request();
};

/* Checks if element type is allowed for value-operations */

OptionsBase.prototype.ValueType = function(t) {
	return (t == "text" || t == "password" || t == "hidden" || t == "select-one" || t == "textarea" || t == "color" || t == "date" || t == "datetime");
};

/* Gathering object properties from UI controls */

OptionsBase.prototype.GatherOne = function(name, property) {
	el = this.Inputs ? this.Inputs[name] : "";

	var prop = property ? property : name;
	if (el) {
		if (this.ValueType(el.type)) {
			this[prop] = el.value;
			return MakeParametersPair(name, el.value);
		} else if (el.type == "checkbox" || el.type == "radio") {
			this[prop] = el.checked;
			return MakeParametersPair(name, el.checked ? 1 : 0);
		}
		if (el.className == "Radios") {
			var value = GetRadioValue(el);
			this[prop] = value;
			return MakeParametersPair(name, value);
		}
	} else if (this[name] != "undefined") {
		return MakeParametersPair(name, this[name]);	// In case if no real controls relate to object
	}
	return "";
};

OptionsBase.prototype.GatherFields = function(fields) {
	this.FindRelatedControls();
	if (!fields) {
		if (!this.properties) {
			this.properties = this.fields;	// In case if properties differs from elements ids
		}
		fields = this.fields;
		properties = this.properties;
	} else {
		properties = fields;
	}

	var el;
	var result = "";
	for (var i = 0,l = fields.length; i < l; i++) {
		result += this.GatherOne(fields[i], properties[i]);
	}
	return result;
};

OptionsBase.prototype.BaseGather = function() {
	var result = this.GatherFields();
	if (this.alt_fields) {
		result += this.GatherFields(this.alt_fields);
	}
	return result;
};

OptionsBase.prototype.Gather = function() {	// Method to override
	return this.BaseGather();
};

/* Filling object properties with values from source array */

OptionsBase.prototype.FillBase = function(source, fields) {
	if (!fields) {
		fields = this.fields;
	} else {
		this.alt_fields = fields;
	}

	var doClear = 0;
	if (!source || !source.length) {
		source = this.defaultValues;	// Do reset fields to default values
	}
	if (source.length != fields.length) {
		doClear = 1;
	}
	
	var el;
	for (var i = 0,l = fields.length; i < l; i++) {
		this[fields[i]] = doClear ? "" : source[i];
	}
};

OptionsBase.prototype.FillFrom = function(source, fields) {		// Method to override
	this.FillBase(source, fields);
};

/* Binding tba controls with object's properties */

OptionsBase.prototype.FindRelatedControls = function(force) {
	if ((force || !this.Inputs) && this.Tab) {
		this.Inputs = IndexElementChildElements(this.Tab.RelatedDiv);
	}
};

OptionsBase.prototype.BindFields = function(fields) {
	this.FindRelatedControls();
	var el;
	for (var i = 0, l = fields.length; i < l; i++) {
		var field = fields[i];
		var value = this[field];
		if (value == "undefined") {
			value = "";
		}
		this.SetTabElementValue(field, value);
	}
};

OptionsBase.prototype.BaseBind = function() {
	this.BindFields(this.fields);
	if (this.alt_fields) {
		this.BindFields(this.alt_fields);
	}
};

OptionsBase.prototype.Bind = function() {	// Method to override
	return this.BaseBind();
};

OptionsBase.prototype.AssignObjectTo = function(id, obj, name) {
	this.FindRelatedControls();
	var el = this.Inputs[id];
	if (el) {
		el[name] = obj;
	}
};

OptionsBase.prototype.AssignTabTo = function(id) {
	this.AssignObjectTo(id, this.Tab, "Tab");
};

OptionsBase.prototype.AssignSelfTo = function(id) {
	this.AssignObjectTo(id, this, "obj");
};

OptionsBase.prototype.SetTabElementValue = function(element, value) {
	this.FindRelatedControls();
	var el = this.Inputs[element];
	if (el) {
		if (this.ValueType(el.type)) {
			el.value = value;
			return;
		} else if (el.type == "checkbox" || el.type == "radio") {
			el.checked = value;
			return;
		}

		if (el.className == "Radios") {
			SetRadioValue(el, value);
		} else {
			el.innerHTML = value;
		}
	}
};

OptionsBase.prototype.DisplayTabElement = function(element, state) {
	var el = this.Inputs[element];
	if (el) {
		DisplayElement(el, state);
	}
};

OptionsBase.prototype.Clear = function() {
	this.FindRelatedControls();
	for (var i = 0,l = this.fields.length; i < l; i++) {
		this[this.fields[i]] = "";
	}
};

OptionsBase.prototype.Reset = function() {
	if (!this.defaultValues || this.defaultValues.length < this.fields.length) {
		return;
	}
	this.FindRelatedControls();
	for (var i = 0,l = this.fields.length; i < l; i++) {
		this.SetTabElementValue(this.fields[i], this.defaultValues[i]);
	}
};

/* Request methods */

OptionsBase.prototype.BaseRequest = function(params, callback) {
	if (!params) {
		params = "";
	}
	params += MakeParametersPair("USER_ID", this.USER_ID);
	sendRequest(this.ServicePath, callback ? callback : this.RequestCallback, params, this);
};

OptionsBase.prototype.Request = function(params, callback) {	/* Method to override */
	this.BaseRequest(params, callback);
};

OptionsBase.prototype.Save = function(callback) {
	var params = this.Gather();
	params += MakeParametersPair("go", "save");
	this.Request(params, callback);
};

OptionsBase.prototype.Delete = function(callback) {
	var params = this.Gather();
	params += MakeParametersPair("go", "delete");
	this.Request(params, callback);
};


/* Common callback */

OptionsBase.prototype.RequestBaseCallback = function(req, obj) {
	this.data = [];
	this.Total = 0;
	if (obj) {
		var tabObject = obj.Tab;
		tabObject.Alerts.Clear();
		eval(req.responseText);
	}
};

// Reaction method to be overriden
OptionsBase.prototype.React = function(value) {alert("Reaction handler is unset.");};


OptionsBase.prototype.GroupAssign = function(method, items) {
	if (this[method]) {
		for (var i = 0, l = items.length; i < l; i++) {
			this[method](items[i]);
		}
	}
};

OptionsBase.prototype.GroupSelfAssign = function(items) {
	this.GroupAssign("AssignSelfTo", items);
};

OptionsBase.prototype.GroupTabAssign = function(items) {
	this.GroupAssign("AssignTabTo", items);
};


OptionsBase.prototype.UpdateToPrintableDate = function(field) {
	this.SetTabElementValue(field, ParseDate(this[field]).ToPrintableString(1));
};

/* Helper methods */

var optionsWindow;
function ShowOptions() {
	if (!optionsWindow || optionsWindow.closed) {
		optionsWindow = open("options", "options", "width=600,height=420,toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=1");
	}
	optionsWindow.focus();
};

/* Static content requestor */

var cachedContent = new Array();
function RequestContent(obj) {
	var req;
	if (cachedContent[obj.Template] && obj.TemplateLoaded) {
		var req = new Object();
		req.responseText = cachedContent[obj.Template];
		obj.TemplateLoaded(req);
	} else {
		// No callback passed as parameter. Supposed to be taken from Obj.
		req = sendRequest("/options/" + obj.Template + ".php", "", "", obj);
	}
	return req;
};

function KeepRequestedContent(name, value) {
	cachedContent[name] = value;
};

/* Load template template_name with data for user_id into tab */

function LoadAndBindObjectToTab(tab, user_id, obj, obj_class, callback, login) {
	obj.USER_ID = Math.round(user_id);
	obj.LOGIN = login;

	tab[obj_class] = obj;
	tab.Alerts = new Alerts(tab.TopicDiv);
	obj.Tab = tab;

	if (obj.Template) {
		RequestContent(obj);
	}
};

function CreateUserTab(id, login, obj, prefix, parameter, tab_id) {
	if (!tab_id) {
		tab_id = tabs.tabsCollection.LastId + 1;
	}
	var tab = tabs.tabsCollection.Get(tab_id);
	if (!tab) {
		tab = new Tab(tab_id, (prefix ? prefix : "") + (prefix && login ? " " : "") + login);
		tabs.Add(tab);

		SwitchToTab(tab_id);
		tabs.Print();

		var tab = tabs.tabsCollection.Get(tab_id);
		tab.PARAMETER = parameter;
		obj.LoadTemplate(tab, id, login);
	} else {
		SwitchToTab(tab_id);
		tabs.Print();
	}
	return tab;
};

/* Common methods */

function SaveObject(a) {
	if (a && a.obj) {
		if (a.obj.Tab & a.obj.Tab.Alerts) {
			a.obj.Tab.Alerts.Clear();
		}
		a.obj.Save();
	}
};

function ReRequestData(a) {
	if (a.obj) {
		a.obj.Tab.Alerts.Clear();
		a.obj.Request();
	}
};

function ResetFilter(a) {
	if (a.obj) {
		var ac = a.obj;
		ac.SetTabElementValue("DATE", "");
		ac.SetTabElementValue("SEARCH", "");
		if (ac.CustomReset) {
			ac.CustomReset();
		}
		ac.Request();
	}
};