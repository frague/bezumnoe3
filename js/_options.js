/*
  User options UI and helper methods
*/

// Options base class
class OptionsBase {
  constructor() {
    this.defaultValues = [];
  }

  // Loading Template
  loadTemplate(tab, user_id, login) {    // To be overriden
    this.LoadBaseTemplate(tab, user_id, login);
  }

  LoadBaseTemplate(tab, user_id, login) {
    this.USER_ID = Math.round(user_id);
    this.LOGIN = login;

    tab[this.ClassName] = this;
    tab.Alerts = new Alerts(tab.TopicDiv);
    this.Tab = tab;

    if (this.Template) {
      RequestContent(this);
    }
  }

  // Template Callbacks
  TemplateLoaded(req) {  // To be overriden
    this.TemplateBaseLoaded(req);
  }

  TemplateBaseLoaded(req) {
    var text = req || '';
    if (req) KeepRequestedContent(this.Template, text);
    this.Tab.RelatedDiv.innerHTML = text;
    this.Tab.initUploadFrame();
    this.request();
  }

  /* Checks if element type is allowed for value-operations */

  ValueType(t) {
    return ["text", "password", "hidden", "select-one", "textarea", "color", "date", "datetime"].indexOf(t) >= 0;
  }

  /* Gathering object properties from UI controls */

  GatherOne(name, property) {
    var el = this.inputs ? this.inputs[name] : '',
      prop = property || name,
      s = new ParamsBuilder();

    if (el) {
      if (this.ValueType(el.type)) {
        this[prop] = el.value;
        return s.add(name, el.value).build();
      } else if (el.type == "checkbox" || el.type == "radio") {
        this[prop] = el.checked;
        return s.add(name, el.checked ? 1 : 0).build();
      }
      if (el.className == "Radios") {
        var value = GetRadioValue(el);
        this[prop] = value;
        return s.add(name, value).build();
      }
    } else if (this[name] != "undefined") {
      return s.add(name, this[name]).build();
    }
    return "";
  }

  GatherFields(fields) {
    var properties = fields;
    this.FindRelatedControls();
    if (!fields) {
      if (!this.properties) {
        this.properties = this.fields;  // In case if properties differs from elements ids
      }
      fields = this.fields;
      properties = this.properties;
    }

    var el,
      result = "";
    for (var i = 0,l = fields.length; i < l; i++) {
      result += this.GatherOne(fields[i], properties[i]);
    }
    return result;
  }

  BaseGather() {
    var result = this.GatherFields();
    if (this.alt_fields) {
      result += this.GatherFields(this.alt_fields);
    }
    return result;
  }

  Gather() { // Method to override
    return this.BaseGather();
  }

  /* Filling object properties with values from source array */

  FillBase(source, fields) {
    if (!fields) {
      fields = this.fields;
    } else {
      this.alt_fields = fields;
    }

    var doClear = 0;
    if (!source || !source.length) {
      source = this.defaultValues;    // Do reset fields to default values
    }
    if (source.length != fields.length) {
      doClear = 1;
    }

    var el;
    for (var i = 0,l = fields.length; i < l; i++) {
      this[fields[i]] = doClear ? "" : source[i];
    }
  }

  FillFrom(source, fields) {     // Method to override
    this.FillBase(source, fields);
  }

  /* Binding tba controls with object's properties */

  FindRelatedControls(force) {
    if ((force || !this.inputs) && this.Tab) {
      this.inputs = IndexElementChildElements(this.Tab.RelatedDiv);
    }
  }

  BindFields(fields) {
    this.FindRelatedControls();
    _.each(
      fields,
      function(field) {
        console.log(field, this[field]);
        var value = this[field];
        if (_.isUndefined(value)) return;
        this.SetTabElementValue(field, value);
      },
      this
    )
  }

  BaseBind() {
    this.BindFields(this.fields);
    if (this.alt_fields) {
      this.BindFields(this.alt_fields);
    }
  }

  Bind() {   // Method to override
    return this.BaseBind();
  }

  AssignObjectTo(id, obj, name) {
    this.FindRelatedControls();
    var el = this.inputs[id];
    if (el) {
      el[name] = obj;
    }
  }

  AssignTabTo(id) {
    this.AssignObjectTo(id, this.Tab, "Tab");
  }

  AssignSelfTo(id) {
    this.AssignObjectTo(id, this, "obj");
  }

  SetTabElementValue(element, value) {
    this.FindRelatedControls();
    var el = this.inputs[element];
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
  }

  DisplayTabElement(element, state) {
    var el = this.inputs[element];
    if (el) {
      displayElement(el, state);
    }
  }

  Clear() {
    this.FindRelatedControls();
    for (var i = 0,l = this.fields.length; i < l; i++) {
      this[this.fields[i]] = "";
    }
  }

  Reset() {
    if (!this.defaultValues || this.defaultValues.length < this.fields.length) {
      return;
    }
    this.FindRelatedControls();
    for (var i = 0,l = this.fields.length; i < l; i++) {
      this.SetTabElementValue(this.fields[i], this.defaultValues[i]);
    }
  }

  /* Request methods */

  BaseRequest(params, callback) {
    var s = new ParamsBuilder(params);
    s.add('USER_ID', this.USER_ID);
    $.post(this.ServicePath, s.build())
      .then(callback || this.requestCallback);
  }

  request(params, callback) {    /* Method to override */
    this.BaseRequest(params, callback);
  }

  Save(callback) {
    var s = new ParamsBuilder(this.Gather());
    s.add('go', 'save');
    this.request(s.build(), callback);
  }

  Delete(callback) {
    var s = new ParamsBuilder(this.Gather());
    s.add('go', 'delete');
    this.request(s.build(), callback);
  }


  /* Common callback */

  requestBaseCallback(req) {
    this.data = [];
    this.Total = 0;

    this.Tab.Alerts.Clear();
    eval(req);
  }

  // Reaction method to be overriden
  React(value) {
    console.warn("Reaction handler is unset.");
  }


  GroupAssign(method, items) {
    if (this[method]) {
      for (var i = 0, l = items.length; i < l; i++) {
        this[method](items[i]);
      }
    }
  }

  GroupSelfAssign(items) {
    this.GroupAssign("AssignSelfTo", items);
  }

  GroupTabAssign(items) {
    this.GroupAssign("AssignTabTo", items);
  }

  UpdateToPrintableDate(field) {
    this.SetTabElementValue(field, ParseDate(this[field]).ToPrintableString(1));
  }
}

/* Helper methods */

function ShowOptions() {
  var tab = tabs.tabsCollection.Get('Меню');
  if (!tab) {
    var menuTab = new Tab('menu', 'Меню');
    tabs.Add(menuTab);
    menuTab.switchTo();
    tabs.Print();
    $(menuTab.RelatedDiv).load(
      '/options/menu.php',
      function() {
        initLayout(
          pages.menu,
          $('#MessagesContainer')[0]
        );
        $(menuTab.RelatedDiv).trigger('load');
      }
    );
  }
};

/* Static content requestor */

var cachedContent = new Array();
function RequestContent(obj) {
  var req;
  if (cachedContent[obj.Template] && obj.TemplateLoaded) {
    req = cachedContent[obj.Template];
    obj.TemplateLoaded.call(obj, req);
  } else {
    req = $.get('/options/' + obj.Template + '.php')
      .done(obj.TemplateLoaded.bind(obj));
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

function createUserTab(id, login, obj, prefix, parameter, tab_id) {
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
    obj.loadTemplate(tab, id, login);
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
    a.obj.request();
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
    ac.request();
  }
};
