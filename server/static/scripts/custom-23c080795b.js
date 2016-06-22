"use strict";

//1.0
/*
	Functions to be overridden.
*/

var spoilerNames = [];
var spoilerInits = [];

function ShowBlog() {};
function ShowUser() {};
function CreateAdminTab() {};
function umExtraButtons() {};
function umAdditionalExtraButtons() {};
"use strict";

//2.7
/*
	Admin only functionality.
	Will be loaded only if server rights checking is == adminRights.
*/

/* Options Admin tab */

function CreateAdminTab() {
	return new Tab(7, "Администрирование", 1, "", function (tab) {
		new AdminOptions().LoadTemplate(tab, me.Id);
	});
};

/* Usermanager admins' section */

function umExtraButtons(tr, id, login, obj) {
	var td1 = MakeSection("Опции администратора:");
	var ul1 = d.createElement("ul");

	ul1.appendChild(MakeUserMenuLink(MakeButtonLink("ShowUser(" + id + ",'" + login + "')", "Профиль", obj, "")));
	if (me.IsSuperAdmin()) {
		umAdditionalExtraButtons(ul1, id, login, obj);
	}
	td1.appendChild(ul1);
	tr.appendChild(td1);

	var td2 = MakeSection("Операции:", "Red");
	var ul2 = d.createElement("ul");
	ul2.appendChild(MakeUserMenuLink(MakeButtonLink("DeleteUser(" + id + ",'" + login + "', this)", "Удалить", obj, "Red")));
	td2.appendChild(ul2);
	tr.appendChild(td2);
};

/* Custom Tabs creation */

function CustomTab(id, name, o, tab_prefix, name_prefix) {
	var tab_id = tab_prefix + id;
	CreateUserTab(id, name, new o(), name_prefix, "", tab_id);
};

function ShowUser(id, name) {
	CustomTab(id, name, Profile, "u", "");
};

function DeleteUser(id, name, a) {
	co.AlertType = false;
	co.Show(function () {
		DeleteUserConfirmed(id, a.obj);
	}, "Удалить пользователя?", "Пользователь	<b>" + name + "</b> и все данные,	относящиеся к нему	(фотографии,	записи в журнале и форумах,	профиль) будут удалены.<br>Вы уверены?");
};

function DeleteUserConfirmed(id, obj) {
	var req = new Requestor(servicesPath + "user_delete.service.php", obj);
	req.Callback = RefreshList;
	req.Request(["user_id"], [id]);
	obj.Inputs["FILTER_BANNED"].DelayedRequestor.Request();
};

/* Admin Options */

var spoilerNames = ["Кодекс администратора	(обязателен для прочтения)", "Запреты", "Комнаты"];

var spoilerInits = [function (tab) {
	new LawCode().LoadTemplate(tab);
}, function (tab) {
	new BannedAddresses().LoadTemplate(tab);
}, function (tab) {}];
"use strict";

//4.1
/*
	SuperAdmin only functionality.
	Will be loaded only if server rights checking is > adminRights.
*/

/* Usermanager admins' section */

function umAdditionalExtraButtons(el, id, login, obj) {
	el.appendChild(MakeUserMenuLink(MakeButtonLink("ShowSettings(" + id + ",\"" + login + "\")", "Настройки", obj, "")));
	el.appendChild(MakeUserMenuLink(MakeButtonLink("ShowBlog(" + id + ",\"" + login + "\")", "Журнал", obj, "")));
};

function ShowBlog(id, name) {
	CustomTab(id, name, JournalMessages, "j", "Журнал");
};

function ShowSettings(id, name) {
	CustomTab(id, name, Settings, "s", "Настройки");
};

/* Admin Options */

var spoilerNames = ["Кодекс администратора", "Новости чата", "Запреты", "Логи системы", "Лог сообщений чата", "Персональные статусы", "Комнаты", "Боты", "Задачи по расписанию"];

var spoilerInits = [function (tab) {
	new LawCode().LoadTemplate(tab);
}, function (tab) {
	new News().LoadTemplate(tab);
}, function (tab) {
	new BannedAddresses().LoadTemplate(tab);
}, function (tab) {
	new SystemLog().LoadTemplate(tab);
}, function (tab) {
	new MessagesLog().LoadTemplate(tab);
}, function (tab) {
	new Statuses().LoadTemplate(tab);
}, function (tab) {
	new Rooms().LoadTemplate(tab);
}, function (tab) {
	new Bots().LoadTemplate(tab);
}, function (tab) {
	new ScheduledTasks().LoadTemplate(tab);
}];
"use strict";

//6.0
/*
    Contains all global script settings, constants, variables and common methods
*/

var debug = 0;

var d = document;
var w = window;
var voidLink = "javascript:void(0);";
var voidHref = "href=\"" + voidLink + "\"";

var imagesPath = "/img/";
var servicesPath = "/services/";
var userPhotosPath = "/img/photos/";
var avatarsPath = "/img/avatars/";
var skinsPreviewPath = "/img/journals/";
var openIdPath = "/img/openid/";

var adminRights = 75;
var keeperRights = 20;
var topicRights = 10;

var LoadingIndicator = "<div class='LoadingIndicator'></div>";

var SeverityCss = ["Warning", "Error"];

var ReplaceTags = new RegExp("\<[\/a-z][^\>]*\>", "gim");

/* Service methods */

function GetElement(el) {
    if (el && (el.nodeType || el.jquery)) {
        return el;
    }
    return "#" + el;
};

var display_options = { effect: "fade", easing: "easeInOutBack", duration: 600 };

function DisplayElement(el, state) {
    if (!el) {
        return;
    }

    el = GetElement(el);
    if (state) {
        $(el).show();
    } else {
        $(el).hide();
    }
}

function DoShow(el) {
    DisplayElement(el, true);
};

function DoHide(el) {
    DisplayElement(el, false);
};

function SwitchVisibility(el) {
    el = GetElement(el);
    $(el).toggle(display_options);
};

var empty_pass = "**********";
function ClearInput(el) {
    if (el.value == empty_pass) {
        el.value = "";
    } else {
        el.previousValue = el.value;
    }
};

function RestoreInput(el, relatedBlockId) {
    if (el.value != el.previousValue) {
        DisplayElement(relatedBlockId, el.value);
    }
    if (!el.value) {
        el.value = empty_pass;
    }
};

/*
   Workaround the IE bug with assigning
   names to dynamically created elements
   Upd.: Opera has document.all property
   Upd.: Mozilla doesn't know try-catch
*/
function CreateElement(tag, name) {
    var result;
    if (d.all) {
        try {
            result = d.createElement("<" + tag + (name ? " name=\"" + name + "\" id=\"" + name + "\"" : "") + ">");
        } catch (e) {}
    }
    if (!result) {
        result = d.createElement(tag);
        if (name) {
            result.name = name;
            result.id = name;
        }
    }
    return result;
};

function CreateBitInput(name, checked, is_radio) {
    var result;
    var type = is_radio ? "radio" : "checkbox";

    if (d.all) {
        try {
            result = d.createElement("<input type=\"" + type + "\" name=\"" + name + "\"" + (is_radio ? "" : " id=\"" + name + "\"") + (checked ? " checked" : "") + ">");
        } catch (e) {}
    }
    if (!result) {
        result = d.createElement("input");
        result.type = type;
        result.name = name;
        if (!is_radio) {
            result.id = name;
        }
        if (checked) {
            result.setAttribute("checked", "true");
        }
    }
    return result;
};

function CreateRadio(name, checked) {
    return CreateBitInput(name, checked, 1);
};

function CreateCheckBox(name, checked) {
    return CreateBitInput(name, checked, 0);
};

function CreateLabel(target, text) {
    label = CreateElement("label");
    label.innerHTML = text;
    if (target) {
        label.setAttribute("for", target.name ? target.name : target);
    }
    return label;
};

function CreateBooleanImage(state) {
    var img = new Image();
    img.src = state ? "/img/icons/done.gif" : "/img/delete_icon.gif";
    return img;
};

function CheckEmpty(value, def) {
    if (!value || value == "undefined") {
        value = def ? def : "";
    }
    return value;
};

function MakeButtonLink(target, text, obj, css, alt) {
    var a = d.createElement("a");
    a.href = voidLink;
    a.className = css;
    a.obj = obj;
    if (text) {
        a.innerHTML = text;
    }
    alt = CheckEmpty(alt);
    a.alt = alt;
    a.title = alt;
    eval("a.onclick=function(){" + target + "}");
    return a;
};

function MakeButton(target, src, obj, css, alt) {
    alt = CheckEmpty(alt);
    css = CheckEmpty(css);

    var a = MakeButtonLink(target, "", obj, css, alt);
    a.className = "Button " + css;
    a.innerHTML = "<img src='" + imagesPath + src + "' alt='" + alt + "' title='" + alt + "' />";
    return a;
};

function MakeDiv(text, tag) {
    var div = d.createElement(tag ? tag : "div");
    div.innerHTML = text;
    return div;
};

function IndexElementChildElements(el, hash) {
    if (!hash) {
        hash = new Array();
    }
    if (el) {
        if (el.id) {
            hash[el.id] = el;
        }
        for (var i = 0, l = el.childNodes.length; i < l; i++) {
            // Recursion
            IndexElementChildElements(el.childNodes[i], hash);
        }
    }
    return hash;
};

function insertAfter(new_node, existing_node) {
    if (existing_node.nextSibling) {
        existing_node.parentNode.insertBefore(new_node, existing_node.nextSibling);
    } else {
        existing_node.parentNode.appendChild(new_node);
    }
};

function BindList(list, holder, obj, empty_message) {
    var l = list.length;
    if (l) {
        for (var i = 0; i < l; i++) {
            holder.appendChild(list[i].ToString(i, obj));
        }
    } else {
        holder.innerHTML = empty_message;
    }
};

/* Methods to set/get value of group of radios in holder element */

function RenameRadioGroup(holder, name) {
    if (!name) {
        name = "group" + Math.random(9999);
    }
    for (var i = 0, l = holder.childNodes.length; i < l; i++) {
        var el = holder.childNodes[i];
        if (el && el.type == "radio") {
            el.name = name;
        }
    }
};

// Recursive methods
function SetRadioValue(holder, value) {
    result = "";
    for (var i = 0, l = holder.childNodes.length; i < l; i++) {
        var el = holder.childNodes[i];
        if (el.hasChildNodes()) {
            result = result || SetRadioValue(el, value);
        } else if (el && el.type == "radio") {
            m = el.value == "" + value;
            el.checked = m;
            result = m ? el : result;
        }
    }
    return result;
};

function GetRadioValue(holder) {
    for (var i = 0, l = holder.childNodes.length; i < l; i++) {
        var el = holder.childNodes[i];
        if (el.hasChildNodes()) {
            var v = GetRadioValue(el);
            if (v) {
                return v;
            }
        } else if (el && el.type == "radio") {
            if (el.checked) {
                return el.value;
            }
        }
    }
    return "";
};

// Bubbling

function CancelBubbling(e) {
    if (!e) {
        var e = window.event;
    }
    e.cancelBubble = true;
    if (e.stopPropagation) {
        e.stopPropagation();
    }
};

// Create Pop & Push methods For Array if not supported

function ArrayPop(a) {
    var o = a[a.length - 1];
    a.length--;
    return o;
};

function ArrayPush(a, p) {
    a[a.length] = p;
    return a.length;
};

function ArrayInsertFirst(a, p, len) {
    if (!a) {
        return false;
    }
    for (var i = a.length - 1; i >= 0; i--) {
        a[i + 1] = a[i];
    }
    a[0] = p;
    if (a.length > len) {
        a.length = len;
    }
};

// DOM Helper methods

function AddSelectOption(select, name, value, selected) {
    var opt = d.createElement("option");
    opt.value = value;
    opt.text = name;
    opt.selected = selected ? true : false;

    try {
        select.add(opt, null); // standards compliant; doesn't work in IE
    } catch (ex) {
        select.add(opt); // IE only
    }
};

function Random(n, not_null) {
    return (not_null ? 1 : 0) + Math.round(n * Math.random());
};
// Bind ddl with all rooms
function BindRooms(ddl) {
    if (ddl && opener && opener.rooms) {
        opener.rooms.Gather(ddl);
    }
};
'use strict';

//1.3
/*
    Helps in JS code debugging. Displays debugging information
    in pop-up window.
*/

var debugWin;

function DebugLine(line) {
    if (!debug) {
        return;
    }
    if (!debugWin || !debugWin.document) {
        debugWin = window.open('', 'debug');
        debugWin.document.open();
        debugWin.document.writeln('<html><head><title>bezumnoe log:</title><link rel=stylesheet type=text/css href=/css/debug.css></head><body>');
    }
    debugWin.document.writeln('<p>' + line);
};

function PropertiesOf(o) {
    var s = "";
    var l = 0;
    for (p in o) {
        s += p + "=" + o[p] + "     ";
        if (l++ == 3) {
            l = 0;
            s += "\n";
        }
    }
    return s;
};
"use strict";

//1.6
/*
	Contains misc string-related functions.
*/

var chars = "абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
var ascii = new Array();

for (var i = 0; i < chars.length; i++) {
	var ch = chars.charAt(i);
	ascii[ch] = i + 1;
};

function CheckSum(source) {
	source = "" + source;
	var sum = 0;
	if (source != "undefined" && source != "") {
		source = " " + source;

		var i;
		if (source && source.length) {
			var code;
			for (i = 1; i < source.length; i++) {
				code = ascii[source.charAt(i)];
				if (!code) {
					code = source.charCodeAt(i);
					if (code > 255) {
						code = 1;
					}
				}
				sum += code;
				//DebugLine(source.charAt(i) + " = " + code + " -> " + sum);
			}
		}
	}
	//DebugLine(source + " = " + sum + "<hr>");
	return sum;
};

function MakeParametersPair(name, value) {
	if (value == "undefined") {
		return "";
	}

	var param = encodeURIComponent(name);
	param += "=";
	param += encodeURIComponent(value);
	param += "&";
	return param;
};

function TwoDigits(d) {
	if (d > 9) {
		return d;
	}
	return "0" + d;
};

var tagsRegex = new RegExp("\<[\/]{0,1}[a-z]+[^\>]*\>", "ig");
function StripTags(text) {
	return text.replace(tagsRegex, "");
};

function HtmlQuotes(text) {
	return text.replace(/"/g, "&quot;");
};

function StrongHtmlQuotes(text) {
	text = text.replace(/&/g, "&amp;");
	text = HtmlQuotes(text);
	text = text.replace(/</g, "&lt;");
	text = text.replace(/>/g, "&gt;");
	text = text.replace(/"/g, "&quot;");
	return text;
};

function Quotes(text) {
	//	return text.replace(/\"/g, "\\\"").replace(/'/g, "\\'");
	return text.replace(/'/g, "\\'").replace(/"/g, "&amp;quot;");
};

function Slash(text) {
	return text.replace(/(['"<>])/g, "\\$1");
};
"use strict";

//2.0

function sendRequest(url, callback, postData, obj) {
    $.ajax({
        url: url,
        data: postData,
        type: postData ? "POST" : "GET"
    }).done(function (data) {
        if (callback) {
            // callback passed as parameter
            callback(data, obj);
        } else if (obj && obj.TemplateLoaded) {
            // Template render callback
            obj.TemplateLoaded(data);
        }
    });
    return;
};

function handleRequest(responseText) {
    try {
        eval(responseText);
        return;
    } catch (e) {
        return;
    }
};
'use strict';

//2.6
/*
    Collection of entities (users, rooms etc.)
*/

function Collection() {
    this.Base = new Array();
    this.LastId = 0;
};

Collection.prototype.Get = function (id) {
    if (this.Base['_' + id]) {
        return this.Base['_' + id];
    }
    return false;
};

Collection.prototype.Add = function (e) {
    this.Base['_' + e.Id] = e;
    this.LastId = e.Id;
};

Collection.prototype.BulkAdd = function (arr) {
    for (var i = 0, l = arr.length; i < l; i++) {
        el = arr[i];
        if (!el.Id) {
            el.Id = i + 1;
        }
        this.Add(el);
    }
};

Collection.prototype.Delete = function (id) {
    id = '_' + id;
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

Collection.prototype.Clear = function () {
    this.Base = new Array();
};

Collection.prototype.Count = function () {
    var l = 0;
    for (var k in this.Base) {
        l += k ? 1 : 0;
    }
    return l;
};

Collection.prototype.ToString = function (holder) {
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

Collection.prototype.Gather = function (holder) {
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
"use strict";

//3.6
/*
	DTOs with edit functionality
*/

/* Vase DTO */

function DTO() {
	this.fields = [];
};

DTO.prototype.Init = function (args) {
	if (!args || !this.fields || args.length != this.fields.length) {
		return;
	}
	for (var i = 0, l = this.fields.length; i < l; i++) {
		this[this.fields[i]] = args[i];
	}
};

DTO.prototype.ToString = function (index, grid) {
	return this.ToShowView(index, grid);
};

DTO.prototype.ToShowView = function () {
	// To override
	return "";
};

/* Editable DTO */

function EditableDTO() {
	this.EditView = false;
};

EditableDTO.prototype = new DTO();

EditableDTO.prototype.ToString = function (index, grid) {
	if (this.EditView) {
		return this.ToEditView(index, grid);
	} else {
		return this.ToShowView(index, grid);
	}
};

EditableDTO.prototype.ToEditView = function () {
	// To override
	return "";
};

EditableDTO.prototype.Gather = function () {
	for (var i = 0, l = this.fields.length; i < l; i++) {
		var input = this[this.fields[i] + "Input"];
		if (input) {
			if (input.type == "checkbox" || input.type == "radio") {
				this[this.fields[i]] = input.checked ? 1 : "";
			} else {
				this[this.fields[i]] = input.value;
			}
		}
	}
};

/* Buttons events */

EditableDTO.prototype.CancelEditing = function () {
	if (this.Grid) {
		this.Grid.CancelEditing();
	}
};

EditableDTO.prototype.Edit = function () {
	if (this.Grid) {
		this.Grid.Edit(this.Id);
	}
};

EditableDTO.prototype.Save = function () {
	if (this.Grid) {
		this.Gather();
		if (this.Grid.GatherDTO(this)) {
			this.Grid.Save();
		}
	}
};

EditableDTO.prototype.Delete = function () {
	if (this.Grid) {
		this.Gather();
		if (this.Grid.GatherDTO(this)) {
			this.Grid.Delete();
		}
	}
};

/* ---------------------------- */

EditableDTO.prototype.MakeButtonsCell = function (hideEdit) {
	var td = d.createElement("td");
	td.className = "Middle Centered";
	if (this.EditView) {
		td.appendChild(MakeButton("this.obj.Save()", "icons/done.gif", this, "", "Сохранить"));
		td.appendChild(MakeButton("this.obj.CancelEditing()", "icons/cancel.gif", this, "", "Отмена"));
	} else {
		if (!hideEdit) {
			td.appendChild(MakeButton("this.obj.Edit()", "icons/edit.gif", this, "", "Править"));
		}
		td.appendChild(MakeButton("this.obj.Delete()", "delete_icon.gif", this, "", "Удалить"));
	}
	return td;
};
"use strict";

//7.10
/*
    User options UI and helper methods
*/

// Options base class

function OptionsBase() {
    this.defaultValues = [];
};

// Loading Template
OptionsBase.prototype.LoadTemplate = function (tab, user_id, login) {
    // To be overriden
    this.LoadBaseTemplate(tab, user_id, login);
};

OptionsBase.prototype.LoadBaseTemplate = function (tab, user_id, login) {
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
OptionsBase.prototype.TemplateLoaded = function (req) {
    // To be overriden
    this.TemplateBaseLoaded(req);
};

OptionsBase.prototype.TemplateBaseLoaded = function (req) {
    text = req;
    if (req) {
        DebugLine("Template " + this.Template + " caching");
        text = req;
        KeepRequestedContent(this.Template, text);
    }
    DebugLine("Template publishing");
    this.Tab.RelatedDiv.innerHTML = text;
    this.Tab.InitUploadFrame();
    DebugLine("Data request");
    this.Request();
};

/* Checks if element type is allowed for value-operations */

OptionsBase.prototype.ValueType = function (t) {
    return t == "text" || t == "password" || t == "hidden" || t == "select-one" || t == "textarea" || t == "color" || t == "date" || t == "datetime";
};

/* Gathering object properties from UI controls */

OptionsBase.prototype.GatherOne = function (name, property) {
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
        return MakeParametersPair(name, this[name]); // In case if no real controls relate to object
    }
    return "";
};

OptionsBase.prototype.GatherFields = function (fields) {
    this.FindRelatedControls();
    if (!fields) {
        if (!this.properties) {
            this.properties = this.fields; // In case if properties differs from elements ids
        }
        fields = this.fields;
        properties = this.properties;
    } else {
        properties = fields;
    }

    var el;
    var result = "";
    for (var i = 0, l = fields.length; i < l; i++) {
        result += this.GatherOne(fields[i], properties[i]);
    }
    return result;
};

OptionsBase.prototype.BaseGather = function () {
    var result = this.GatherFields();
    if (this.alt_fields) {
        result += this.GatherFields(this.alt_fields);
    }
    return result;
};

OptionsBase.prototype.Gather = function () {
    // Method to override
    return this.BaseGather();
};

/* Filling object properties with values from source array */

OptionsBase.prototype.FillBase = function (source, fields) {
    if (!fields) {
        fields = this.fields;
    } else {
        this.alt_fields = fields;
    }

    var doClear = 0;
    if (!source || !source.length) {
        source = this.defaultValues; // Do reset fields to default values
    }
    if (source.length != fields.length) {
        doClear = 1;
    }

    var el;
    for (var i = 0, l = fields.length; i < l; i++) {
        this[fields[i]] = doClear ? "" : source[i];
    }
};

OptionsBase.prototype.FillFrom = function (source, fields) {
    // Method to override
    this.FillBase(source, fields);
};

/* Binding tba controls with object's properties */

OptionsBase.prototype.FindRelatedControls = function (force) {
    if ((force || !this.Inputs) && this.Tab) {
        this.Inputs = IndexElementChildElements(this.Tab.RelatedDiv);
    }
};

OptionsBase.prototype.BindFields = function (fields) {
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

OptionsBase.prototype.BaseBind = function () {
    this.BindFields(this.fields);
    if (this.alt_fields) {
        this.BindFields(this.alt_fields);
    }
};

OptionsBase.prototype.Bind = function () {
    // Method to override
    return this.BaseBind();
};

OptionsBase.prototype.AssignObjectTo = function (id, obj, name) {
    this.FindRelatedControls();
    var el = this.Inputs[id];
    if (el) {
        el[name] = obj;
    }
};

OptionsBase.prototype.AssignTabTo = function (id) {
    this.AssignObjectTo(id, this.Tab, "Tab");
};

OptionsBase.prototype.AssignSelfTo = function (id) {
    this.AssignObjectTo(id, this, "obj");
};

OptionsBase.prototype.SetTabElementValue = function (element, value) {
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

OptionsBase.prototype.DisplayTabElement = function (element, state) {
    var el = this.Inputs[element];
    if (el) {
        DisplayElement(el, state);
    }
};

OptionsBase.prototype.Clear = function () {
    this.FindRelatedControls();
    for (var i = 0, l = this.fields.length; i < l; i++) {
        this[this.fields[i]] = "";
    }
};

OptionsBase.prototype.Reset = function () {
    if (!this.defaultValues || this.defaultValues.length < this.fields.length) {
        return;
    }
    this.FindRelatedControls();
    for (var i = 0, l = this.fields.length; i < l; i++) {
        this.SetTabElementValue(this.fields[i], this.defaultValues[i]);
    }
};

/* Request methods */

OptionsBase.prototype.BaseRequest = function (params, callback) {
    if (!params) {
        params = "";
    }
    params += MakeParametersPair("USER_ID", this.USER_ID);
    sendRequest(this.ServicePath, callback ? callback : this.RequestCallback, params, this);
};

OptionsBase.prototype.Request = function (params, callback) {
    /* Method to override */
    this.BaseRequest(params, callback);
};

OptionsBase.prototype.Save = function (callback) {
    var params = this.Gather();
    params += MakeParametersPair("go", "save");
    this.Request(params, callback);
};

OptionsBase.prototype.Delete = function (callback) {
    var params = this.Gather();
    params += MakeParametersPair("go", "delete");
    this.Request(params, callback);
};

/* Common callback */

OptionsBase.prototype.RequestBaseCallback = function (req, obj) {
    this.data = [];
    this.Total = 0;
    if (obj) {
        var tabObject = obj.Tab;
        tabObject.Alerts.Clear();
        eval(req);
    }
};

// Reaction method to be overriden
OptionsBase.prototype.React = function (value) {
    alert("Reaction handler is unset.");
};

OptionsBase.prototype.GroupAssign = function (method, items) {
    if (this[method]) {
        for (var i = 0, l = items.length; i < l; i++) {
            this[method](items[i]);
        }
    }
};

OptionsBase.prototype.GroupSelfAssign = function (items) {
    this.GroupAssign("AssignSelfTo", items);
};

OptionsBase.prototype.GroupTabAssign = function (items) {
    this.GroupAssign("AssignTabTo", items);
};

OptionsBase.prototype.UpdateToPrintableDate = function (field) {
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
        var req = cachedContent[obj.Template];
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
"use strict";

//3.1
/*
    Validation of controls against rules given.
*/

function ValidatorsCollection() {
    this.Clear();
};

ValidatorsCollection.prototype = new Collection();

ValidatorsCollection.prototype.Init = function (summary_control, summary_text) {
    this.Summary = $(GetElement(summary_control))[0];
    this.SummaryText = summary_text ? "<h2>" + summary_text + "</h2>" : "";
    this.InitSummary();

    for (var id in this.Base) {
        if (id && this.Base[id].Init) {
            this.Base[id].Init();
        }
    }
};

ValidatorsCollection.prototype.InitSummary = function () {
    if (this.Summary) {
        this.Summary.innerHTML = this.SummaryText;
        DoHide(this.Summary);
    }
};

ValidatorsCollection.prototype.ShowSummary = function (errors) {
    if (this.Summary && errors && errors.length) {
        this.Summary.innerHTML = this.SummaryText + "<li> " + errors.join("<li> ");
        DoShow(this.Summary);
    }
};

ValidatorsCollection.prototype.AreValid = function () {
    this.InitSummary();

    var result = true;
    for (var id in this.Base) {
        if (id && this.Base[id].Validate) {
            if (!this.Base[id].Validate(this.Summary)) {
                result = false;
            }
        }
    }
    return result;
};

var PageValidators = new ValidatorsCollection();

function ValueHasChanged() {
    return PageValidators.AreValid();
};

/* --------------- Single Validator --------------- */

function Validator(control, rule, message, summarize, on_the_fly) {
    this.Control = $(GetElement(control))[0];
    this.Rule = rule;
    this.Message = message;
    this.ShowInSummary = summarize;
    this.OnTheFly = on_the_fly;

    this.Id = Random(1000, 1);
    this.Enabled = true;
};

Validator.prototype.Init = function () {
    if (this.OnTheFly) {
        this.Control.onchange = ValueHasChanged;
    }

    this.ErrorContainer = d.createElement("div");
    if (!this.ShowInSummary) {
        this.Display(false);
        this.ErrorContainer.innerHTML = this.Message;

        insertAfter(this.ErrorContainer, this.Control);
    }
};

Validator.prototype.Validate = function (summary_control) {
    if (this.Control && this.Rule.Check(this.Control.value, this.Control)) {
        this.Display(false);
        return true;
    }
    this.Control.focus();
    this.Display(true, summary_control);
    return false;
};

Validator.prototype.Display = function (state, summary_control) {
    if (summary_control && this.ShowInSummary) {
        summary_control.innerHTML += "<li>" + this.Message;
        DoShow(summary_control);
    } else {
        this.ErrorContainer.className = "Validator" + (state ? "" : " Hidden");
    }
};

/* -------------------- Validation Rules -------------------- */
// Required Field

function RequiredField() {};

RequiredField.prototype.Check = function (value) {
    return value.length > 0;
};

// Field Length

function LengthRange(min_length, max_length) {
    this.MinLength = min_length;
    this.MaxLength = max_length;
};

LengthRange.prototype.Check = function (value) {
    var l = value.length;
    return l >= this.MinLength && l <= this.MaxLength;
};

// Equal To

function EqualTo(control) {
    this.Control = control;
};

EqualTo.prototype.Check = function (value) {
    return this.Control && this.Control.value == value;
};

// Match the pattern
var emailPattern = new RegExp("^[0-9a-zA-Z\!\#\$\'\*\+\-\/\=\?\^_\.\`\{\|\}\~]+\@[0-9a-zA-Z\!\#\$\'\*\+\-\/\=\?\^_\`\{\|\}\~]{2,50}([\.][0-9a-zA-Z\!\#\$\'\*\+\-\/\=\?\^_\`\{\|\}\~]{2,50})+$");

function MatchPattern(pattern) {
    this.Pattern = pattern;
};

MatchPattern.prototype.Check = function (value) {
    return value.match(this.Pattern);
};

// Is Checked
function IsChecked() {};

IsChecked.prototype.Check = function (x, control) {
    return control.checked;
};
'use strict';

//1.1
/*

    MyFrame class
    Handles window resize and update object's properties correspondingly

*/
function MyFrame(obj, min_width, min_height) {
    this.x = 0;
    this.y = 0;
    this.width = 0;
    this.height = 0;

    this.minWidth = min_width ? parseInt(min_width) : 0;
    this.minHeight = min_height ? parseInt(min_height) : 0;

    this.element = 0;

    this.GetPosAndSize = function () {
        if (this.element == window) {
            return this.GetWindowSize();
        }

        this.width = parseInt(this.element.clientWidth);
        this.height = parseInt(this.element.clientHeight);

        var obj = this.element;

        if (obj.offsetParent) {
            this.x = obj.offsetLeft;
            this.y = obj.offsetTop;
            while (obj = obj.offsetParent) {
                this.x += obj.offsetLeft;
                this.y += obj.offsetTop;
            }
        }

        /*console.log(self.innerHeight + " : " + self.height);*/
    };

    this.GetWindowSize = function () {
        this.x = 0;
        this.y = 0;

        if (self.innerWidth) {
            this.width = self.innerWidth;
            this.height = self.innerHeight;
        } else if (document.documentElement && document.documentElement.clientWidth) {
            this.width = document.documentElement.clientWidth;
            this.height = document.documentElement.clientHeight;
        } else if (document.body) {
            this.width = document.body.clientWidth;
            this.height = document.body.clientHeight;
        }

        if (navigator.appVersion.indexOf("Chrome") > 0) {
            this.height -= 24;
        }
    };

    this.Replace = function (x, y, w, h) {
        if (this.element == window || !this.element.style) {
            return;
        }

        if (x >= 0) {
            this.element.style.left = x + 'px';
        }
        if (y >= 0) {
            this.element.style.top = y + 'px';
        }
        if (w >= 0) {
            if (w < this.minWidth) {
                w = this.minWidth;
            }
            this.element.style.width = w + 'px';
        }
        if (h >= 0) {
            if (h < this.minHeight) {
                h = this.minHeight;
            }
            this.element.style.height = h + 'px';
        }
        this.GetPosAndSize();
    };

    this.Info = function () {
        var s = 'x=' + this.x + ', ';
        s += 'y=' + this.y + ', ';
        s += 'width=' + this.width + ', ';
        s += 'height=' + this.height;
        return s;
    };

    if (obj) {
        this.element = obj;
        this.GetPosAndSize();
    }
}
"use strict";

//3.8
/*
    Sliding panel class
*/

var panels = new Array();

function Panel() {
    this.IsOpened = 1;
    this.Step = 20;
    this.MinPosition = 10;

    this.BaseLinkClass = "Panel";
};

Panel.prototype.Init = function (id, size) {
    this.Id = id;
    this.Holder = $(getElement(id))[0];
    this.Position = size;
    this.CurrentSize = size;

    this.CreateLink();

    if (this.Holder) {
        this.Resize(this.Position);
        if (this.SwitchLink) {
            this.SwitchLink.href = voidLink;
            this.SwitchLink.Panel = this;
            this.SwitchLink.onclick = function () {
                Slide(this.Panel);
            };

            this.LinkSwitcher();
        }
        panels[id] = this;
    }
};

Panel.prototype.CreateLink = function () {
    this.SwitchLink = d.createElement("a");
    this.SwitchLink.onfocus = function () {
        this.blur();
    };
    if (this.Holder.hasChildNodes()) {
        this.Holder.insertBefore(this.SwitchLink, this.Holder.firstChild);
    } else {
        this.Holder.appendChild(this.SwitchLink);
    }
};

Panel.prototype.LinkSwitcher = function () {
    this.SwitchLink.className = this.BaseLinkClass + " " + (this.IsOpened ? "Opened" : "Closed");
    _.result(window, 'onResize');
};

Panel.prototype.ResizeBy = function (to) {
    var result = true;
    var size = this.CurrentSize + to;

    if (size < this.MinPosition) {
        size = this.MinPosition;
        this.IsOpened = false;
        result = false;
    }
    if (size > this.Position) {
        size = this.Position;
        this.IsOpened = true;
        result = false;
    }
    if (!result) {
        this.LinkSwitcher();
    }
    this.Resize(size);
    return result;
};

Panel.prototype.Resize = function (size) {// Method to override
};

function Slide(panel) {
    if (panel.ResizeBy(panel.IsOpened ? -panel.Step : panel.Step)) {
        setTimeout(function () {
            Slide(panel);
        }, 1);
    }
};

/* Left Panel derived class */

function LeftPanel(id, size) {
    this.BaseLinkClass = "PanelLeft";

    this.Init(id, size);
};

LeftPanel.prototype = new Panel();

LeftPanel.prototype.Resize = function (size) {
    //  this.Holder.style.width = size + "px";
    this.Holder.style.left = size - this.Position + "px";
    this.CurrentSize = size;
};

/* Right Panel derived class */

function RightPanel(id, size) {
    this.BaseLinkClass = "PanelRight";

    this.MinPosition = 10;
    this.Init(id, size);
};

RightPanel.prototype = new Panel();

RightPanel.prototype.Resize = function (size) {
    this.Holder.style.width = size + "px";
    this.Holder.style.right = "10px";
    this.CurrentSize = size;
};
"use strict";

//3.2
/*
    Represents room entity on client-side.
*/

function Room(id, title, topic, topic_lock, topic_author_id, topic_author_name, is_locked, is_by_invitation, owner_id) {
    // Properties

    this.Id = id;
    this.Title = title;
    this.Topic = topic;
    this.TopicLock = topic_lock;
    this.TopicAuthorId = topic_author_id;
    this.TopicAuthorName = topic_author_name;
    this.IsLocked = is_locked;
    this.IsInvitationRequired = is_by_invitation;
    this.OwnerId = owner_id;
};

// Methods
Room.prototype.IsCurrent = function () {
    if (this.Id == CurrentRoomId) {
        CurrentRoom = this;
        return true;
    }
    return false;
};

Room.prototype.Enter = function () {
    CurrentRoomId = this.Id;
};

Room.prototype.ToString = function () {
    var s = "<li class='roomBox" + (this.IsCurrent() ? " Current" : "") + "'>";
    var title = this.Title.length < 16 ? this.Title : this.Title.substr(0, 16) + "...";
    if (this.IsCurrent()) {
        s += "<strong class='" + this.MakeCSS() + "' title='" + this.Title + "'>" + title + "</strong>";
    } else {
        s += "<a " + voidHref + " onclick=\"ChangeRoom('" + this.Id + "')\" class='" + this.MakeCSS() + "' title='" + this.Title + "'>" + title + "</a>";
    }

    var inside = 0;
    var t = '<ul class=\"Users\">';
    var requestors = "";

    for (var id in users.Base) {
        var user = users.Base[id];
        if (user && user.RoomId == this.Id) {
            var str = user.ToString(this);
            if (user.HasAccessTo(this) || me.RoomId != this.Id) {
                t += str;
                inside++;
            } else {
                requestors += str;
            }
        }
    }
    if (requestors) {
        t += "<div class=\"Requestors\">Ожидают допуска:</div>" + requestors;
    }
    t += "</ul></li>";
    s = s + (inside ? "&nbsp;<span class='Count'>(" + inside + ")</span>" : "") + (inside || requestors ? t : "");
    return s;
};

Room.prototype.Gather = function (sel) {

    var opt = d.createElement("option");
    opt.value = this.Id;
    opt.text = this.Title;

    try {
        sel.add(opt, null); // standards compliant; doesn't work in IE
    } catch (ex) {
        sel.add(opt); // IE only
    }
};

Room.prototype.MakeCSS = function () {
    var cl = this.IsInvitationRequired ? "Private" : "Usual";
    cl += this.IsCurrent() ? " Current" : "";
    cl += this.IsLocked ? " Locked " : "";
    return cl;
};

Room.prototype.CheckSum = function () {
    var cs = CheckSum(this.OwnerId);
    cs += CheckSum(this.Title);
    cs += CheckSum(this.Topic);
    cs += CheckSum(this.TopicLock);
    cs += CheckSum(this.TopicAuthorId);
    cs += CheckSum(this.IsLocked);
    cs += CheckSum(this.IsInvitationRequired);
    //DebugLine("Room: " + this.Id + " sum: "+cs);
    return cs;
};

var CurrentRoom;
function ChangeRoom(id) {
    if (rooms && rooms.Get) {
        var room = rooms.Get(id);
        if (room && room.Enter) {
            room.Enter();
            if (MoveToRoom) {
                MoveToRoom(id);
            }
            if (PrintRooms) {
                PrintRooms();
            }
        } else {
            return false;
        }
    }
};

/* Room DTO class */

function RoomLightweight() {
    this.fields = new Array("NEW_ROOM", "IS_PRIVATE", "IS_LOCKED");
    this.ServicePath = servicesPath + "room.service.php";
    this.Template = "add_room";
    this.ClassName = "AddRoom";
};

RoomLightweight.prototype = new OptionsBase();

RoomLightweight.prototype.requestCallback = function (responseText) {
    if (responseText) {
        this.SetRoomStatus(responseText);
    } else {
        this.SetRoomStatus("");
        this.Clear();
        this.Bind();
        this.Tab.Display(false);
        PrintRooms();
    }
};

RoomLightweight.prototype.request = function (params, callback) {};

RoomLightweight.prototype.Save = function (callback) {
    var params = this.Gather();
    if (this.NEW_ROOM) {
        this.BaseRequest(params, callback);
    } else {
        this.SetRoomStatus("Введите название");
    }
};

RoomLightweight.prototype.SetRoomStatus = function (text) {
    this.FindRelatedControls();
    var st = this.inputs["RoomStatus"];
    if (st) {
        st.innerHTML = text;
    }
};

RoomLightweight.prototype.TemplateLoaded = function (req) {
    this.TemplateBaseLoaded(req);

    displayElement("AdminOnly", me && me.Rights >= adminRights);

    this.AssignTabTo("linkAdd");
    BindEnterTo(this.inputs["NEW_ROOM"], this.inputs["linkAdd"]);
};

/* Room lightweight link actions */

function AddRoom(a) {
    if (a.Tab) {
        a.Tab.AddRoom.Save();
    }
};

/* Room Data Transfer Object */

function rdto(id, title, is_deleted, is_locked) {
    this.fields = ["Id", "Title", "IsDeleted", "IsLocked", "IsInvitationRequired"];
    this.Init(arguments);
};

rdto.prototype = new EditableDTO();
"use strict";

//1.0
/*
    Represents status entity on client-side.
*/

function Status(rights, title, color) {
    // Properties

    this.Rights = rights;
    this.Title = title;
    this.Color = color;
};

// Methods
Status.prototype.MakeCSS = function () {
    return "color:" + this.Color + ";";
};

Status.prototype.CheckSum = function () {
    var cs = CheckSum(this.Rights);
    cs += CheckSum(this.Title);
    cs += CheckSum(this.Color);

    return cs;
};
"use strict";

//2.5
/*
    Represents font settings
*/

function Font(color, size, face, is_bold, is_italic, is_underlined) {
    this.Color = color;
    this.Size = size;
    this.Face = face;
    this.IsBold = is_bold;
    this.IsItalic = is_italic;
    this.IsUnderlined = is_underlined;

    this.fields = new Array("FONT_COLOR", "FONT_SIZE", "FONT_FACE", "FONT_BOLD", "FONT_ITALIC", "FONT_UNDERLINED");
    this.properties = new Array("Color", "Size", "Face", "IsBold", "IsItalic", "IsUnderlined");
};

Font.prototype = new OptionsBase();

// Methods

Font.prototype.ToCSS = function (observer) {
    var s = observer && observer.Settings ? observer.Settings : new Settings('', 0, 0, 0, 0);

    var style = '';
    if (this.Color && !s.IgnoreColors) {
        style += "color:" + this.Color + ";";
    }
    if (this.Face && !s.IgnoreFonts) {
        style += "font-family:\"" + this.Face + "\";";
    }
    if (this.Size && !s.IgnoreSizes) {
        style += "font-size:" + (5 + 2 * this.Size) + "pt;";
    }
    if (!s.IgnoreStyles) {
        if (1 * this.IsBold) {
            style += "font-weight:bold;";
        }
        if (1 * this.IsItalic) {
            style += "font-style:italic;";
        }
        if (1 * this.IsUnderlined) {
            style += "text-decoration:underline;";
        }
    }
    return style;
};

Font.prototype.ApplyTo = function (element) {
    if (element) {
        var result = (this.IsItalic ? "italic" : "normal") + " normal ";
        result += this.IsBold ? "bold " : "normal ";
        result += 5 + 2 * this.Size + "pt ";
        result += "'" + this.Face + "'";
        element.style.font = result;

        element.style.textDecoration = this.IsUnderlined ? "underline" : "none";
        element.style.color = this.Color;
    };
};

Font.prototype.CheckSum = function () {
    var cs = CheckSum(this.Color);
    cs += CheckSum(this.Size);
    cs += CheckSum(this.Face);
    cs += CheckSum(this.IsBold);
    cs += CheckSum(this.IsItalic);
    cs += CheckSum(this.IsUnderlined);
    return cs;
};
"use strict";

//5.4
/*
	Represents user entity on client-side.
*/

var IsIgnoredDefault = "",
    IgnoresYouDefault = "";

function User(id, login, room_id, room_is_permitted, ip, away_message, ban_reason, banned_by, nickname, settings, rights, status_title, status_color, is_ignored, ignores_you) {
	// Properties

	this.Id = id;
	this.Login = login;
	this.RoomId = room_id;
	this.RoomIsPermitted = room_is_permitted;

	this.SessionAddress = ip;
	this.AwayMessage = away_message;

	this.BanReason = ban_reason;
	this.BannedBy = banned_by;

	this.Nickname = nickname;

	this.Settings = settings;

	this.Rights = rights;
	this.StatusTitle = status_title;
	this.StatusColor = status_color;

	this.IsIgnored = !!is_ignored;
	this.IgnoresYou = !!ignores_you;
}

User.prototype.CheckSum = function () {
	var cs = 0;
	cs += CheckSum(this.RoomId);
	cs += CheckSum(this.RoomIsPermitted);

	cs += CheckSum(this.AwayMessage);

	cs += CheckSum(this.BanReason);
	cs += CheckSum(this.BannedBy);

	cs += this.Settings.CheckSum();

	cs += CheckSum(this.Nickname);

	cs += CheckSum(this.Rights);
	cs += CheckSum(this.StatusTitle);
	cs += CheckSum(this.StatusColor);

	cs += "" + this.IsIgnored + "" + this.IgnoresYou;

	return cs;
};

User.prototype.IsAdmin = function () {
	return this.Rights >= adminRights;
};

User.prototype.isSuperAdmin = function () {
	return this.Rights > adminRights;
};

User.prototype.ToString = function (room) {
	var name = this.Nickname || this.Login,
	    qname = Quotes(name),
	    has_access = this.HasAccessTo(room),
	    s = this.NameToString(name, has_access);

	s += '<div class="UserInfo" style="display:none" onmouseover="Show(this);" onclick="HideDelayed();" onmouseout="Hide();" id="_' + this.Id + '">';
	s += '<ul><li> <a ' + voidHref + ' class="Close">x</a><a ' + voidHref + ' onclick="Info(' + this.Id + ')">Инфо</a>';

	if (this.Id != me.Id && me.RoomId == this.RoomId) {
		if (!has_access && me.RoomIsPermitted == 1 && this.RoomIsPermitted == 0) {
			s += '<li class="Grant"> <a ' + voidHref + ' onclick="AG(' + this.Id + ',1)">Впустить</a>';
		} else if (room.OwnerId == me.Id && this.Id != room.OwnerId) {
			s += '<li class="Deny"> <a ' + voidHref + ' onclick="AG(' + this.Id + ',0)">Закрыть доступ</a>';
		}
	}

	s += '<li> <a ' + voidHref + ' onclick="_(\'' + qname + '\')">Обратиться</a>';
	s += '<li> <a ' + voidHref + ' onclick="AR(' + this.Id + ',\'' + qname + '\')">Шёпотом</a>';
	s += '<li> <a ' + voidHref + ' onclick="AR(' + this.Id + ',\'' + qname + '\',\'wakeup\')">Вейкап</a>';
	if (!me || this.Id != me.Id) {
		s += '<li> <a ' + voidHref + ' onclick="IG(' + this.Id + ',\'' + this.IsIgnored + '\')">' + (this.IsIgnored ? 'Убрать игнор' : 'В игнор') + '</a>';
	}
	if (me && me.Rights >= this.Rights) {
		if (me.IsAdmin() || me.Rights == keeperRights) {
			s += '<li> <a ' + voidHref + ' onclick="AR(' + this.Id + ',\'' + qname + '\',\'kick\')">Выгнать</a>';
		}
		if (me.IsAdmin() && me.Rights > this.Rights && this.Rights != keeperRights || me.isSuperAdmin()) {
			s += '<li> <a ' + voidHref + ' onclick="AR(' + this.Id + ',\'' + qname + '\',\'ban\')">Забанить</a>';
			if (this.Login) {
				s += '<li class="Overlined"><span>' + this.Login + '</span>';
				if (this.SessionAddress) {
					s += '<li title=' + this.SessionAddress + ' class="IP">' + this.SessionAddress;
				}
			}
		}
	}
	s += '</ul></div>';

	return s;
};

User.prototype.NameToString = function (name, has_access) {
	var color = this.StatusColor;

	var className = "";
	var cl = 1;
	if (this.BannedBy > 0) {
		className = "Banned";
		cl = 0;
	} else if (this.AwayMessage) {
		className = "Away";
		cl = 0;
	}

	if (!has_access && me.RoomId == this.RoomId) {
		className = "Requestor";
	}
	if (this.IgnoresYou) {
		className += " IgnoresMe";
	}

	var title = HtmlQuotes(name) + (this.AwayMessage ? " отсутствует	&laquo;" + this.AwayMessage + "&raquo;" : "");

	var s = '<li><span' + (this.IsIgnored ? ' class="Ignored"' : '') + '><a ' + voidHref + ' onclick="switchVisibility(\'_' + this.Id + '\')" ';
	s += ' ' + (cl ? ' style="color:' + color + '"' : '') + ' class="' + className + '" alt="' + title + '" title="' + title + '">' + name + '</a></span><br>';
	return s;
};

User.prototype.DisplayedName = function () {
	return this.Nickname ? this.Nickname : this.Login;
};

User.prototype.HasAccessTo = function (room) {
	if (!room.IsInvitationRequired || room.OwnerId == this.Id) {
		return true;
	}
	if (this.RoomId == room.Id && this.RoomIsPermitted != 0) {
		return true;
	}
	return false;
};

/* Helper methods */

var shownElement;
var hideTimer;
function Show(id) {
	if (shownElement) {
		if (shownElement != id) {
			HideDelayed();
		} else if (hideTimer) {
			clearTimeout(hideTimer);
			return;
		}
	}
	displayElement(id, true);
	shownElement = id;
};

function Hide() {
	hideTimer = setTimeout("HideDelayed()", 1000);
};

function HideDelayed() {
	displayElement(shownElement, false);
	shownElement = '';
};
"use strict";

//3.1
/*
	Displays inline confirmation window
	blocking all content behind and handling callback.
*/

function Confirm() {
	var Cover;
	var Holder;
	var callbackObject;

	var AlertType = false;
};

Confirm.prototype.Init = function (coverId, holderId) {
	this.Cover = $(getElement(coverId))[0];
	this.Holder = $(getElement(holderId))[0];

	this.Holder.className = "ConfirmContainer";
};

Confirm.prototype.Display = function (state) {
	displayElement(this.Holder, state);
	displayElement(this.Cover, state);
};

Confirm.prototype.SetBodyOverflow = function (state) {
	return;
	//	var b = d.documentElement ? d.documentElement : d.body;
	//	b.style.overflow = state ? "auto" : "hidden";
};

Confirm.prototype.Show = function (callback, title, message, customContent, keep_opened) {
	if (!this.Holder) {
		return false;
	} else {
		this.Holder.innerHTML = "";
	}

	this.SetBodyOverflow(0);

	title = title ? title : 'Confirmation';

	this.callbackObject = callback;
	this.KeepOpened = keep_opened;

	var h1 = d.createElement("h1");
	h1.innerHTML = title;
	this.Holder.appendChild(h1);

	var m = d.createElement("div");
	m.innerHTML = message;
	this.Holder.appendChild(m);

	if (customContent && customContent.CreateControls) {
		customContent.CreateControls(this.Holder);
		if (customContent.requestData) {
			customContent.requestData();
		}
	}

	var index = CheckEmpty(this.ButtonUrlIndex);
	var m1 = d.createElement("div");
	m1.className = "ConfirmButtons";
	if (this.AlertType) {
		m1.appendChild(MakeButton("ConfirmObject.Ok()", "ok_button" + index + ".gif"));
	} else {
		m1.appendChild(MakeButton("ConfirmObject.Ok()", "yes_button" + index + ".gif"));
		m1.appendChild(MakeButton("ConfirmObject.Cancel()", "no_button" + index + ".gif"));
	}

	this.Holder.appendChild(m1);

	this.Display(true);
	window.ConfirmObject = this;

	return false;
};

Confirm.prototype.Hide = function () {
	if (!this.Holder) {
		return;
	}
	this.Display(false);
	this.Holder.innerHTML = "";
	this.SetBodyOverflow(1);
};

Confirm.prototype.Cancel = function () {
	if (!this.Holder) {
		return;
	}
	this.Hide();
	this.callbackObject = null;
};

Confirm.prototype.Ok = function () {
	if (!this.Holder) {
		return;
	}
	if (!this.KeepOpened) {
		this.Hide();
	}
	if (this.callbackObject) {
		if (typeof this.callbackObject == 'function') {
			this.callbackObject();
		} else {
			if (this.callbackObject.click) {
				this.callbackObject.click();
			} else {
				location.href = this.callbackObject;
			}
		}
	}
};
"use strict";

//4.1
/*
	Tab class. Entity of Tabs one.
*/

/* Base tab class */
function TabBase() {};

TabBase.prototype.InitUploadFrame = function (property) {
	if (!property) {
		property = "UploadFrame";
	}
	if (!this[property]) {
		this[property] = CreateElement("iframe", "UploadFrame" + Math.round(100000 * Math.random()));
		this[property].className = "UploadFrame";
		if (this.RelatedDiv) {
			this.RelatedDiv.appendChild(this[property]);
		}
	}
};

/* obj - object to be assigned as a.obj (Tab by default) */
TabBase.prototype.AddSubmitButton = function (method, holder, obj) {
	var m1 = d.createElement("div");
	m1.className = "ConfirmButtons";
	this.SubmitButton = MakeButton(method, "ok_button.gif", obj ? obj : this, "", "Сохранить изменения");
	m1.appendChild(this.SubmitButton);
	//		alert(holder ? holder : "RelatedDiv");
	this[holder ? holder : "RelatedDiv"].appendChild(m1);
};

/* Tab object reaction by outside call */
TabBase.prototype.React = function (value) {
	if (this.Reactor) {
		this.Reactor.React(value);
	}
};

/* Sets additional className to RelatedDiv */
TabBase.prototype.SetAdditionalClass = function (className) {
	this.RelatedDiv.className = "TabContainer" + (className ? " " + className : "");
};

/* Tab class */
Tab.prototype = new TabBase();

function Tab(id, title, is_locked, is_private, on_select) {
	this.Id = id;
	this.Title = title;
	this.IsLocked = is_locked;
	this.IsPrivate = is_private;
	this.onSelect = on_select;

	this.UnreadMessages = 0;
	this.lastMessageId = -1;
	this.Alt = "";

	this.recepients = new Collection();
	if (this.IsPrivate) {
		this.recepients.Add(new Recepient(id, title, 1));
	}
};

Tab.prototype.ToString = function (index) {
	var isSelected = CurrentTab.Id == this.Id;
	if (isSelected) {
		CurrentTab = this;
	}
	this.DisplayDiv(isSelected);
	var s = "<li class='" + (isSelected ? "Selected " : "") + (this.UnreadMessages ? "HasUnread" : "") + "'" + (this.Alt ? " alt='" + this.Alt + "' title='" + this.Alt + "'" : "") + "><div>";
	if (isSelected) {
		s += "<span>" + this.Title + "</span>";
	} else {
		var action = "SwitchToTab(\"" + this.Id + "\")";
		s += "<a accesskey='" + (index + 1) + "'" + voidHref + " onclick='" + action + "' onfocus='" + action + "'>" + this.Title;
		if (this.UnreadMessages) {
			s += "(" + this.UnreadMessages + ")";
		}
		s += "</a>";
	}
	if (!this.IsLocked) {
		s += "<a " + voidHref + " onclick='CloseTab(\"" + this.Id + "\")' class='CloseSign'>x</a>";
	}
	s += "</div></li>";
	return s;
};

Tab.prototype.DisplayDiv = function (state) {
	DisplayElement(this.RelatedDiv, state);
	DisplayElement(this.TopicDiv, state);
};

Tab.prototype.Clear = function () {
	this.TopicDiv.innerHTML = '';
	this.RelatedDiv.innerHTML = '';
	this.lastMessageId = -1;
};

/*
	Tabs collection class.
*/

var CurrentTab;
function Tabs(tabsContainer, contentContainer) {
	this.TabsContainer = tabsContainer;
	this.ContentContainer = contentContainer;
	this.tabsCollection = new Collection();

	this.tabsList = document.createElement("ul");
	this.tabsList.className = "Tabs";
	this.TabsContainer.appendChild(this.tabsList);
};

Tabs.prototype.Print = function () {
	this.tabsList.innerHTML = this.tabsCollection.ToString();
};

Tabs.prototype.Add = function (tab, existing_container) {
	var topic = document.createElement("div");
	topic.className = "TabTopic";
	this.ContentContainer.appendChild(topic);
	tab.TopicDiv = topic;

	if (!existing_container) {
		existing_container = document.createElement("div");
		existing_container.className = "TabContainer";
		this.ContentContainer.appendChild(existing_container);
	}
	tab.RelatedDiv = existing_container;

	this.tabsCollection.Add(tab);
	tab.DisplayDiv(false);
};

Tabs.prototype.Delete = function (id) {
	var tab = this.tabsCollection.Get(id);
	if (tab) {
		this.ContentContainer.removeChild(tab.TopicDiv);
		this.ContentContainer.removeChild(tab.RelatedDiv);
		this.tabsCollection.Delete(id);
	}
};

Tabs.prototype.PrintTo = function (id, text) {
	var tab = this.tabsCollection.Get(id);
	if (tab && tab.RelatedDiv) {
		tab.RelatedDiv.innerHTML = text;
	}
};

/* Service functions */

function SwitchToTab(id) {
	var tab = tabs.tabsCollection.Get(id);
	if (tab) {
		CurrentTab = tab;
		tab.UnreadMessages = 0;
		tabs.Print();
		recepients = tab.recepients;
		if (window.ShowRecepients) {
			ShowRecepients();
		}
		if (tab.onSelect) {
			tab.RelatedDiv.innerHTML = LoadingIndicator;
			tab.onSelect(tab);
			tab.onSelect = ""; /* TODO: Treat failure */
		}
	}
	if (AdjustDivs) {
		AdjustDivs();
	}
}

function CloseTab(id) {
	var tab = tabs.tabsCollection.Get(id);
	if (tab) {
		tabs.Delete(id);
		SwitchToTab(MainTab.Id);
		tabs.Print();
	}
}
"use strict";

//1.5
/*
    Recepient class.

*/
function Recepient(id, title, is_locked) {
    this.Id = id;
    this.Title = title;
    this.IsLocked = is_locked;
};

Recepient.prototype.ToString = function (index) {
    var s = (index ? ", " : "") + this.Title;
    if (!this.IsLocked) {
        s += "<a " + voidHref + " onclick='DR(" + this.Id + ")'>x</a>";
    }
    return s;
};

Recepient.prototype.Gather = function (index) {
    return (index ? "," : "") + this.Id;
};
"use strict";

//1.8
/*
    Replaces #pvt#, #info# and #add# chunks with
    proper links
*/

function MakePrivateLink(id, name) {
    s = "<a " + voidHref + " onclick=\"AR(" + id + ",'" + StrongHtmlQuotes(Slash(name)) + "')\">#</a>";
    //  alert(s);
    return s;
};

function MakeLink(name) {
    return "<a " + voidHref + " onclick=\"__(this)\">" + StrongHtmlQuotes(name) + "</a>";
};

function MakeInfoLink(id, name) {
    return "<a " + voidHref + " onclick=\"Info('" + id + "')\">" + StrongHtmlQuotes(name) + "</a>";
};

function GetUserStyle(id) {
    if (users) {
        var user = users.Get(id);
        if (user && user.Settings.Font && user.Settings.Font.ToCSS) {
            return "style='" + user.Settings.Font.ToCSS(me) + "'";
        }
    }
    return "";
}

var ReplaceSmiles = new RegExp("\\*([0-9a-z]+)\\*", "gim");
function MakeSmiles(text) {
    return text.replace(ReplaceSmiles, "<img src=\"/img/smiles/$1.gif\" border=\"0\" />");
};

function Format(text, person_id, person_name) {
    text = text.replace("#style#", GetUserStyle(person_id));
    text = text.replace("#info#", MakeInfoLink(person_id, person_name));
    text = text.replace("#pvt#", MakePrivateLink(person_id, person_name));
    text = text.replace("#add#", MakeLink(person_name));
    return MakeSmiles(text);
};
"use strict";

//2.0
/*
	Opens pop-up wakeup windows with messages.
*/

var wakeups = new Collection();
var WakeupsHolder;
var MaxShownWakeups = 10;
var ReceivedWakeups = 0;

function Wakeup(id, sender, reset) {
	if (!me) {
		return;
	}
	if (me.Settings.ReceiveWakeups) {
		ShowWakeup(id);
	} else {
		wakeups.Add(new WakeupMessage(id, sender));
	}
};

function ShowWakeup(id, remove) {
	var wakeupWindow = window.open("wakeup.php?id=" + id, "wakeup" + id, "width=500,height=300,toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=1");
	if (remove) {
		wakeups.Delete(id);
		PrintWakeups();
	}
};

function PrintWakeups() {
	if (!WakeupsHolder) {
		WakeupsHolder = $("#Wakeups");
	}
	if (WakeupsHolder) {
		ReceivedWakeups = wakeups.Count();
		if (ReceivedWakeups > 0) {
			WakeupsHolder.innerHTML = "<h3>Вейкапы	<span class='Count'>(" + ReceivedWakeups + ")</span>:</h3>";
			wakeups.ToString(WakeupsHolder);
			DisplayElement(WakeupsHolder, true);
		} else {
			DisplayElement(WakeupsHolder, false);
		}
	}
};

function WakeupMessage(id, sender) {
	this.Id = id;
	this.Sender = sender;
};

WakeupMessage.prototype.ToString = function (holder, i) {
	if (i == MaxShownWakeups) {
		holder.appendChild(d.createTextNode(",	и ещё " + (ReceivedWakeups - MaxShownWakeups)));
		return;
	} else if (i > MaxShownWakeups) {
		return;
	}
	var a = d.createElement("a");
	a.innerHTML = this.Sender;
	a.href = voidLink;
	a.wId = this.Id;
	a.onclick = function () {
		ShowWakeup(this.wId, 1);
	};
	if (i) {
		holder.appendChild(d.createTextNode(",	"));
	}
	holder.appendChild(a);
};
"use strict";

//1.1
/*
    Info PopUp helper
*/

var infoPopUp;
function Info(id) {
    infoPopUp = open("/user/" + id + ".html", "info", "width=550,height=600,toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=1");
}
"use strict";

//2.2
/*
    Menu items
*/

var menuItemWidth = 100;
var menuItemHeight = 20;

function MenuItem(id, title, action, is_locked) {
    this.Id = id;
    this.Title = title;
    this.Action = action;
    this.IsLocked = is_locked;
    this.SubItems = new MenuItemsCollection();
};

MenuItem.prototype.Gather = function (holder) {
    var a = d.createElement('a');
    a.innerHTML = this.Title;
    a.href = voidLink;
    if (this.Action) {
        eval("a.onclick = function() {" + this.Action + ";}");
    }

    var li = document.createElement("li");
    li.RelatedItem = this;
    li.onmouseover = function () {
        DisplaySubmenu(this, true);
    };
    li.onmouseout = function () {
        DisplaySubmenu(this, false);
    };
    li.onclick = li.onmouseout;

    if (this.SubItems.Items.Count() > 0) {
        this.SubItems.Create(li);
    }

    li.appendChild(a);
    holder.appendChild(li);
};

function MenuItemsCollection(shown) {
    this.Items = new Collection();
    this.Container = document.createElement("ul");
    if (!shown) {
        DisplayElement(this.Container, false);
    }
};

MenuItemsCollection.prototype.Create = function (where) {
    this.Container.innerHTML = "";
    if (this.Items.Count() > 0) {
        this.Items.Gather(this.Container);
        where.appendChild(this.Container);
    }
};

MenuItemsCollection.prototype.Display = function (state) {
    DisplayElement(this.Container, state);
};

function DisplaySubmenu(el, state, force) {
    if (el.RelatedItem && el.RelatedItem.SubItems) {
        el.RelatedItem.SubItems.Display(state);
        el.className = state ? "Selected" : "";
    }
};
"use strict";

//1.3
/*
    Displays messages on given container.
*/

function Alerts(container) {
    this.Holder = container;
    this.Holder.className = "AlertsHolder";
    this.Container = RoundCorners(container, "orange");
    this.Clear();
};

Alerts.prototype.Add = function (message, isError) {
    displayElement(this.Holder, true);
    this.Container.innerHTML += "<p class='" + (isError ? "Error" : "") + "'>" + message + "</p>";
    this.HasErrors = this.HasErrors || isError;
    this.IsEmpty = 0;
};

Alerts.prototype.Clear = function () {
    displayElement(this.Holder, false);
    this.Container.innerHTML = "";
    this.HasErrors = 0;
    this.IsEmpty = 1;
};
"use strict";

//2.7
/*
	Custom confirm contents.
*/

/* Nickname class */

var nicknames = new Collection();
var nicknames1;
var max_names = 5;
var name_length = 20;

function Nickname(index, id, name) {
	this.Index = index;
	this.Id = id;
	this.OldName = name;
	this.Name = name;
	this.Mode = "show";
};

Nickname.prototype.IsEmpty = function () {
	return this.Name == "";
};

Nickname.prototype.HasChanged = function () {
	return this.OldName != this.Name;
};

Nickname.prototype.CreateButton = function (src, action) {
	var button = d.createElement("input");
	button.type = "image";
	button.RelatedItem = this;
	eval("button.onclick = function(){" + action + "}");
	button.className = "Button";
	button.style.width = "15px";
	button.style.height = "15px";
	button.src = imagesPath + src;
	return button;
};

Nickname.prototype.CreateViewControls = function () {
	this.Div.innerHTML = "";
	if (this.Mode == "show") {
		this.Div.innerHTML += (this.Name ? this.Name + (this.Name == me.Login ? "&nbsp;(ваш логин)" : "") : "&lt;не задано&gt;") + "&nbsp;";
		if (this.Id) {
			this.Div.appendChild(this.CreateButton("edit_icon.gif", "Edit(this)"));
			if (this.Name) {
				this.Div.appendChild(this.CreateButton("delete_icon.gif", "Clear(this)"));
			}
		}
	} else {
		this.Input = d.createElement("input");
		this.Input.className = "NewNick";
		this.Input.value = this.Name;
		this.Input.setAttribute("maxlength", name_length);
		this.Div.appendChild(this.Input);
		if (this.Id) {
			this.Div.appendChild(this.CreateButton("icons/done.gif", "StopEditing(true)"));
			this.Div.appendChild(this.CreateButton("icons/cancel.gif", "StopEditing(false)"));
		}
	}
};

Nickname.prototype.ToString = function (holder) {
	if (!this.Li) {
		this.Li = d.createElement("li");
	} else {
		this.Li.innerHTML = "";
	}
	this.Radio = CreateRadio("nickname", !me.Nickname && this.Name == me.Login || me.Nickname && this.Name == me.Nickname);
	this.Radio.RelatedItem = this;
	eval("this.Radio.onclick = function(){Select(this)}");

	this.Li.appendChild(this.Radio);
	this.Div = d.createElement("span");

	this.CreateViewControls();

	this.Li.appendChild(this.Div);
	holder.appendChild(this.Li);
};

Nickname.prototype.Gather = function (index) {
	var s = "";
	s += MakeParametersPair("id" + index, this.Id > 0 ? this.Id : "");
	s += MakeParametersPair("name" + index, this.Name);
	if (this.Radio.checked) {
		s += MakeParametersPair("selected", index);
	}
	return s;
};

/* Change Nickname class */

function ChangeNickname() {};

ChangeNickname.prototype.CreateControls = function (container) {
	this.Holder = d.createElement("ul");
	this.Holder.className = "NamesList";

	this.Holder.innerHTML = LoadingIndicator;

	container.appendChild(this.Holder);

	this.Status = d.createElement("div");
	this.Status.className = "Status";
	container.appendChild(this.Status);
	nicknames1 = this;
};

ChangeNickname.prototype.RequestData = function () {
	sendRequest(servicesPath + "nickname.service.php", NamesResponse, "");
};

function NamesResponse(responseText) {
	if (nicknames1.Holder) {

		nicknames.Clear();
		nicknames.Add(new Nickname(0, 0, me.Login));

		try {
			eval(responseText);
		} catch (e) {}
		for (var i = nicknames.Count(); i <= max_names; i++) {
			nicknames.Add(new Nickname(i + 1, -(i + 1), ""));
		}
		if (NewNickname != "-1") {
			me.Nickname = NewNickname;
			if (PrintRooms) {
				PrintRooms();
			}
		}
		nicknames1.Holder.innerHTML = "";
		nicknames.ToString(nicknames1.Holder);
	}
};

var activeItem;
function Select(e) {
	if (e.RelatedItem) {
		StopEditing(true);
		var item = e.RelatedItem;
		if (item.IsEmpty()) {
			Edit(e);
		}
	}
};

function Edit(e) {
	if (e.RelatedItem) {
		StopEditing(true);
		var item = e.RelatedItem;
		item.Mode = "edit";
		item.CreateViewControls();
		item.Input.focus();
		activeItem = item;
	}
};

function Clear(e) {
	if (e.RelatedItem) {
		var item = e.RelatedItem;
		item.Name = "";
		item.CreateViewControls();
	}
};

function StopEditing(acceptChanges) {
	if (activeItem) {
		activeItem.Mode = "show";
		if (acceptChanges) {
			activeItem.Name = activeItem.Input.value;
		}
		activeItem.CreateViewControls();
	}
};

var nicknameSaving = 0;
function SaveNicknameChanges() {
	if (nicknameSaving) {
		return;
	}
	StopEditing(true);
	nicknameSaving = 1;
	setTimeout("UnLockSaving()", 10000);
	sendRequest(servicesPath + "nickname.service.php", SavingResults, nicknames.Gather());
};

function UnLockSaving() {
	nicknameSaving = 0;
};

function SavingResults(req) {
	UnLockSaving();
	status = "";
	NamesResponse(req);
	if (!status) {
		SetStatus("Изменения сохранены.");
		setTimeout("co.Hide()", 2000);
	}
	ForcePing();
};

var status;
function SetStatus(text) {
	nicknames1.Status.innerHTML = text;
	status = text;
};
"use strict";

//6.8
/*
	User profile data & helper methods
*/

function Profile() {
	this.fields = new Array("LOGIN", "EMAIL", "NAME", "GENDER", "BIRTHDAY", "CITY", "ICQ", "URL", "PHOTO", "AVATAR", "ABOUT", "REGISTERED", "LAST_VISIT");
	this.ServicePath = servicesPath + "profile.service.php";
	this.Template = "userdata";
	this.ClassName = "Profile"; // Optimize?
};

Profile.prototype = new OptionsBase();

Profile.prototype.Gather = function () {
	var result = this.BaseGather();
	result += this.GatherOne("PASSWORD");
	result += this.GatherOne("PASSWORD_CONFIRM");
	result += this.GatherOne("BANNED");
	return result;
};

Profile.prototype.Bind = function () {
	this.BaseBind();

	/* Bind images */
	DisplayElement(this.Inputs["liDeletePhoto"], this.PHOTO);
	DisplayElement(this.Inputs["liDeleteAvatar"], this.AVATAR);

	this.BindImage(this.Photo1);
	this.BindImage(this.Avatar1);

	/* Ban Status */
	this.SetTabElementValue("BANNED", this.BANNED_BY > 0);
	this.DisplayTabElement("BanDetails", !this.ADMIN && this.BANNED_BY > 0);

	var ban_status = "";
	if (this.ADMIN) {
		ban_status += "Пользователь забанен администратором&nbsp;<b>" + this.ADMIN + "</b>";
		ban_status += "&nbsp;" + (this.BANNED_TILL ? "до " + this.BANNED_TILL : "бессрочно");
		if (this.BAN_REASON) {
			ban_status += "&nbsp;по причине&nbsp;&laquo;" + this.BAN_REASON + "&raquo;";
		}
	}
	this.SetTabElementValue("BanStatus", ban_status);

	// Correct dates
	this.UpdateToPrintableDate("REGISTERED");
	this.UpdateToPrintableDate("LAST_VISIT");
};

Profile.prototype.BindImage = function (img) {
	if (this[img.Field]) {
		this.ReloadImage(img);
	} else {
		img.ImageObject = "";
		this.PrintLoadedImage(img);
	}
};

Profile.prototype.CheckPhoto = function (img) {
	if (!img.HasImage()) {
		img.ImageObject = d.createElement("img");
		img.ImageObject.Profile = this;
		img.ImageObject.Img = img;
		img.ImageObject.onload = function () {
			ImageLoaded(this);
		};
	}
};

Profile.prototype.ReloadImage = function (img) {
	if (!this.Tab.Alerts.HasErrors) {
		this.SetTabElementValue(img.Container, LoadingIndicator);
		this.CheckPhoto(img);
		img.ImageObject.src = img.Path + this[img.Field] + "?" + Math.random(100);
		if (this.Inputs[img.UploadForm]) {
			this.Inputs[img.UploadForm].reset();
		}
	}
};

Profile.prototype.PrintLoadedImage = function (img) {
	var p = this.Inputs[img.Container],
	    result = "не загружено";
	if (p) {
		if (img.ImageObject) {
			var dim = "width='" + img.MaxWidth + "'";
			if (img.ImageObject.width < img.MaxWidth) {
				dim = "width='" + img.ImageObject.width + "' height='" + img.ImageObject.height + "'";
			}
			result = "<img class='Photo' src='" + img.ImageObject.src + "' " + dim + ">";
		}
		p.innerHTML = result;
	}
};

Profile.prototype.RequestCallback = function (req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind();
		obj.Initialized = false;
	}
};

function ImageLoaded(e) {
	e.Profile.PrintLoadedImage(e.Img);
};

// Loading template
Profile.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);

	/* Init images (photo & avatar) */
	this.Tab.InitUploadFrame("AvatarUploadFrame");

	/* Assign Tab to links */
	this.Photo1 = new Img("PHOTO", "Photo", "uploadForm", this.Tab.UploadFrame, userPhotosPath, 300);
	this.Avatar1 = new Img("AVATAR", "Avatar", "avatarUploadForm", this.Tab.AvatarUploadFrame, avatarsPath, 120);

	this.GroupTabAssign(["linkDeletePhoto", "linkDeleteAvatar", "BANNED", "PASSWORD"]);
	this.GroupSelfAssign(["linkRefresh", "linkLockIP"]);

	/* Viewing my profile hide Status & Ban sections */
	if (this.USER_ID == me.Id) {
		this.DisplayTabElement("NotForMe", false);
	}

	/* Date pickers */
	new DatePicker(this.Inputs["BIRTHDAY"]);
	new DatePicker(this.Inputs["BANNED_TILL"], 1);

	/* OpenIDs associated with this user */
	var oid = new Spoiler(1, "OpenID", 0, 0, function (tab) {
		new OpenIds().LoadTemplate(tab, this.USER_ID);
	});
	oid.USER_ID = this.USER_ID;
	oid.ToString(this.Inputs["OpenIds"]);

	/* Admin comments spoiler */
	if (me.IsAdmin()) {
		var acs = new Spoiler(2, "Комментарии администраторов	&	логи", 0, 0, function (tab) {
			new AdminComments().LoadTemplate(tab, this.USER_ID);
		});
		acs.USER_ID = this.USER_ID;
		acs.ToString(this.Inputs["AdminComments"]);
	}

	/* Submit button */
	this.Tab.AddSubmitButton("SaveProfile(this)", "", this);
};

/* Save profile */

function UploadImage(profile, img) {
	var form = profile.Inputs[img.UploadForm];
	if (form) {
		var p = form["PHOTO1"];
		if (p && p.value) {
			form["tab_id"].value = profile.Tab.Id;
			form["USER_ID"].value = profile.USER_ID;
			form.target = img.Frame.name;
			form.submit();
		}
	}
};

function SaveProfile(a) {
	if (a.obj) {
		a.obj.Tab.Alerts.Clear();

		/* Saving Photo & Avatar */
		UploadImage(a.obj, a.obj.Photo1);
		UploadImage(a.obj, a.obj.Avatar1);

		/* Saving profile */
		a.obj.Save(ProfileSaved);
	}
};

function ProfileSaved(responseText, obj) {
	if (obj && responseText) {
		var tabObject = obj.Tab;
		obj.RequestCallback(responseText, obj);

		// Refresh admin comments
		obj.FindRelatedControls(true);
		DoClick(obj.Inputs["RefreshAdminComments"]);
	}
};

/* Links actions */

function DeletePhotoConfirmed(a, image) {
	if (a.Tab) {
		a.Tab.Alerts.Clear();
		a.Tab.Profile.Request(MakeParametersPair("go", "delete_" + image));
	}
};

function ShowBanDetails(cb) {
	if (cb.Tab) {
		cb.Tab.Profile.DisplayTabElement("BanDetails", !cb.Tab.Profile.ADMIN && cb.checked);
	}
};

function RestoreInput(el, relatedBlockId) {
	var tab = el.Tab;
	if (!tab) {
		return;
	}
	if (el.value != el.previousValue) {
		tab.Profile.DisplayTabElement(relatedBlockId, el.value);
	}
	if (!el.value) {
		el.value = empty_pass;
	}
};

/* Profile Image helper class */

function Img(field, container, form, frame, path, max_width) {
	this.Field = field;
	this.Container = container;
	this.UploadForm = form;
	this.Frame = frame;
	this.Path = path;
	this.MaxWidth = max_width;
};

Img.prototype.HasImage = function () {
	return this.ImageObject;
};

/* Confirms */

function DeletePhoto(a) {
	co.Show(function () {
		DeletePhotoConfirmed(a, "photo");
	}, "Удалить фотографию?", "Фотография пользователя будет удалена из профиля.<br>Вы уверены?");
};

function DeleteAvatar(a) {
	co.Show(function () {
		DeletePhotoConfirmed(a, "avatar");
	}, "Удалить аватар?", "Автар будет удален из профиля.<br>Вы уверены?");
};
"use strict";

//3.7
/*
    Represents settings entity on client-side.
*/

function Settings(status, ignore_colors, ignore_sizes, ignore_fonts, ignore_styles, receive_wakes, frameset, font) {
    // Properties
    this.Status = status;
    this.IgnoreColors = ignore_colors;
    this.IgnoreSizes = ignore_sizes;
    this.IgnoreFonts = ignore_fonts;
    this.IgnoreStyles = ignore_styles;
    this.ReceiveWakeups = receive_wakes;
    this.Frameset = frameset;

    this.Font = font;

    this.fields = new Array("LOGIN", "STATUS", "IGNORE_COLORS", "IGNORE_FONT_SIZE", "IGNORE_FONTS", "IGNORE_FONT_STYLE", "RECEIVE_WAKEUPS", "FRAMESET", "ENTER_MESSAGE", "QUIT_MESSAGE", "FONT_COLOR", "FONT_SIZE", "FONT_FACE", "FONT_BOLD", "FONT_ITALIC", "FONT_UNDERLINED");
    this.ServicePath = servicesPath + "settings.service.php";
    this.Template = "usersettings";
    this.ClassName = "Settings";
};

Settings.prototype = new OptionsBase();

// Methods
Settings.prototype.CheckSum = function () {
    var cs = CheckSum(this.Status);
    cs += CheckSum(this.IgnoreColors);
    cs += CheckSum(this.IgnoreSizes);
    cs += CheckSum(this.IgnoreFonts);
    cs += CheckSum(this.IgnoreStyles);
    cs += CheckSum(this.ReceiveWakeups);
    cs += CheckSum(this.Frameset);

    if (this.Font && this.Font.CheckSum) {
        cs += this.Font.CheckSum();
    }

    return cs;
};

Settings.prototype.Bind = function () {
    this.BaseBind();
    UpdateFontView();
};

Settings.prototype.requestCallback = function (req) {
    this.requestBaseCallback(req);
    this.Bind();
};

Settings.prototype.TemplateLoaded = function (req) {
    this.TemplateBaseLoaded(req);
    this.AssignSelfTo("linkRefresh");

    // Create Font object
    font = new Font();
    font.inputs = this.inputs;
    setTimeout("UpdateFontView()", 1000); // Set delay for IE

    // Init ColorPicker
    //  var cp = new ColorPicker("FONT_COLOR");
    new ColorPicker("FONT_COLOR");

    /* Submit button */
    this.Tab.AddSubmitButton("SaveObject(this)", "", this);
};

/* Links actions */

function RefreshSettings(a) {
    if (a.Tab) {
        a.Tab.Alerts.Clear();
        a.Tab.Settings.request();
    }
};

var font;
function UpdateFontView() {
    if (font && font.inputs) {
        var el = font.inputs["fontExample"];
        if (el) {
            font.Gather();
            font.ApplyTo(el);
        }
    }
};
"use strict";

//3.1
/*
    Rounded div corners based on CSS3
*/

function RoundCorners(el, color, css) {
    if (!el.tagName) {
        el = $(getElement(id))[0];
    }
    if (!el) {
        return;
    }

    var div = d.createElement("div");
    div.className = "RoundedCorners AlertsContainer" + (css ? " " + css : "");
    if (color) {
        div.style.backgroundColor = color;
    }
    el.appendChild(div);
    return div;
};
"use strict";

//2.4
/*
	Allows color selection with mouse click
*/

var bw = new Array("0", "2", "4", "6", "8", "A", "C", "D", "E", "F");
var ones = new Array("19", "33", "4C", "66", "99", "99", "99", "B2", "CC", "E5");
var twos = new Array("33", "66", "99", "CC", "E5", "FF", "33", "66", "99", "CC");
var line = new Array("_002", "_012", "_022", "_021", "_020", "_120", "_220", "_210", "_200", "_201", "_202", "_102");

function ColorPicker(input) {
	this.Visible = false;
	if (input.tagName) {
		this.Input = input;
	} else {
		this.Input = $("#" + input)[0];
	}
	if (this.Input) {
		this.Input.className = "Color";
		this.Input.type = "color";

		this.Table = d.createElement("table");
		displayElement(this.Table, false);
		var t = d.createElement("tbody");
		this.Table.appendChild(t);
		this.Table.className = "ColorPicker";
		this.Table.obj = this;
		this.Table.onclick = function () {
			this.obj.ColorSelected();
		};
		//this.Table.onmouseout = function() {this.obj.switchVisibility()};

		for (var i = 0, l = bw.length; i < l; i++) {
			this.MakeRow(i);
		}
		insertAfter(this.Table, this.Input);
		insertAfter(MakeButton("SwitchPicker(this)", "icons/palette.gif", this, "PickerButton", "Выбрать цвет"), this.Input);
	}
};

ColorPicker.prototype.MakeCell = function (row, i, color) {
	var cell = row.insertCell(i);
	cell.style.backgroundColor = color;
	cell.Color = color;
	cell.onclick = function () {
		pc(this);
	};
};

ColorPicker.prototype.MakeRow = function (index) {
	var row = this.Table.insertRow(index);
	this.MakeCell(row, 0, "#" + bw[index] + bw[index] + bw[index] + bw[index] + bw[index] + bw[index]);

	var zero = "00";
	if (index > 5) {
		zero = "FF";
	}
	for (var i = 0, l = line.length; i < l; i++) {
		var color = "#";
		var rgb = line[i];
		for (var j = 1; j <= 3; j++) {
			var comp = zero == "FF" ? 2 - 1 * rgb.charAt(j) : 1 * rgb.charAt(j);
			color += comp ? comp == 1 ? ones[index] : twos[index] : zero;
		}
		this.MakeCell(row, i + 1, color);
	}
};

ColorPicker.prototype.switchVisibility = function () {
	this.Visible = !this.Visible;
	displayElement(this.Table, this.Visible);
};

ColorPicker.prototype.ColorSelected = function () {
	this.Input.value = this.Table.SelectedColor;
	this.switchVisibility();
	if (top.UpdateFontView) {
		UpdateFontView();
	}
};

function SwitchPicker(a) {
	if (a && a.obj) {
		a.obj.switchVisibility();
	}
};

function pc(td) {
	var table = td.parentNode.parentNode.parentNode;
	table.SelectedColor = td.Color;
};
"use strict";

//6.3
/*
    Base class for grids with[out] pager.
*/

/* Grid class */

function Grid() {
    this.GridId = "Grid";
    this.Columns = 3;
    this.CurrentContent = [];
    this.HasEmptyRow = false;
};

Grid.prototype = new OptionsBase();

Grid.prototype.GatherDTO = function (dto) {
    var l = this.fields.length;
    if (dto.fields.length < l) {
        l = dto.fields.length;
    }
    for (var i = 0; i < l; i++) {
        this[this.fields[i]] = dto[dto.fields[i]];
    }
    return true;
};

Grid.prototype.FindTableBase = function () {
    if (this.Tbody) {
        return true;
    }
    this.FindRelatedControls();

    if (!this.inputs) {
        return false;
    }

    var el = this.inputs[this.GridId];
    if (el) {
        this.Tbody = el.firstChild;
    }
    return false;
};

Grid.prototype.FindTable = function () {
    // Method to override
    return this.FindTableBase();
};

Grid.prototype.AddItem = function (dto) {
    this.CurrentContent[this.CurrentContent.length] = dto;
};

Grid.prototype.ClearRecords = function (show_indicator) {
    this.FindTable();

    if (!this.Tbody) {
        return false;
    }

    for (var i = 1, l = this.Tbody.childNodes.length; i < l; i++) {
        var e = this.Tbody.childNodes[l - i];
        this.Tbody.removeChild(e);
    }

    if (show_indicator) {
        var tr = d.createElement("tr");

        var td = d.createElement("td");
        td.colSpan = this.Columns;
        td.innerHTML = loadingIndicator;

        tr.appendChild(td);
        this.Tbody.appendChild(td);
    }
    this.CurrentContent = [];
    this.HasEmptyRow = false;
};

Grid.prototype.request = function (params, callback) {
    this.ClearRecords(true);
    this.BaseRequest(params, callback);
    this.HasEmptyRow = false;
};

Grid.prototype.DoBind = function (content) {
    if (!this.Tbody) {
        return false;
    }

    this.BaseBind();
    this.ClearRecords();
    for (var i = 0, l = content.length; i < l; i++) {
        this.Tbody.appendChild(content[i].ToString(i, this, this.Tbody));
        content[i].Grid = this;
    }
    this.CurrentContent = content;
};

Grid.prototype.Bind = function (content) {
    return this.DoBind(content);
};

Grid.prototype.Refresh = function () {
    var content = this.CurrentContent;
    return this.DoBind(content);
};

/* --------------- Paged Grid class --------------- */

function PagedGrid() {
    this.PerPage = 10;
    this.PagerId = "Pager";
};

PagedGrid.prototype = new Grid();

PagedGrid.prototype.InitPager = function () {
    // Method to override
    this.Pager = new Pager(this.inputs[this.PagerId], function () {}, this.PerPage);
    this.Tab.Pager = this.Pager;
};

PagedGrid.prototype.FindTable = function () {
    if (!this.FindTableBase()) {
        this.InitPager();
        this.Pager.Tab = this.Tab;
    }
};

PagedGrid.prototype.Bind = function (content, total) {
    this.Pager.Total = total;
    this.DoBind(content);
    this.Pager.Print();
};

PagedGrid.prototype.request = function (params, callback) {
    this.ClearRecords(true);
    var s = new ParamsBuilder((params || '') + this.Gather());
    s.add('from', this.Pager.Offset());
    s.add('amount', this.Pager.PerPage);
    this.BaseRequest(s.build(), callback);
};

PagedGrid.prototype.SwitchPage = function () {
    this.request();
};

/* --------------- Editable Grid class --------------- */

function EditableGrid() {};

EditableGrid.prototype = new Grid();

EditableGrid.prototype.Edit = __edit;
EditableGrid.prototype.CancelEditing = __cancelEditing;
EditableGrid.prototype.AddRow = __addRow;

/* --------------- Editable Paged Grid class --------------- */

function EditablePagedGrid() {};

EditablePagedGrid.prototype = new PagedGrid();

EditablePagedGrid.prototype.Edit = __edit;
EditablePagedGrid.prototype.CancelEditing = __cancelEditing;
EditablePagedGrid.prototype.AddRow = __addRow;

/* Editable grid helper methods */

function __edit(id) {
    if (!this.CurrentContent) {
        return;
    }
    for (var i = 0, l = this.CurrentContent.length; i < l; i++) {
        this.CurrentContent[i].EditView = this.CurrentContent[i].Id == id ? 1 : 0;
    }
    this.Refresh();
};

function __cancelEditing() {
    if (this.CurrentContent && this.CurrentContent.length && this.CurrentContent[this.CurrentContent.length - 1].Id == 0) {
        this.CurrentContent.pop();
    }
    this.Edit("");
};

function __addRow(dto) {
    if (!this.HasEmptyRow) {
        this.AddItem(dto);
        this.DoBind(this.CurrentContent);
        this.Edit(0);
        this.HasEmptyRow = true;
    }
};

/* Helper methods */

function MakeGridRow(index) {
    var tr = d.createElement("tr");
    if (index % 2) {
        tr.className = " Dark";
    }
    if (this.IsHidden) {
        tr.className += " Hidden";
    }
    return tr;
};

function MakeGridSubHeader(index, cols, text) {
    var trd = MakeGridRow(index);
    trd.className = "Sub";
    var td0 = d.createElement("th");
    td0.colSpan = cols;
    td0.innerHTML = text;
    trd.appendChild(td0);
    return trd;
};
"use strict";

//6.5
/*
	User Manager admin functionality
*/

function Userman() {
	this.fields = ["BY_NAME", "BY_ROOM", "FILTER_BANNED", "FILTER_EXPIRED", "FILTER_TODAY", "FILTER_YESTERDAY", "FILTER_REGDATE", "REG_DATE"];
	this.ServicePath = servicesPath + "users.service.php";
	this.Template = "userman";
	this.ClassName = "Userman";

	this.GridId = "UsersContainer";
	this.Columns = 2;
};

Userman.prototype = new Grid();

Userman.prototype.BaseBind = function () {};

Userman.prototype.requestCallback = function (req) {
	this.more = 0;
	this.requestBaseCallback(req);
	this.Bind(this.data);
	if (this.more) {
		this.Tab.Alerts.Add("Более	20	результатов	-	уточните критерий поиска.", 1);
	} else {
		if (_.isEmpty(this.data) && userSearched) this.Tab.Alerts.Add("Пользователи не найдены.");
	}
};

Userman.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);

	var assignee = ["BY_NAME", "BY_ROOM", "FILTER_BANNED", "FILTER_EXPIRED", "FILTER_TODAY", "FILTER_YESTERDAY", "FILTER_REGDATE", "REG_DATE"];
	for (var i = 0, l = assignee.length; i < l; i++) {
		var el = this.inputs[assignee[i]];
		if (el) {
			var a = new delayedRequestor(this, el);
			a.GetParams = GatherUsersParameters;
		}
	}

	BindRooms(this.inputs["BY_ROOM"]);
	new DatePicker(this.inputs["REG_DATE"]);
};

/* User DTO */

function udto(id, login, nickname) {
	this.fields = ["Id", "Login", "Nickname"];
	this.Init(arguments);
};

udto.prototype = new DTO();

udto.prototype.MakeTitle = function () {
	return this.Login + (this.Nickname ? "&nbsp;(" + this.Nickname + ")" : "");
};

udto.prototype.ToString = function (index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
	var name = this.MakeTitle();
	td1.appendChild(umDisplayName(this, name, td1, obj));
	tr.appendChild(td1);
	if (me.IsAdmin()) {
		td2 = d.createElement("td");
		cb = CreateBitInput("usr" + this.Id, 0);
		td2.appendChild(cb);
		tr.appendChild(td2);
	} else {
		td1.colSpan = 2;
	}
	return tr;
};

udto.prototype.ToLiString = function (index, obj, callbackObj) {
	this.callbackObj = callbackObj;

	var li = d.createElement("li");

	var a = d.createElement("a");
	a.Obj = this;
	a.href = voidLink;
	a.onclick = function () {
		this.Obj.Select();
	};
	a.innerHTML = this.MakeTitle();

	li.appendChild(a);
	return li;
};

udto.prototype.Select = function () {
	if (this.callbackObj) {
		this.callbackObj.Select(this);
	};
};

/* Helper methods */

function umDisplayName(userDTO, name, td, obj) {
	var a = d.createElement("a");
	a.innerHTML = name;
	a.href = voidLink;
	a.onclick = function () {
		showUserMenu(this, userDTO.Id, userDTO.Login, td, obj);
	};
	a.className = "Closed";
	return a;
};

var userMenu;
function showUserMenu(a, id, login, container, obj) {
	a.blur();
	if (HideUserMenu(id)) {
		return;
	};

	userMenu = d.createElement("table");
	userMenu.className = "UserMenu";
	userMenu.Container = container;
	userMenu.Id = id;
	userMenu.onclick = HideUserMenu;
	userMenu.Link = a;

	var tr = d.createElement("tr");
	if (me.IsAdmin()) {
		umExtraButtons(tr, id, login, obj);
	};
	userMenu.appendChild(tr);

	insertAfter(userMenu, a);
	a.className = "Opened";
};

function HideUserMenu(id) {
	if (userMenu) {
		userMenu.Link.className = "Closed";
		var i = userMenu.Id;
		userMenu.Container.removeChild(userMenu);
		userMenu = "";
		return id == i;
	}
	return false;
};

function makeSection(title, className) {
	var td = d.createElement("td");
	var h3 = d.createElement("h4");
	if (className) {
		h3.className = className;
	}
	h3.innerHTML = title;
	td.appendChild(h3);
	return td;
};

function makeUserMenuLink(el) {
	var li = d.createElement("li");
	li.appendChild(el);
	return li;
};

function GatherUsersParameters() {
	var s = new ParamsBuilder(this.obj.Gather());
	s.add('type', this.Input.name);
	return s.build();
};
"use strict";

//3.0
/*
    Spoiler class.
    Displays/hides logical piece of content on demand.
*/

function Spoiler(id, title, height, is_opened, on_select) {
    this.Id = id;
    this.Title = title;
    this.Height = height;
    this.IsOpened = is_opened ? 1 : 0;
    this.OnSelect = on_select;
    this.Locked = 0;
};

Spoiler.prototype = new TabBase();

Spoiler.prototype.UpdateTitle = function () {
    this.TitleHolder.innerHTML = this.Title;
};

Spoiler.prototype.ToString = function (holder) {
    this.Holder = d.createElement("div");
    this.Holder.id = "Spoiler" + this.Id;

    var a = d.createElement("a");
    a.Spoiler = this;
    a.href = voidLink;
    a.className = "Title";
    a.onclick = function () {
        this.Spoiler.Switch();this.blur();
    };

    this.TitleHolder = d.createElement("h4");
    this.UpdateTitle();
    a.appendChild(this.TitleHolder);
    this.Holder.appendChild(a);

    this.TopicDiv = d.createElement("div");
    this.Holder.appendChild(this.TopicDiv);

    this.RelatedDiv = d.createElement("div");
    this.RelatedDiv.className = "RelatedDiv";
    if (this.Height) {
        this.RelatedDiv.style.height = this.Height;
    }
    this.Holder.appendChild(this.RelatedDiv);

    holder.appendChild(this.Holder);
    this.Display(this.IsOpened);
};

Spoiler.prototype.Display = function (state) {
    if (this.Disabled) {
        return;
    }
    this.IsOpened = state;
    this.Holder.className = "Spoiler " + (this.IsOpened ? "Opened" : "Closed");
};

Spoiler.prototype.Switch = function () {
    this.Display(!this.IsOpened);
    if (this.OnSelect) {
        this.RelatedDiv.innerHTML = loadingIndicator;
        this.OnSelect(this);
        this.OnSelect = ""; /* TODO: Treat failure */
    }
};
"use strict";

//6.7
/*
	Journal functionality: Blog templates, messages, settings
*/

// Warning! Matrix should correspond to standard accesses
var accessMatrix = [{ f: [0, 0, 0, 0], g: [0, 0, 0, 0], j: [0, 0, 0, 0] }, { f: [1, 0, 0, 0], g: [1, 0, 0, 0], j: [1, 0, 0, 0] }, { f: [1, 0, 0, 0], g: [1, 0, 0, 0], j: [1, 0, 0, 0] }, { f: [1, 0, 0, 0], g: [1, 0, 0, 0], j: [1, 0, 0, 0] }, { f: [1, 0, 1, 1], g: [1, 0, 1, 0], j: [1, 1, 1, 1] }];

var MessagesSpoiler, TemplatesSpoiler, SettingsSpoiler;

function Journal() {
	this.fields = [];
	this.ServicePath = servicesPath + "journal.service.php";
	this.Template = "journal";
	this.ClassName = "Journal";
	this.Forum = "";
};

Journal.prototype = new OptionsBase();

Journal.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.Bind();
};

Journal.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);

	this.FindRelatedControls();

	this.Forum = this.Tab.Forum;

	this.GroupSelfAssign(["linkNewPost", "linkDeleteJournal"]);
	this.SetTabElementValue("FORUM_ID", this.Forum.FORUM_ID);
	this.SetTabElementValue("Title", this.Forum.TITLE);
	this.SetTabElementValue("linkNewPost", "Создать новую запись в	&laquo;" + this.Forum.TITLE + "&raquo;");
	this.SetTabElementValue("linkDeleteJournal", "Удалить	&laquo;" + this.Forum.TITLE + "&raquo;");

	if (this.Forum.ACCESS != FULL_ACCESS) {
		this.DisplayTabElement("linkDeleteJournal", 0);
	} else if (this.Forum.ACCESS != READ_ADD_ACCESS && this.Forum.ACCESS != FULL_ACCESS) {
		this.DisplayTabElement("linkNewPost", 0);
	}

	var spoilers = this.inputs["Spoilers"];
	if (spoilers) {
		// TODO: Check type here
		MessagesSpoiler = new Spoiler(1, "Сообщения", 0, 0, function (tab) {
			new JournalMessages().loadTemplate(tab, me.Id, me.Login);
		});
		TemplatesSpoiler = new Spoiler(2, "Шаблоны отображения", 0, 0, function (tab) {
			new JournalTemplates().loadTemplate(tab, me.Id);
		});
		SettingsSpoiler = new Spoiler(3, "Настройки", 0, 0, function (tab) {
			new JournalSettings().loadTemplate(tab, me.Id);
		});
		AccessSpoiler = new Spoiler(4, "Доступ / друзья", 0, 0, function (tab) {
			new ForumAccess().loadTemplate(tab, me.Id);
		});

		s = [MessagesSpoiler, TemplatesSpoiler, SettingsSpoiler, AccessSpoiler];
		accessRow = accessMatrix[this.Forum.ACCESS][this.Forum.TYPE];

		for (i = 0; i < s.length; i++) {
			if (accessRow[i]) {
				s[i].Forum = this.Forum;
				s[i].ToString(spoilers);
			}
		}
	}
	InitMCE();
};

// tinyMCE initialization
function InitMCE() {
	tinymce.init({
		selector: "textarea.Editable",
		schema: "html5",
		language: "ru",
		theme: "modern",
		skin: "lightgray",
		resize: true,
		relative_urls: false,
		image_advtab: true,
		height: 500,
		statusbar: false,
		plugins: ["advlist autolink link image lists charmap hr anchor pagebreak", "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking", "save contextmenu directionality template paste preview"],
		content_css: "css/content.css",
		menubar: "insert format",
		toolbar: "insertfile undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | code preview"
	});
};

/*

		mode : "textareas",
		theme : "advanced",
		language : "ru",
		plugins : "pagebreak,style,advimage,advlink,inlinepopups,preview,contextmenu,paste,directionality,visualchars,nonbreaking,xhtmlxtras,template,media",
		convert_urls : false,
		extended_valid_elements : "a[name|href|target|title|onclick],img[class|style|src|border=1|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
		editor_selector : "Editable",

		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
		theme_advanced_buttons2 : "link,unlink,image,|,bullist,numlist,|,outdent,indent,blockquote,|,insertdate,inserttime|,sub,sup,|,charmap,|,cite,del,ins,hr,nonbreaking,pagebreak",
		theme_advanced_buttons3 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,visualchars,removeformat,|,code,preview",
		theme_advanced_toolbar_location : "bottom",
		theme_advanced_toolbar_align : "left",
		theme_advanced_resizing : true,
        media_strict : false
	});
*/
"use strict";

//2.3
/*
	Journal functionality: Blog templates, messages, settings
*/

function JournalsManager() {
	this.fields = ["SEARCH", "SHOW_FORUMS", "SHOW_JOURNALS", "SHOW_GALLERIES"];
	this.ServicePath = servicesPath + "journal_manager.service.php";
	this.Template = "journal_manager";
	this.ClassName = "JournalManager";
};

JournalsManager.prototype = new OptionsBase();

JournalsManager.prototype.request = function (params, callback) {
	this.BaseRequest(this.Gather(), callback);
};

JournalsManager.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.Bind();
};

JournalsManager.prototype.Bind = function () {
	var container = this.inputs["ForumsContainer"];
	if (container) {
		container.innerHTML = "";
		if (this.data && this.data.length) {
			for (var i = 0, l = this.data.length; i < l; i++) {
				this.data[i].ToString(i, container);
			}
		}
	}

	this.DisplayTabElement("CreateJournal", !this.HasJournal);
	this.SetTabElementValue("linkNewForum", this.HasJournal ? "" : "Создать журнал");
};

JournalsManager.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);

	this.FindRelatedControls();
	this.GroupSelfAssign(this.fields);
	this.GroupSelfAssign(["linkRefresh", "linkNewForum"]);
};

/* Forum line DTO */

var ForumTypes = { "f": "Форум", "g": "Фотогалерея", "j": "Журнал" };
function jjdto(id, title, type, access) {
	this.fields = ["FORUM_ID", "TITLE", "TYPE", "ACCESS"];
	this.Init(arguments);
};

jjdto.prototype = new DTO();

jjdto.prototype.ToShowView = function (index, container) {
	var t = this.MakeTitle();
	s = new Spoiler("_j" + (i + 1), t, 0, 0, function (tab) {
		new Journal().loadTemplate(tab, me.Id, me.Login);
	});
	s.Forum = this;
	s.ToString(container);
	if (!index) {
		s.Switch();
	}
};

jjdto.prototype.MakeTitle = function () {
	return ForumTypes[this.TYPE] + "	&laquo;" + this.TITLE + "&raquo;";
};

/* New Forum Creation */
function CreateForum(obj) {
	obj.BaseRequest("go=create&");
};
"use strict";

//7.5
/*
	Journal messages grid. Edit & delete buttons.
*/

// Global reference to update messages list
var journalMessagesObj;

function JournalMessages() {
	this.fields = ["DATE", "SEARCH", "LOGIN", "FORUM_ID"];
	this.ServicePath = servicesPath + "journal.messages.service.php";
	this.Template = "journal_messages";
	this.ClassName = "JournalMessages";

	this.GridId = "MessagesGrid";

	journalMessagesObj = this;
	this.ForumsLoaded = 0;

	this.Forum = new jjdto();
};

JournalMessages.prototype = new PagedGrid();

JournalMessages.prototype.InitPager = function () {
	this.Pager = new Pager(this.inputs[this.PagerId], function () {
		this.Tab.JournalMessages.SwitchPage();
	}, this.PerPage);
};

JournalMessages.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.Bind(this.data, this.Total);
};

JournalMessages.prototype.TemplateLoaded = function (req) {
	this.Forum = this.Tab.Forum;
	if (this.Forum) {
		this.FORUM_ID = this.Forum.FORUM_ID;
	}

	this.TemplateBaseLoaded(req);

	this.GroupSelfAssign(["buttonSearch", "ResetFilter"]);
	BindEnterTo(this.inputs["SEARCH"], this.inputs["buttonSearch"]);
	new DatePicker(this.inputs["DATE"]);
};

/* Journal Record Data Transfer Object */

function jrdto(id, title, content, date, comments, type) {
	this.fields = ["Id", "Title", "Content", "Date", "Comments", "Type"];
	this.Init(arguments);
};

jrdto.prototype = new DTO();

jrdto.prototype.ToString = function (index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
	var h2 = d.createElement("h2");
	if (this.Type) {
		h2.className = this.Type == "1" ? "Friends" : "Private";
	}
	h2.innerHTML = this.Title;
	td1.appendChild(h2);
	td1.innerHTML += this.Content;
	tr.appendChild(td1);

	var td2 = d.createElement("td");
	td2.className = "Centered";
	var comments = Math.round(this.Comments);
	if (comments) {
		var a = d.createElement("a");
		a.innerHTML = comments;
		a.jrdto = this;
		a.obj = obj;
		a.href = voidLink;
		a.onclick = function () {
			ShowMessageComments(this);
		};
		td2.appendChild(a);
	} else {
		td2.innerHTML = comments;
	}
	tr.appendChild(td2);

	var td3 = d.createElement("td");
	td3.className = "Centered";
	td3.appendChild(MakeButton("EditRecord(this," + this.Id + ")", "icons/edit.gif", obj, "", "Править"));
	td3.appendChild(MakeButton("DeleteRecord(this," + this.Id + ")", "delete_icon.gif", obj, "", "Удалить"));
	tr.appendChild(td3);

	return tr;
};

/* Actions */

function EditRecord(a, post_id) {
	EditJournalPost(a.obj, post_id);
};

// TODO: Rewrite using Requestor
function DeleteRecordConfirmed(obj, id) {
	obj.Tab.Alerts.Clear();
	var s = new ParamsBuilder();
	s.add('go', 'delete');
	s.add('USER_ID', obj.USER_ID);
	s.add('RECORD_ID', id);
	$.post(post_service, s.build, Deletecallback.bind(obj));
};

function Deletecallback(req, obj) {
	if (obj) {
		obj.requestBaseCallback(req, obj);
		if (!obj.Tab.Alerts.HasErrors) {
			obj.request();
		}
	}
};

/* Confirms */

function DeleteRecord(a, id) {
	co.Show(function () {
		DeleteRecordConfirmed(a.obj, id);
	}, "Удалить запись?", "Запись в блоге и все комментарии к ней будут удалены.<br>Продолжить?");
};
"use strict";

//1.6
/*
    Paged view of the long content.
*/

function Pager(holder, callback, per_page, total, current) {
    this.Holder = holder;
    this.callback = callback;
    this.PerPage = per_page ? per_page : 20;
    this.Total = total;
    this.Current = current ? current : 0;

    this.VisiblePages = 10;
    this.Holder.className += " Pager";
};

Pager.prototype.AddLink = function (i, prefix, postfix) {
    if (prefix) {
        this.Holder.appendChild(d.createTextNode(prefix));
    }

    var page;
    if (i == this.Current) {
        page = d.createElement("span");
    } else {
        page = d.createElement("a");
        page.href = voidLink;
        page.Pager = this;
        page.onclick = function () {
            this.Pager.SwitchTo(this);
        };
    }
    page.innerHTML = i + 1;
    this.Holder.appendChild(page);

    if (postfix) {
        this.Holder.appendChild(d.createTextNode(postfix));
    }
};

Pager.prototype.Offset = function () {
    return this.Current * this.PerPage;
};

Pager.prototype.Pages = function () {
    return Math.ceil(this.Total / this.PerPage);
};

Pager.prototype.Print = function () {
    var pages = this.Pages();

    this.Holder.innerHTML = "";
    var from = this.Current - Math.floor(this.VisiblePages / 2);
    if (from < 0) {
        from = 0;
    }
    var till = from + this.VisiblePages;
    if (till > pages) {
        from = pages - this.VisiblePages;
        till = pages;
        if (from < 0) {
            from = 0;
        }
    }

    if (from > 0) {
        this.AddLink(0, "", "..");
    }
    for (var i = from; i < till; i++) {
        this.AddLink(i);
    }

    if (till < pages) {
        this.AddLink(pages - 1, "..");
    }
};

Pager.prototype.SwitchToPage = function (num) {
    this.Current = num;
    this.Print();
    if (this.callback) {
        this.callback(this.Current);
    }
};

Pager.prototype.SwitchTo = function (a) {
    this.SwitchToPage(1 * a.innerHTML - 1);
};

function SwitchPage(el) {
    if (el && el.obj && el.obj.Pager) {
        el.obj.Pager.SwitchToPage(0);
    }
};
"use strict";

//2.6
/*
    Help with binding events to controls.
*/

function EnterHandler(e, el) {
    var keynum = 0;
    if (window.event) {
        // IE
        keynum = e.keyCode;
    } else if (e.which) {
        // Netscape/Firefox/Opera
        keynum = e.which;
    }

    if (keynum == 13 && el.Submitter) {
        DoClick(el.Submitter);
        return true;
    }
    return false;
};

function BindEnterTo(el, click_to) {
    if (el) {
        el.Submitter = click_to;
        el.onkeypress = function (e) {
            EnterHandler(e, this);
        };
        if (el.type == "checkbox" || el.type == "radio") {
            el.onchange = function () {
                DoClick(this.Submitter);
            };
        }
    }
};

function DoClick(el) {
    if (el) {
        if (el.click) {
            el.click();
        } else if (el.onclick) {
            el.onclick();
        }
    }
};
"use strict";

//3.4
/*
    Journal templates: Global markup, single message & stylesheets.
*/

function JournalTemplates() {
    this.fields = new Array("TITLE", "BODY", "MESSAGE", "CSS", "SKIN_TEMPLATE_ID");
    this.ServicePath = servicesPath + "journal.templates.service.php";
    this.Template = "journal_templates";
    this.ClassName = "JournalTemplates";

    this.Forum = new jjdto();
};

JournalTemplates.prototype = new OptionsBase();

JournalTemplates.prototype.request = function (params, callback) {
    var s = new ParamsBuilder(params);
    s.add('FORUM_ID', this.Forum.FORUM_ID);
    this.BaseRequest(s.build, callback);
};

JournalTemplates.prototype.requestCallback = function (req) {
    this.skinTemplateId = '';
    this.ownMarkupAllowed = '';
    this.defaultTemplateId = '';
    this.requestBaseCallback(req);

    if (this.data) {
        this.FillFrom(this.data);
        this.Bind();
    }
    if (!this.ownMarkupAllowed) {
        this.DisplayTabElement("label__1", false);
        bId = this.skinTemplateId > 0 ? this.skinTemplateId : this.defaultTemplateId;
    } else {
        bId = this.skinTemplateId;
    }
    button = SetRadioValue(this.inputs["SKIN_TEMPLATE_ID"], bId);
    if (button) {
        button.click();
    }
};

JournalTemplates.prototype.TemplateLoaded = function (req) {
    this.Forum = this.Tab.Forum;
    this.FORUM_ID = this.Tab.FORUM_ID;

    this.TemplateBaseLoaded(req);

    this.AssignTabTo("SKIN_TEMPLATE_ID");
    this.Tab.AddSubmitButton("SaveObject(this)", "", this);
};

/* Actions */

function Maximize(el) {
    el.className = "Maximized";
};
"use strict";

//3.7
/*
	Date picker fuctionality.
*/

function Calendar(holder, date, with_time) {
	this.Holder = holder;
	this.WithTime = with_time;
	this.callback = "";
	this.Date = date ? date : new Date();
	this.SelectDate();
};

Calendar.prototype.SelectDate = function () {
	this.SelectedDate = new Date(this.Date.getFullYear(), this.Date.getMonth(), this.Date.getDate());
};

Calendar.prototype.MakeHeaderLink = function (is_selectable, holder) {
	if (holder.hasChildNodes()) {
		holder.removeChild(holder.firstChild);
	}
	var result;
	if (is_selectable) {
		var select = d.createElement("select");
		select.Calendar = this;
		result = select;
	} else {
		var a = d.createElement("a");
		a.href = voidLink;
		a.Calendar = this;
		result = a;
	}
	holder.appendChild(result);
	return result;
};

Calendar.prototype.MakeMonth = function (is_selectable) {
	var el = this.MakeHeaderLink(is_selectable, this.MonthHolder);
	this.MonthSelect = is_selectable ? el : "";
	if (is_selectable) {
		el.onchange = function () {
			this.Calendar.UpdateMonth();
		};
		el.onblur = el.onchange;
		el.className = "Right";

		var month = this.Date.getMonth();
		for (var i = 0, l = months.length; i < l; i++) {
			var o = d.createElement("option");
			o.text = months[i];
			o.value = i;
			if (i == month) {
				o.selected = true;
			}
			el.options.add(o, i);
		}
		el.focus();
	} else {
		el.innerHTML = months[this.Date.getMonth()];
		el.onclick = function () {
			this.Calendar.MakeMonth(true);
		};
	}
};

Calendar.prototype.MakeYear = function (is_selectable) {
	var el = this.MakeHeaderLink(is_selectable, this.YearHolder);
	this.YearSelect = is_selectable ? el : "";
	var year = this.Date.getFullYear();

	if (is_selectable) {
		el.onchange = function () {
			this.Calendar.UpdateYear();
		};
		el.onblur = el.onchange;

		var k = 0;
		for (var i = year - 20; i < year + 20; i++) {
			var o = d.createElement("option");
			o.text = i;
			o.value = i;
			if (i == year) {
				o.selected = true;
			}
			el.options.add(o, k++);
		}
		el.focus();
	} else {
		el.innerHTML = year;
		el.onclick = function () {
			this.Calendar.MakeYear(true);
		};
	}
};

Calendar.prototype.MakeHours = function (is_selectable) {
	var el = this.MakeHeaderLink(is_selectable, this.HoursHolder);
	this.HoursSelect = is_selectable ? el : "";
	var hours = this.Date.getHours();

	if (is_selectable) {
		el.onchange = function () {
			this.Calendar.UpdateTime();
		};
		el.onblur = el.onchange;

		var k = 0;
		for (var i = 0; i < 24; i++) {
			var o = d.createElement("option");
			o.text = TwoDigits(i);
			o.value = i;
			if (i == hours) {
				o.selected = true;
			}
			el.options.add(o, k++);
		}
		el.focus();
	} else {
		el.innerHTML = TwoDigits(hours);
		el.onclick = function () {
			this.Calendar.MakeHours(true);
		};
	}
};

Calendar.prototype.MakeMinutes = function (is_selectable) {
	var el = this.MakeHeaderLink(is_selectable, this.MinutesHolder);
	this.MinutesSelect = is_selectable ? el : "";
	var minutes = this.Date.getMinutes();

	if (is_selectable) {
		el.onchange = function () {
			this.Calendar.UpdateTime();
		};
		el.onblur = el.onchange;

		var k = 0;
		for (var i = 0; i < 60; i++) {
			var o = d.createElement("option");
			o.text = TwoDigits(i);
			o.value = i;
			if (i == minutes) {
				o.selected = true;
			}
			el.options.add(o, k++);
		}
		el.focus();
	} else {
		el.innerHTML = TwoDigits(minutes);
		el.onclick = function () {
			this.Calendar.MakeMinutes(true);
		};
	}
};

Calendar.prototype.UpdateMonth = function () {
	if (this.MonthSelect) {
		this.Date.setMonth(this.MonthSelect.value);
		this.Print();
	} else {
		this.MakeMonth(false);
	}
};

Calendar.prototype.UpdateYear = function () {
	if (this.YearSelect) {
		this.Date.setYear(this.YearSelect.value);
		this.Print();
	} else {
		this.MakeYear(false);
	}
};

Calendar.prototype.UpdateTime = function (flag) {
	if (this.HoursSelect) {
		this.Date.setHours(this.HoursSelect.value);
		this.Print();
	} else if (this.MinutesSelect) {
		this.Date.setMinutes(this.MinutesSelect.value);
		this.Print();
	}
};

Calendar.prototype.MakeHeader = function () {
	var tr = d.createElement("tr");
	var th = d.createElement("th");
	th.colSpan = 7;

	this.MonthHolder = d.createElement("span");
	this.MakeMonth(false);
	th.appendChild(this.MonthHolder);

	th.appendChild(d.createTextNode(" "));

	this.YearHolder = d.createElement("span");
	this.MakeYear(false);
	th.appendChild(this.YearHolder);

	tr.appendChild(th);
	return tr;
};

Calendar.prototype.MakeTimePicker = function () {
	var tr = d.createElement("tr");
	var th = d.createElement("th");
	th.colSpan = 7;

	th.appendChild(d.createTextNode("Время: "));

	this.HoursHolder = d.createElement("span");
	this.MakeHours(false);
	th.appendChild(this.HoursHolder);

	th.appendChild(d.createTextNode(":"));

	this.MinutesHolder = d.createElement("span");
	this.MakeMinutes(false);
	th.appendChild(this.MinutesHolder);

	tr.appendChild(th);
	return tr;
};

Calendar.prototype.Clear = function () {
	this.Holder.innerHTML = "";
};

Calendar.prototype.MakeLink = function (day, check_day) {
	var a = d.createElement("a");
	a.href = voidLink;
	a.innerHTML = day;
	a.Calendar = this;
	a.onclick = function () {
		this.Calendar.Select(this);
	};
	if (day == check_day) {
		a.className = "Selected";
	}
	return a;
};

Calendar.prototype.Print = function () {
	/* Date calculations */
	var a = new Date(this.Date.getFullYear(), this.Date.getMonth(), 1);
	var offset = a.getDay() - 1;
	if (offset < 0) {
		offset = 6;
	}
	var days = 31;
	if (this.Date.getMonth() != 11) {
		var b = new Date(this.Date.getFullYear(), this.Date.getMonth() + 1, 1);
		days = Math.round((b.getTime() - a.getTime()) / dayMsec);
	}

	var t = d.createElement("table");
	var tb = d.createElement("tbody");

	/* Header */
	tb.appendChild(this.MakeHeader());

	/* Body */
	var i = 0;
	var day = 1;
	var check_day = 0;
	if (this.Date.getMonth() == this.SelectedDate.getMonth() && this.Date.getYear() == this.SelectedDate.getYear()) {
		check_day = this.SelectedDate.getDate();
	}

	while (day <= days) {
		var tr = d.createElement("tr");
		for (var k = 0; k < 7; k++) {
			var td = d.createElement("td");
			if (i >= offset && day <= days) {
				td.appendChild(this.MakeLink(day, check_day));
				day++;
			} else {
				td.innerHTML = "&nbsp;";
			}
			i++;
			tr.appendChild(td);
		}
		tb.appendChild(tr);
	}

	/* Time */
	if (this.WithTime) {
		tb.appendChild(this.MakeTimePicker());
	}

	this.Holder.innerHTML = "";
	t.appendChild(tb);
	t.className = "Calendar";
	this.Holder.appendChild(t);
};

Calendar.prototype.Select = function (a) {
	this.Date.setDate(a.innerHTML);
	this.SelectDate();
	this.Print();

	if (this.callback) {
		this.callback();
	}
};

/*-------------------------------------------------*/

function DatePicker(input, with_time) {
	if (!input) {
		return;
	}
	this.Visible = true;
	this.WithTime = with_time;
	this.Input = input;
	if (!input.type) {
		this.Input = $(getElement(input))[0];
	}
	if (this.Input) {
		this.Input.className = with_time ? "DateTime" : "Date";
		/*this.Input.type = with_time ? "datetime" : "date"; */

		this.Holder = d.createElement("div");
		this.Holder.className = "DatePicker";
		this.switchVisibility(false);

		this.Calendar = new Calendar(this.Holder, "", this.WithTime);

		this.Calendar.Picker = this;
		this.Calendar.callback = function () {
			this.Picker.Selected();
		};

		insertAfter(this.Holder, this.Input);
		insertAfter(MakeButton("SwitchDatePicker(this)", "icons/calendar.gif", this, "PickerButton", "Выбрать дату"), this.Input);
	}
};

DatePicker.prototype.Init = function () {
	this.Calendar.Date = ParseDate(this.Input.value);
	this.Calendar.SelectDate();
	this.Calendar.Print();
};

DatePicker.prototype.switchVisibility = function () {
	this.Visible = !this.Visible;
	displayElement(this.Holder, this.Visible);
	if (this.Visible) {
		this.Init();
	}
};

DatePicker.prototype.Selected = function () {
	this.Visible = true;
	this.switchVisibility();
	var date = this.Calendar.Date;
	this.Input.value = date.ToString(this.WithTime);
};

function SwitchDatePicker(a) {
	if (a && a.obj) {
		a.obj.switchVisibility();
	}
};
"use strict";

//2.2
/*
	Helper methods to work with dates.
*/

var dayMsec = 1000 * 60 * 60 * 24;
var months = new Array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
var monthsNames = new Array("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");

function ParseDate(str) {
	var datePat = /^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})(\s(\d{1,2}):(\d{1,2})){0,1}/;
	var matchArray = str.match(datePat);

	var date = new Date();
	date.IsEmpty = false;
	if (matchArray == null) {
		date.IsEmpty = true;
		return date;
	}

	var year = matchArray[1];
	var month = matchArray[3];
	var day = matchArray[5];

	var hours = date.getHours();
	var minutes = date.getMinutes();
	if (matchArray[6]) {
		hours = matchArray[7];
		minutes = matchArray[8];
	}

	return new Date(year, month - 1, day, hours, minutes, 0);
};

Date.prototype.ToString = function (add_time) {
	var result = this.getFullYear() + "-" + TwoDigits(1 + this.getMonth()) + "-" + TwoDigits(this.getDate());
	if (add_time) {
		result += " " + TwoDigits(this.getHours()) + ":" + TwoDigits(this.getMinutes());
	}
	return result;
};

Date.prototype.ToPrintableString = function (add_time) {
	var result = this.getDate() + " " + monthsNames[this.getMonth()] + " " + this.getFullYear();
	if (add_time) {
		result += ",	" + this.Time();
	}
	return result;
};

Date.prototype.Time = function () {
	return TwoDigits(this.getHours()) + ":" + TwoDigits(this.getMinutes());
};
"use strict";

//3.5
/*
	Create/edit blog post in separate tab.
*/

var post_service = servicesPath + "journal.post.service.php";

function JournalPost(forum) {
	this.fields = new Array("RECORD_ID", "TITLE", "CONTENT", "DATE", "TYPE", "IS_COMMENTABLE", "FORUM_ID");
	this.defaultValues = new Array("-1", "", "", new Date().ToString(true), "0", "1", "");
	this.ServicePath = post_service;
	this.ClassName = "JournalPost";
	this.Template = "journal_post";

	this.Forum = forum;
	mceInitialized = 0;
};

JournalPost.prototype = new OptionsBase();

JournalPost.prototype.EditorIsShown = function () {
	return !this.Forum || this.Forum.TYPE == "j";
};

JournalPost.prototype.Gather = function () {
	var editor = tinyMCE.get(this.ContentField.id);
	if (editor) {
		this.CONTENT = editor.save();
	} else {
		this.CONTENT = this.ContentField.value;
	}
	return this.BaseGather();
};

JournalPost.prototype.Bind = function () {
	this.BaseBind();
	this.ContentField.value = this.CONTENT;

	/* Update tab title */
	if (this.TITLE) {
		this.Tab.Title = "&laquo;" + this.TITLE.substr(0, 10) + "...&raquo;";
		this.Tab.Alt = this.TITLE;
		tabs.Print();
	}
};

JournalPost.prototype.request = function (params, callback) {
	var s = new ParamsBuiler(params).add('RECORD_ID', this.RECORD_ID);
	if (this.Forum) {
		s.add('FORUM_ID', this.Forum.FORUM_ID);
	};
	if (this.TagsSpoiler && this.TagsSpoiler.AddedTags) {
		s.add('TAGS', this.TagsSpoiler.AddedTags.Gather());
	}
	this.BaseRequest(s.build(), callback);
};

JournalPost.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	if (this.data && this.data != '') {
		this.FillFrom(this.data);
		this.Bind();
		if (journalMessagesObj) {
			journalMessagesObj.request();
		};
	};

	if (!mceInitialized && this.EditorIsShown && this.EditorIsShown()) {
		InitMCE();
		mceInitialized = 1;
	};

	if (this.Forum) {
		this.SetTabElementValue("TITLE1", this.Forum.MakeTitle());
		this.Tab.SetAdditionalClass(this.Forum.TYPE);
	};
};

JournalPost.prototype.TemplateLoaded = function (req) {
	this.RECORD_ID = 1 * this.Tab.PARAMETER;
	this.TemplateBaseLoaded(req);

	this.SetTabElementValue("LOGIN", this.LOGIN);

	// Create content field
	this.ContentField = createElement("textarea", "CONTENT" + Math.random(10000));
	if (this.EditorIsShown()) {
		this.ContentField.className = "Editable";
	}
	this.ContentField.rows = 30;
	if (this.inputs["ContentHolder"]) {
		this.inputs["ContentHolder"].appendChild(this.ContentField);
	}

	// Radios group rename
	RenameRadioGroup(this.inputs["TYPE"]);

	// DatePicker
	this.inputs["DATE"].value = new Date().ToString(1);
	var a = new DatePicker(this.inputs["DATE"], 1);

	// Tags (labels) spoiler
	var tagsContainer = this.inputs["TagsContainer"];
	if (tagsContainer) {
		this.TagsSpoiler = new Spoiler(1, "Теги&nbsp;(метки)", 0, 0, function (tab) {
			new Tags().loadTemplate(tab, me.Id, me.Login);
		});
		this.TagsSpoiler.ToString(tagsContainer);
		this.TagsSpoiler.RECORD_ID = this.RECORD_ID;
	}

	// Submit button
	this.Tab.AddSubmitButton("SaveObject(this)", "", this);
};

/* Helper methods */

function EditJournalPost(obj, post_id) {
	if (obj) {
		//		var login = obj.LOGIN ? obj.LOGIN : "";
		var login = "";
		var tab_id = "post" + post_id;
		createUserTab(obj.USER_ID, login, new JournalPost(obj.Forum), "Новая запись", post_id, tab_id);
	}
};
"use strict";

//2.5
/*
    Journal settings of user menu.
*/

function JournalSettings() {
    this.fields = ["ALIAS", "REQUESTED_ALIAS", "TITLE", "DESCRIPTION", "IS_PROTECTED", "IS_HIDDEN", "OWN_MARKUP_ALLOWED"];
    this.ServicePath = servicesPath + "journal.settings.service.php";
    this.Template = "journal_settings";
    this.ClassName = "JournalSettings";

    this.Forum = new jjdto();
};

JournalSettings.prototype = new OptionsBase();

JournalSettings.prototype.requestCallback = function (req) {
    this.requestBaseCallback(req);
    this.FillFrom(this.data);
    this.Bind(this.data);
};

JournalSettings.prototype.TemplateLoaded = function (req) {
    this.Forum = this.Tab.Forum;
    if (this.Forum && this.Forum.FORUM_ID) {
        this.FORUM_ID = this.Forum.FORUM_ID;
    }

    this.TemplateBaseLoaded(req);

    if (!me.IsAdmin()) {
        this.SetTabElementValue("IsHidden", "");
    };

    this.AssignTabTo("linkRefresh");
    this.FindRelatedControls();
    this.Tab.AddSubmitButton("SaveObject(this)", "", this);
};

JournalSettings.prototype.request = function (params, callback) {
    var s = new ParamsBuilder(params).add('FORUM_ID', this.FORUM_ID);
    this.BaseRequest(s.build(), callback);
};
"use strict";

//1.1
/*
    Comments base class
*/

function Comments() {
    this.fields = new Array("SEARCH", "LOGIN", "TITLE");
    this.GridId = "CommentsGrid";
};

Comments.prototype = new PagedGrid();
"use strict";

//3.5
/*
	Journal comments grid. Edit & delete buttons.
*/

function JournalComments(forum) {
	this.ServicePath = servicesPath + "journal.comments.service.php";
	this.Template = "journal_comments";
	this.ClassName = "JournalComments";
	this.Columns = 3;

	this.Forum = forum;
};

JournalComments.prototype = new Comments();

JournalComments.prototype.Gather = function () {
	return new ParamsBuilder(this.BaseGather()).add('RECORD_ID', this.jrdto.Id).build();
};

JournalComments.prototype.InitPager = function () {
	this.Pager = new Pager(this.inputs[this.PagerId], function () {
		this.Tab.JournalComments.SwitchPage();
	}, this.PerPage);
};

JournalComments.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.Bind(this.data, this.Total);
	if (this.Forum) {
		this.SetTabElementValue("FORUM", this.Forum.MakeTitle());
	}
};

JournalComments.prototype.loadTemplate = function (tab, user_id, login) {
	// Important!
	this.jrdto = tab.PARAMETER;
	this.TITLE = this.jrdto.Title;

	/* Update tab title */
	tab.Title = "Комментарии к&nbsp;&laquo;" + this.jrdto.Title.substr(0, 10) + "...&raquo;";
	tab.Alt = this.jrdto.Title;
	tabs.Print();

	this.LoadBaseTemplate(tab, user_id, login);
};

JournalComments.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);

	this.AssignSelfTo("buttonSearch");
	BindEnterTo(this.inputs["SEARCH"], this.inputs["buttonSearch"]);
};

/* Journal Record Data Transfer Object */

function jcdto(id, user_id, name, title, content, date, is_hidden, is_deleted) {
	this.fields = ["Id", "UserId", "Name", "Title", "Content", "Date", "IsHidden", "IsDeleted"];
	this.Init(arguments);
};

jcdto.prototype = new DTO();

jcdto.prototype.ToString = function (index, obj) {
	var tr = MakeGridRow(index);
	if (this.IsHidden) {
		tr.className += " Hidden";
	}
	if (this.IsDeleted) {
		tr.className += " Deleted";
	}

	var td1 = d.createElement("td");
	var h2 = d.createElement("h2");
	h2.innerHTML = "&laquo;" + this.Title + "&raquo;";
	td1.appendChild(h2);

	var span = d.createElement("span");
	span.innerHTML = this.Content;
	td1.appendChild(span);

	var div = d.createElement("div");
	div.innerHTML = (this.UserId ? "<b>" : "") + this.Name + (this.UserId ? "</b>" : "") + ",	" + this.Date;
	td1.appendChild(div);
	tr.appendChild(td1);

	var td3 = d.createElement("td");
	td3.className = "Centered";
	td3.appendChild(MakeButton("EditRecord(this," + this.Id + ")", "icons/edit.gif", obj, "", "Править"));
	td3.appendChild(MakeButton("DeleteComment(this," + this.Id + ")", "delete_icon.gif", obj, "", "Удалить"));
	tr.appendChild(td3);

	return tr;
};

/* Actions */

function DeleteCommentConfirmed(obj, id) {
	var s = new ParamsBuilder().add('go', 'delete').add('id', id);
	obj.request(s.build());
};

function ShowMessageComments(a) {
	var tab_id = "c" + a.jrdto.Id;
	createUserTab(a.obj.USER_ID, a.obj.LOGIN, new JournalComments(a.obj.Forum), "Комментарии в журнале", a.jrdto, tab_id);
};

/* Confirms */

function DeleteComment(a, id) {
	co.Show(function () {
		DeleteCommentConfirmed(a.obj, id);
	}, "Удалить комментарий?", "Комментарий будет удалён.<br>Продолжить?");
};
"use strict";

//4.1
/*
    Performs single async request with given set of parameters
*/

function Requestor(service, obj) {
    this.ServicePath = service;
    this.obj = obj;
};

Requestor.prototype.request = function (names, values) {
    var s = new ParamsBuilder().add('no-cache', _.random(1000, 9999));
    names.forEach(function (name, index) {
        s.add(name, values[index]);
    });
    $.post(this.ServicePath, s.build()).done(this.requestCallback.bind(this));
};

Requestor.prototype.callback = function () {};

Requestor.prototype.Basecallback = function (req) {
    var obj = this.obj;
    var tabObject = this.obj.Tab;
    tabObject.Alerts.Clear();
    eval(req);
    this.req = req;
    this.callback(this);
};

Requestor.prototype.requestCallback = function (req) {
    this.Basecallback(req);
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

    this.Submitter = ""; // Treating enter button

    input.onkeypress = function (e) {
        GetData(this, e);
    };
    input.onchange = function () {
        GetData(this);
    };
};

delayedRequestor.prototype.request = function () {
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
delayedRequestor.prototype.GetParams = function () {
    return new ParamsBuilder().add(this.Input.name, this.Input.value).build();
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
    input.delayedRequestor.Timer = setTimeout(function () {
        input.delayedRequestor.request();
    }, 500);
};
"use strict";

//6.11
/*
	Forum access functionality.
	Allows to manage users access to forums/journals/galleries.
*/

var NO_ACCESS = 0;
var READ_ONLY_ACCESS = 1;
var FRIENDLY_ACCESS = 2;
var READ_ADD_ACCESS = 3;
var FULL_ACCESS = 4;

var accesses = ["доступ закрыт", "только чтение", "дружественный доступ", "чтение/запись", "полный доступ"];

function ForumAccess(user_id, tab) {
	this.UserId = user_id;
	this.Tab = tab;

	this.fields = ["WHITE_LIST", "BLACK_LIST", "FRIENDS_LIST"];
	this.Template = "forum_access";
	this.ClassName = "ForumAccess";
	this.ServicePath = servicesPath + "forum_access.service.php";

	this.Forum = new jjdto();
};

ForumAccess.prototype = new OptionsBase();

ForumAccess.prototype.TemplateLoaded = function (req) {
	this.Forum = this.Tab.Forum;
	this.FORUM_ID = this.Tab.FORUM_ID;

	this.TemplateBaseLoaded(req);

	this.FindRelatedControls();

	this.BlackListGrid = new UserList("BLACK_LIST", this, new fadto());
	this.WhiteListGrid = new UserList("WHITE_LIST", this, new fadto());
	this.FriendsListGrid = new UserList("FRIENDS_LIST", this, new fjdto());

	this.AssignSelfTo("RefreshForumAccess");

	var a = new delayedRequestor(this, this.inputs["ADD_USER"], GetJournalUsers);
	//	a.GetParams = function() {};
};

ForumAccess.prototype.BaseBind = function () {
	this.BlackListGrid.ClearRecords();
	this.WhiteListGrid.ClearRecords();
	this.FriendsListGrid.ClearRecords();

	for (var i = 0, l = this.data.length; i < l; i++) {
		var dtoItem = this.data[i];
		switch (dtoItem.ACCESS) {
			case FULL_ACCESS:
			case READ_ADD_ACCESS:
			case FRIENDLY_ACCESS:
			case READ_ONLY_ACCESS:
				this.WhiteListGrid.AddItem(dtoItem);
				break;
			case NO_ACCESS:
				this.BlackListGrid.AddItem(dtoItem);
				break;
		}
	}

	for (var i = 0, l = this.friends.length; i < l; i++) {
		var dtoItem = this.friends[i];
		this.FriendsListGrid.AddItem(dtoItem);
	}

	this.BlackListGrid.Refresh();
	this.WhiteListGrid.Refresh();
	this.FriendsListGrid.Refresh();
};

ForumAccess.prototype.request = function (params, callback) {
	var s = new ParamsBuilder(params).add('FORUM_ID', this.Forum.FORUM_ID);
	this.BaseRequest(s.build(), callback);
};

ForumAccess.prototype.requestCallback = function (req) {
	this.friends = [];
	this.requestBaseCallback(req);
	this.Bind(this.data);
};

/* Data Transfer Object */

function fadto(forum_id, target_user_id, login, access) {
	this.fields = ["FORUM_ID", "TARGET_USER_ID", "LOGIN", "ACCESS"];
	this.Init(arguments);
	this.Id = this.FORUM_ID + "_" + this.TARGET_USER_ID;
};

fadto.prototype = new EditableDTO();

fadto.prototype.ToShowView = function (index, obj) {
	var tr = MakeGridRow(index);
	tr.className = index % 2 ? "Dark" : "";

	var a = accesses[this.ACCESS];
	var td1 = d.createElement("td");
	td1.innerHTML = this.LOGIN + (a ? "&nbsp;(" + a + ")" : "");
	if (this.ACCESS == FULL_ACCESS) {
		td1.className = "Bold";
	}
	tr.appendChild(td1);
	tr.appendChild(this.MakeButtonsCell(1));
	return tr;
};

fadto.prototype.ToEditView = function () {};

/* Journal user DTO */

function judto($id, $login, $nick, $journal_id, $title) {
	this.fields = ["USER_ID", "LOGIN", "NICKNAME", "JOURNAL_ID", "TITLE"];
	this.Init(arguments);
};

judto.prototype = new DTO();

judto.prototype.ToString = function (index, obj, prev_id, holder, className) {

	if (prev_id != this.USER_ID) {
		var li = d.createElement("li");
		li.className = className;
		li.appendChild(MakeButton("AddForumAccess('" + this.USER_ID + "',''," + FULL_ACCESS + ", this.obj)", "icons/add_gold.gif", obj, "", "Дать полный доступ"));
		li.appendChild(MakeButton("AddForumAccess('" + this.USER_ID + "',''," + READ_ADD_ACCESS + ", this.obj)", "icons/add_white.gif", obj, "", "Дать доступ на чтение/запись"));
		li.appendChild(MakeButton("AddForumAccess('" + this.USER_ID + "',''," + FRIENDLY_ACCESS + ", this.obj)", "icons/add_magenta.gif", obj, "", "Дать дружественный доступ"));
		li.appendChild(MakeButton("AddForumAccess('" + this.USER_ID + "',''," + NO_ACCESS + ", this.obj)", "icons/add_black.gif", obj, "", "Закрыть доступ"));
		li.appendChild(MakeDiv(this.LOGIN + (this.NICKNAME ? "&nbsp;(" + this.NICKNAME + ")" : ""), "span"));
		holder.appendChild(li);
	}
	if (this.JOURNAL_ID) {
		li = d.createElement("li");
		li.className = className + " Journal";
		li.appendChild(MakeButton("AddForumAccess('','" + this.JOURNAL_ID + "', " + FRIENDLY_ACCESS + ", this.obj)", "icons/add_green.gif", obj, "", "Добавить дружественный журнал"));
		li.appendChild(MakeDiv("Журнал &laquo;" + this.TITLE + "&raquo;&nbsp;(" + this.LOGIN + ")", "span"));
		holder.appendChild(li);
	}
};

/* Friendly Journal DTO */

function fjdto(forum_id, title, login, target_forum_id) {
	this.fields = ["FORUM_ID", "TITLE", "LOGIN", "TARGET_FORUM_ID"];
	this.Init(arguments);
	this.Id = this.TARGET_FORUM_ID;
};

fjdto.prototype = new EditableDTO();

fjdto.prototype.ToShowView = function (index, obj) {
	var tr = MakeGridRow(index);
	tr.className = index % 2 ? "Dark" : "";

	var td1 = d.createElement("td");
	td1.innerHTML = "&laquo;" + this.TITLE + "&raquo;&nbsp;(" + this.LOGIN + ")";
	tr.appendChild(td1);
	tr.appendChild(this.MakeButtonsCell(1));
	return tr;
};

fjdto.prototype.ToEditView = function () {};

/* Userlist Grid */

function UserList(id, relatedObject, dtObject) {
	var dt = dtObject;
	this.fields = dt.fields;

	this.ServicePath = servicesPath + "forum_access.service.php";
	this.GridId = id;
	this.Columns = 2;

	this.USER_ID = relatedObject.USER_ID;
	this.Tab = relatedObject.Tab;

	this.FindTable();
	this.ClearRecords();

	this.obj = relatedObject;
};

UserList.prototype = new EditableGrid();

UserList.prototype.BaseBind = function () {};

UserList.prototype.requestCallback = function (req) {
	if (this.obj) {
		this.obj.requestBaseCallback(req);
		this.obj.Bind(this.data);
	}
};

/* Helper methods */

function AddForumAccess(user_id, target_forum_id, access, obj) {
	var req = new Requestor(servicesPath + "forum_access.service.php", obj);
	req.callback = refreshList;
	req.request(["go", "FORUM_ID", "TARGET_USER_ID", "TARGET_FORUM_ID", "ACCESS"], ["add", obj.Forum.FORUM_ID, user_id, target_forum_id, access]);
};

function refreshList(sender) {
	sender.obj.requestCallback(sender.req, sender.obj);
};

function GetJournalUsers(input) {
	input.delayedRequestor.obj.SetTabElementValue("FOUND_USERS", loadingIndicator);
	var juRequest = new Requestor(servicesPath + "journal_users.service.php", input.delayedRequestor.obj);
	juRequest.callback = DrawUsers;
	juRequest.request(["value"], [input.value]);
};

function DrawUsers(sender) {
	var el = sender.obj.inputs["FOUND_USERS"];
	if (el) {
		el.innerHTML = "";
		var ul = d.createElement("ul");
		var prev_id = 0;
		var className = "";
		for (var i = 0, l = sender.data.length; i < l; i++) {
			var item = sender.data[i];
			className = prev_id == item.USER_ID ? className : className ? "" : "Dark";
			item.ToString(i, sender.obj, prev_id, ul, className);
			prev_id = item.USER_ID;
		}
		el.appendChild(ul);
		sender.obj.Tab.Alerts.Clear();
		if (sender.more) {
			sender.obj.Tab.Alerts.Add("Более	20	результатов	-	уточните критерий поиска.", 1);
		}
	}
};
"use strict";

//1.9
/*
    Admin options
*/

var MessagesSpoiler, TemplatesSpoiler, SettingsSpoiler;

function AdminOptions() {
    this.Template = "admin_options";
    this.ClassName = "AdminOptions";
};

AdminOptions.prototype = new OptionsBase();

AdminOptions.prototype.request = function () {
    DebugLine("Admin options request");
};

AdminOptions.prototype.TemplateLoaded = function (req) {
    this.TemplateBaseLoaded(req);

    DebugLine("Related controls");
    this.FindRelatedControls();
    var spoilers = this.inputs["Spoilers"];
    if (spoilers) {
        DebugLine("Spoilers div. SpoilerInits: " + spoilerInits.length);
        for (var i = 0, l = spoilerInits.length; i < l; i++) {
            DebugLine(i);
            var s = new Spoiler(i + 1, spoilerNames[i], 0, 0, spoilerInits[i]);
            s.ToString(spoilers);
        }
    }
};
"use strict";

//5.3
/*
	List of admin comments to user (ban, rights changes etc.)
*/

function AdminComments() {
	this.fields = ["ADMIN_COMMENT", "DATE", "SEARCH", "SEVERITY_NORMAL", "SEVERITY_WARNING", "SEVERITY_ERROR"];
	this.ServicePath = servicesPath + "admin.comments.service.php";
	this.ClassName = "AdminComments";
	this.Template = "admin_comments";
	this.GridId = "AdminCommentsGrid";
	this.Columns = 2;
};

AdminComments.prototype = new PagedGrid();

AdminComments.prototype.BaseBind = function () {};

AdminComments.prototype.InitPager = function () {
	this.Pager = new Pager(this.inputs[this.PagerId], function () {
		this.Tab.AdminComments.SwitchPage();
	}, this.PerPage);
};

AdminComments.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.Bind(this.data, this.Total);
};

// Template loading
AdminComments.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);

	Wait(1000);

	this.AssignTabTo("AddComment");
	this.GroupSelfAssign(["RefreshAdminComments", "ResetFilter"]);

	new DatePicker(this.inputs["DATE"]);

	BindEnterTo(this.inputs["ADMIN_COMMENT"], this.inputs["AddComment"]);
	BindEnterTo(this.inputs["DATE"], this.inputs["RefreshAdminComments"]);
	BindEnterTo(this.inputs["SEARCH"], this.inputs["RefreshAdminComments"]);

	// System log checkboxes
	BindEnterTo(this.inputs["SEVERITY_NORMAL"], this.inputs["RefreshAdminComments"]);
	BindEnterTo(this.inputs["SEVERITY_WARNING"], this.inputs["RefreshAdminComments"]);
	BindEnterTo(this.inputs["SEVERITY_ERROR"], this.inputs["RefreshAdminComments"]);

	if (this.Init) {
		this.Init();
	}
};

AdminComments.prototype.CustomReset = function () {
	this.SetTabElementValue("SEVERITY_NORMAL", 1);
	this.SetTabElementValue("SEVERITY_WARNING", 1);
	this.SetTabElementValue("SEVERITY_ERROR", 1);
};

/* Admin comment Data Transfer Object */

var lastCommentDate;

function acdto(date, content, login, severity, user) {
	this.fields = ["Date", "Content", "Login", "Severity", "User"];
	this.Init(arguments);
};

acdto.prototype = new DTO();

acdto.prototype.ToString = function (index, obj, holder) {
	if (!index) {
		lastCommentDate = "";
	}
	var date = ParseDate(this.Date);
	var dateString = date.ToPrintableString();
	if (date && dateString && dateString != lastCommentDate && holder) {
		lastCommentDate = dateString;
		holder.appendChild(MakeGridSubHeader(index, obj.Columns, dateString));
	}

	var tr = MakeGridRow(index);
	if (this.Severity) {
		tr.className += " " + severityCss[this.Severity - 1];
	}

	var td1 = d.createElement("td");
	td1.className = "Centered";
	td1.innerHTML = date.Time() + "<br><b>" + this.Login + "</b>";
	tr.appendChild(td1);

	var td2 = d.createElement("td");
	td2.innerHTML = (this.User ? "Пользователь	<b>" + this.User + "</b>:<br>" : "") + this.Content;
	tr.appendChild(td2);

	return tr;
};

/* Helper methods */

function AddComment(img) {
	if (img && img.Tab && img.Tab.AdminComments) {
		img.Tab.AdminComments.Save(AdminCommentSaved);
	}
};

function AdminCommentSaved(req) {
	this.SetTabElementValue("ADMIN_COMMENT", "");
	this.requestCallback(req);
};
"use strict";

//2.4
/*
    System log
*/

function SystemLog() {
    this.fields = ["DATE", "SEARCH", "SEVERITY_NORMAL", "SEVERITY_WARNING", "SEVERITY_ERROR"];
    this.Template = "system_log";
    this.ClassName = "SystemLog";
    this.GridId = "AdminCommentsGrid";
    this.Columns = 2;
    this.PerPage = 50;
};

SystemLog.prototype = new AdminComments();

SystemLog.prototype.Init = function () {
    this.FindRelatedControls();
};

SystemLog.prototype.InitPager = function () {
    this.Pager = new Pager(this.inputs[this.PagerId], function () {
        this.Tab.SystemLog.SwitchPage();
    }, this.PerPage);
};
"use strict";

//6.5
/*
	List of forbidden addresses
*/

var BannedAddrsList;
function BannedAddresses() {
	this.fields = ["BAN_ID", "BAN_CHAT", "BAN_FORUM", "BAN_JOURNAL", "TYPE", "CONTENT", "COMMENT", "TILL"];
	this.defaultValues = ["", "1", "1", "1", "ip", "", "", ""];
	this.ServicePath = servicesPath + "banned.addresses.service.php";
	this.Template = "banned_addresses";
	this.GridId = "BannedAddresses";
	this.ClassName = this.GridId;
	this.Columns = 3;
};

BannedAddresses.prototype = new Grid();

BannedAddresses.prototype.BaseBind = function () {};

BannedAddresses.prototype.requestCallback = function (req) {
	if (BannedAddrsList) BannedAddrsList.Tab.Alerts.Clear();

	this.banData = [];
	this.requestBaseCallback(req);

	if (BannedAddrsList) {
		// Adding entry from bans tab
		BannedAddrsList.Bind(this.data);
		if (this.banData.length) {
			BannedAddrsList.FillFrom(this.banData);
			BannedAddrsList.BindFields(BannedAddrsList.fields);
			BannedAddrsList.SetFormName("Редактировать запрет	" + BannedAddrsList["CONTENT"] + ":");
		}
	} else {
		// Adding entry from user profile
		this.Bind(this.data);
	};
	if (BannedAddrsList && !BannedAddrsList.Tab.Alerts.HasErrors && !BannedAddrsList.Tab.Alerts.IsEmpty) {
		BannedAddrsList.Reset();
		BannedAddrsList.SetFormName("");
	};
};

BannedAddresses.prototype.SetFormName = function (name) {
	if (!name) {
		name = "Добавить запрет:";
	}
	this.SetTabElementValue("FORM_TITLE", name);
};

BannedAddresses.prototype.TemplateLoaded = function (req) {
	BannedAddrsList = this;
	this.TemplateBaseLoaded(req);

	this.GroupSelfAssign(["RefreshBannedAddresses", "ResetBannedAddresses"]);

	new DatePicker(this.inputs["TILL"]);

	/* Submit button */
	this.Tab.AddSubmitButton("SaveObject(this)", "", this);
	BindEnterTo(this.inputs["CONTENT"], this.Tab.SubmitButton);
	BindEnterTo(this.inputs["TILL"], this.Tab.SubmitButton);
};

/* Banned Address Data Transfer Object */

function badto(id, content, type, comment, admin, date, till, chat, forum, journal) {
	this.fields = ["Id", "Content", "Type", "Comment", "Admin", "Date", "Till", "Chat", "Forum", "Journal"];
	this.Init(arguments);

	this.Date = ParseDate(this.Date);
	this.Till = ParseDate(this.Till);

	this.BanNames = ["чат", "форум", "журналы"];
	this.Bans = [this.Chat, this.Forum, this.Journal];
};

badto.prototype = new DTO();

badto.prototype.ToString = function (index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
	var h2 = d.createElement("h2");
	h2.innerHTML = this.Content;
	td1.appendChild(h2);
	td1.appendChild(d.createTextNode("(" + this.Type + ")"));
	tr.appendChild(td1);

	var td2 = d.createElement("td");
	td2.appendChild(MakeDiv((this.Comment ? "&laquo;" + this.Comment + "&raquo;" + (this.Admin ? ",	" : "") : "") + (this.Admin ? this.Admin : ""), "h2"));
	td2.appendChild(MakeDiv("c " + this.Date.ToPrintableString() + (!this.Till.IsEmpty ? " по " + this.Till.ToPrintableString() : "")));

	result = "";
	var comma = false;
	for (var i = 0; i < 3; i++) {
		if (this.Bans[i]) {
			result += (comma ? ",	" : "") + this.BanNames[i];
			comma = true;
		}
	}

	td2.appendChild(MakeDiv("Запрет:	<b>" + result + "</b>"));
	tr.appendChild(td2);

	var td3 = d.createElement("td");
	td3.className = "Centered";
	td3.appendChild(MakeButton("EditBan(this," + this.Id + ")", "icons/edit.gif", obj));
	td3.appendChild(MakeButton("DeleteBan(this," + this.Id + ")", "delete_icon.gif", obj));
	tr.appendChild(td3);

	return tr;
};

/* Client events methods */

function SendItemRequest(a, id, go) {
	var s = new ParamsBuilder().add('go', go).add('id', id);
	a.obj.request(s.build());
};

function DeleteBan(a, id) {
	SendItemRequest(a, id, "delete");
};

function EditBan(a, id) {
	SendItemRequest(a, id, "edit");
};

function ResetBanForm(a) {
	var jc = a.obj;
	if (jc) {
		jc.Reset();
		jc.SetFormName("");
	}
};

/* Helper method */

var ipPattern = new RegExp("^([0-9]{1,3}\.){3}[0-9]{1,3}$");
function LockIP(a) {
	if (a.obj) {
		var profile = a.obj;

		var addr = profile["SESSION_ADDRESS"];
		if (addr) {
			var pos = addr.indexOf("[");
			if (pos > 0) {
				addr = addr.substr(0, pos - 1);
			}
			var obj = new BannedAddresses();
			obj.Tab = profile.Tab;
			profile.Tab.BannedAddresses = obj;

			var comment = "Доступ к чату для пользователя " + profile["LOGIN"];
			obj.FillFrom([-1, 1, 0, 0, addr.match(ipPattern) ? "ip" : "host", addr, comment, ""]);
			obj.USER_ID = profile["USER_ID"];
			obj.Save();
		}
	}
};
"use strict";

//2.0
/*
    Cookie helper hethods
*/

/* setCookie("foo", "bar", "Mon, 01-Jan-2001 00:00:00 GMT", "/"); */

var SESSION_KEY = "sdjfhk_session";

function setCookie(name, value, expires, path, domain, secure) {
    document.cookie = name + "=" + escape(value) + (expires ? "; expires=" + expires : "") + (path ? "; path=" + path : "") + (domain ? "; domain=" + domain : "") + (secure ? "; secure" : "");
};

function getCookie(name) {
    var cookie = " " + document.cookie;
    var search = " " + name + "=";
    var setStr = null;
    var offset = 0;
    var end = 0;
    if (cookie.length > 0) {
        offset = cookie.indexOf(search);
        if (offset != -1) {
            offset += search.length;
            end = cookie.indexOf(";", offset);
            if (end == -1) {
                end = cookie.length;
            }
            setStr = unescape(cookie.substring(offset, end));
        }
    }
    return setStr;
};

function GetCurrentSession() {
    return getCookie(SESSION_KEY);
};
"use strict";

//3.9
/*
	Special statuses management
*/

function Statuses() {
	this.fields = ["STATUS_ID", "RIGHTS", "COLOR", "TITLE"];
	this.ServicePath = servicesPath + "statuses.service.php";
	this.Template = "statuses";
	this.ClassName = "Statuses";
	this.GridId = "StatusesGrid";
	this.Columns = 4;
};

Statuses.prototype = new EditableGrid();

Statuses.prototype.BaseBind = function () {};

Statuses.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.Bind(this.data);
};

Statuses.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);
	this.GroupSelfAssign(["AddStatus", "RefreshStatuses"]);
};

/* Status Data Transfer Object */

function sdto(id, rights, color, title) {
	this.fields = ["Id", "Rights", "Color", "Title"];
	this.Init(arguments);
};

sdto.prototype = new EditableDTO();

sdto.prototype.ToShowView = function (index, obj) {
	var tr = MakeGridRow(index);

	// Rights
	var td1 = d.createElement("td");
	td1.className = "Centered";
	td1.innerHTML = this.Rights;
	tr.appendChild(td1);

	var td2 = d.createElement("td");
	td2.colSpan = 2;
	var div = MakeDiv("&nbsp;");
	div.className = "StatusColor";
	div.style.backgroundColor = this.Color;
	td2.appendChild(div);
	td2.appendChild(MakeDiv(this.Title));
	tr.appendChild(td2);
	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

sdto.prototype.ToEditView = function (index, obj) {
	var tr = MakeGridRow(index);

	// Rights
	var td1 = d.createElement("td");
	this.RightsInput = d.createElement("input");
	this.RightsInput.value = this.Rights;
	this.RightsInput.style.width = "30px";
	td1.appendChild(this.RightsInput);
	tr.appendChild(td1);

	var td2 = d.createElement("td");
	td2.style.width = "50px";
	td2.className = "Nowrap";
	this.ColorInput = d.createElement("input");
	this.ColorInput.value = this.Color;
	td2.appendChild(this.ColorInput);
	new ColorPicker(this.ColorInput);
	tr.appendChild(td2);

	var td22 = d.createElement("td");
	td22.style.width = "100%";
	this.TitleInput = d.createElement("input");
	this.TitleInput.value = this.Title;
	this.TitleInput.className = "Wide";
	td22.appendChild(this.TitleInput);
	tr.appendChild(td22);
	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

/* Helper methods */

function AddStatus(a) {
	if (a.obj) {
		a.obj.AddRow(new sdto(0, 1, "White", "Новый статус"));
	}
};
"use strict";

//2.3
/*
	Displaying/managing news sections
*/

function News() {
	this.fields = ["OWNER_ID", "TITLE", "DESCRIPTION"];
	this.ServicePath = servicesPath + "news.service.php";
	this.ClassName = "News";
	this.Template = "news";
	this.GridId = "NewsGrid";
	this.Columns = 2;
};

News.prototype = new EditableGrid();

News.prototype.BaseBind = function () {};

News.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.Bind(this.data);
};

News.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);
	this.GroupSelfAssign(["AddNews", "RefreshNews"]);

	this.Tab.NewsItems = this.inputs["NewsItems"];
};

/* News Data Transfer Object */

function ndto(id, title, description) {
	this.fields = ["Id", "Title", "Description"];
	this.Init(arguments);
};

ndto.prototype = new EditableDTO();

ndto.prototype.ToShowView = function (index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
	var h2 = d.createElement("h2");
	var a = MakeDiv(this.Title, "a");
	a.href = voidLink;
	a.onclick = function () {
		ShowNewsRecords(this);
	};
	a.obj = this;
	h2.appendChild(a);
	td1.appendChild(h2);
	td1.appendChild(MakeDiv(this.Description));
	tr.appendChild(td1);

	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

ndto.prototype.ToEditView = function (index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
	td1.appendChild(MakeDiv("Название:"));

	this.TitleInput = d.createElement("input");
	this.TitleInput.value = this.Title;
	this.TitleInput.className = "Wide";
	td1.appendChild(this.TitleInput);

	td1.appendChild(MakeDiv("Описание:"));

	this.DescriptionInput = d.createElement("textarea");
	this.DescriptionInput.innerHTML = this.Description;
	this.DescriptionInput.className = "Wide NewsDecription";
	td1.appendChild(this.DescriptionInput);
	tr.appendChild(td1);

	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

/* Helper methods */

function AddNews(a) {
	if (a.obj) {
		a.obj.AddRow(new ndto(0, "Новый раздел", ""));
	}
};

function ShowNewsRecords(a) {
	var tab = a.obj.Grid.Tab;
	if (tab.NewsItems) {
		tab.NewsItems.innerHTML = "";
		var s = new Spoiler(0, a.obj.Title, 0, 1);
		s.ToString(tab.NewsItems);

		new NewsRecords().loadTemplate(s, a.obj.Id);
	}
};
"use strict";

//3.7
/*
	Displaying news/guestbook messages grid
*/

function NewsRecords() {
	this.fields = ["NEWS_RECORD_ID", "OWNER_ID", "TITLE", "CONTENT", "IS_HIDDEN", "SEARCH_DATE", "SEARCH"];
	this.ServicePath = servicesPath + "news_records.service.php";
	this.Template = "news_records";
	this.ClassName = "NewsRecords";
	this.GridId = "NewsRecordsGrid";
	this.Columns = 2;
};

NewsRecords.prototype = new EditablePagedGrid();

NewsRecords.prototype.BaseBind = function () {};

NewsRecords.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.Bind(this.data, this.Total);
};

// Template loaded
NewsRecords.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);

	this.GroupSelfAssign(["buttonSearch", "ResetFilter", "linkRefresh", "AddNewsRecord", "RefreshNewsRecords"]);
	BindEnterTo(this.inputs["SEARCH"], this.inputs["buttonSearch"]);
	new DatePicker(this.inputs["SEARCH_DATE"]);
};

NewsRecords.prototype.CustomReset = function () {
	this.SetTabElementValue("SEARCH_DATE", "");
};

/* News Record Data Transfer Object */

function nrdto(id, owner_id, title, content, is_hidden) {
	this.fields = ["Id", "OwnerId", "Title", "Content", "IsHidden", "Date"];
	this.Init(arguments);
};

nrdto.prototype = new EditableDTO();

nrdto.prototype.ToShowView = function (index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
	var date = ParseDate(this.Date).ToPrintableString();
	td1.appendChild(MakeDiv(date + ":	" + this.Title, "h2"));
	td1.appendChild(MakeDiv(this.Content));
	tr.appendChild(td1);

	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

nrdto.prototype.ToEditView = function (index, obj) {
	var tr = MakeGridRow(index);

	var td1 = d.createElement("td");
	td1.appendChild(MakeDiv("Дата:", "h4"));

	this.DateInput = d.createElement("input");
	this.DateInput.value = this.Date;
	td1.appendChild(this.DateInput);
	new DatePicker(this.DateInput);

	td1.appendChild(MakeDiv("Заголовок:", "h4"));

	this.TitleInput = d.createElement("input");
	this.TitleInput.value = this.Title;
	this.TitleInput.className = "Wide";
	td1.appendChild(this.TitleInput);

	td1.appendChild(MakeDiv("Содержание:", "h4"));

	this.ContentInput = d.createElement("textarea");
	this.ContentInput.value = this.Content;
	this.ContentInput.className = "Wide NewsDescription";
	td1.appendChild(this.ContentInput);

	tr.appendChild(td1);
	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

/* Helper methods */

function AddNewsRecord(a) {
	if (a.obj) {
		a.obj.AddRow(new nrdto(0, a.obj["USER_ID"], "Новое сообщение", "", 0, ""));
	}
};
"use strict";

//1.0
/*
    Manage galleries and their contents
*/

function Galleries() {
    this.fields = ["OWNER_ID", "TITLE", "DESCRIPTION"];
    this.ServicePath = servicesPath + "gallery.service.php";
    this.Template = "gallery";
    this.GridId = "GalleriesGrid";
    this.Columns = 2;
};

Galleries.prototype = new EditableGrid();
"use strict";

//2.8
/*
	Wakeup messages grid. Edit & delete buttons.
*/

function Wakeups() {
	this.fields = new Array("SEARCH", "DATE", "IS_INCOMING", "IS_OUTGOING");
	this.ServicePath = servicesPath + "wakeups.service.php";
	this.Template = "wakeups";
	this.ClassName = "Wakeups";
	this.Columns = 3;
	this.PerPage = 20;

	this.GridId = "WakeupsGrid";
};

Wakeups.prototype = new PagedGrid();

Wakeups.prototype.InitPager = function () {
	this.Pager = new Pager(this.inputs[this.PagerId], function () {
		this.Tab.Wakeups.SwitchPage();
	}, this.PerPage);
};

Wakeups.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.Bind(this.data, this.Total);
};

Wakeups.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);

	this.GroupSelfAssign(["buttonSearch", "ResetFilter", "linkRefresh"]);

	BindEnterTo(this.inputs["SEARCH"], this.inputs["buttonSearch"]);
	BindEnterTo(this.inputs["IS_INCOMING"], this.inputs["buttonSearch"]);
	BindEnterTo(this.inputs["IS_OUTGOING"], this.inputs["buttonSearch"]);
	new DatePicker(this.inputs["DATE"]);
};

Wakeups.prototype.CustomReset = function () {
	this.SetTabElementValue("IS_INCOMING", 1);
	this.SetTabElementValue("IS_OUTGOING", 1);
};

/* Wakeup Record Data Transfer Object */

var lastWakeDate;

function wdto(id, user_id, user_name, is_incoming, date, content, is_read) {
	this.fields = ["Id", "UserId", "UserName", "IsIncoming", "Date", "Content", "IsRead"];
	this.Init(arguments);
};

wdto.prototype = new DTO();

wdto.prototype.ToString = function (index, obj, holder) {
	if (!index) {
		lastWakeDate = "";
	}

	var date = ParseDate(this.Date);
	var dateString = date.ToPrintableString();
	if (date && dateString && dateString != lastWakeDate && holder) {
		lastWakeDate = dateString;
		holder.appendChild(MakeGridSubHeader(index, obj.Columns, dateString));
	}

	var tr = MakeGridRow(index);
	tr.className += this.IsRead ? "" : " Unread";
	tr.className += this.IsIncoming == "1" ? " Incoming" : " Outgoing";

	var td0 = d.createElement("td");
	td0.className = "Centered";
	td0.innerHTML = date.Time();
	tr.appendChild(td0);

	var td1 = d.createElement("td");
	td1.className = "Centered";
	var sender = "<i>вы сами (" + this.UserName + ")</i>";
	if (!me || this.UserId != me.Id) {
		sender = (this.IsIncoming == "1" ? "от " : "для ") + this.UserName;
	}
	td1.innerHTML = sender;
	tr.appendChild(td1);

	var td2 = d.createElement("td");
	td2.innerHTML = this.Content;
	tr.appendChild(td2);

	return tr;
};
"use strict";

//3.3
/*
	Display messages log with filter by date, room & keywords
*/

function MessagesLog() {
	this.fields = ["DATE", "SEARCH", "ROOM_ID"];
	this.ServicePath = servicesPath + "messages.log.service.php";
	this.ClassName = "MessagesLog";
	this.Template = "messages_log";
	this.GridId = "MessagesLogGrid";
	this.Columns = 3;
	this.PerPage = 50;
};

MessagesLog.prototype = new PagedGrid();

MessagesLog.prototype.BaseBind = function () {};

MessagesLog.prototype.InitPager = function () {
	this.Pager = new Pager(this.inputs[this.PagerId], function () {
		this.Tab.MessagesLog.SwitchPage();
	}, this.PerPage);
};

MessagesLog.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.Bind(this.data, this.Total);
	this.BindRooms(this.Rooms);
};

MessagesLog.prototype.BindRooms = function (rooms) {
	var select = this.inputs["ROOM_ID"];
	if (select) {
		select.innerHTML = "";
		for (var i = 0, l = rooms.length; i < l; i++) {
			if (!this.ROOM_ID) {
				this.ROOM_ID = rooms[i].Id;
			}
			AddSelectOption(select, rooms[i].Title, rooms[i].Id, this.ROOM_ID == rooms[i].Id);
		}
	}
};

// Template loading
MessagesLog.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);

	this.GroupSelfAssign(["RefreshMessagesLog", "ResetFilter", "ROOM_ID"]);

	new DatePicker(this.inputs["DATE"]);

	BindEnterTo(this.inputs["DATE"], this.inputs["RefreshMessagesLog"]);
	BindEnterTo(this.inputs["ROOM_ID"], this.inputs["RefreshMessagesLog"]);
	BindEnterTo(this.inputs["SEARCH"], this.inputs["RefreshMessagesLog"]);

	if (this.Init) {
		this.Init();
	}
};

/* Message Data Transfer Object */

var lastMessageDate;

function mdto(date, name, name_to, text, color) {
	this.fields = ["Date", "Name", "NameTo", "Text", "Color"];
	this.Init(arguments);
};

mdto.prototype = new DTO();

mdto.prototype.ToString = function (index, obj, holder) {
	if (!index) {
		lastMessageDate = "";
	}

	var date = ParseDate(this.Date);
	var dateString = date.ToPrintableString();
	if (date && dateString && dateString != lastMessageDate && holder) {
		lastMessageDate = dateString;
		holder.appendChild(MakeGridSubHeader(index, obj.Columns, dateString));
	}

	var tr = MakeGridRow(index);
	if (this.NameTo) {
		tr.className += " Highlight Warning";
	}

	var td1 = d.createElement("td");
	td1.className = "Centered";
	td1.innerHTML = date.Time();
	tr.appendChild(td1);

	var td3 = d.createElement("td");
	if (this.Name) {
		var td2 = d.createElement("td");
		td2.innerHTML = this.Name + (this.NameTo ? " для " + this.NameTo : "&nbsp;");
		tr.appendChild(td2);
	} else {
		td3.colSpan = 2;
	}

	if (this.Color) {
		td3.style.color = this.Color;
	}
	td3.innerHTML = this.Text + "&nbsp;";
	tr.appendChild(td3);

	return tr;
};
"use strict";

//1.0
/*
    Base class for those who loads template only (static text)
    with no user actions involved
*/

function StaticText() {};

StaticText.prototype = new OptionsBase();

StaticText.prototype.request = function () {};

StaticText.prototype.TemplateLoaded = function (req) {
    this.TemplateBaseLoaded(req);
};
"use strict";

//1.0
/*
    Law code text
*/

function LawCode() {
    this.Template = "law";
    this.ClassName = "LawCode";
};

LawCode.prototype = new StaticText();
"use strict";

//5.1
/*
	Forum records tags (labels)
*/

var tagPattern = new RegExp("^[a-zA-Zа-я\ёА-Я\Ё0-9\-_\ ]+$", "gim");
var maxTags = 10;

function Tags() {
	this.fields = [];
	this.ServicePath = servicesPath + "tags.service.php";
	this.Template = "tags";
	this.ClassName = "Tags";

	this.IsLoaded = 0;
};

Tags.prototype = new OptionsBase();

Tags.prototype.Bind = function (data, found) {
	if (data && data.length > 0 && !this.IsLoaded) {
		var s = "";
		this.SetTabElementValue("TagsContainer", "");
		this.Tab.AddedTags.Clear();
		var holder = this.inputs["TagsContainer"];

		for (var i = 0, l = data.length; i < l; i++) {
			data[i].obj = this;
			this.Tab.AddedTags.Add(data[i]);
			s += data[i].ToString(holder, i);
		}
		this.IsLoaded = 1;
	}

	if (found) {
		var s = "";
		var holder = this.inputs["FoundTags"];
		holder.innerHTML = "";
		for (var i = 0, l = found.length; i < l; i++) {
			found[i].obj = this;
			found[i].ToSelect(holder);
		}
	}
};

Tags.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.FillFrom(this.data);
	this.Bind(this.data, this.found);
};

Tags.prototype.TemplateLoaded = function (req) {
	this.Tab.AddedTags = new Collection();

	this.RECORD_ID = this.Tab.RECORD_ID;

	this.TemplateBaseLoaded(req);
	this.FindRelatedControls();

	this.AssignSelfTo("AddTag");

	// Validation
	this.Tab.Validators = new ValidatorsCollection();
	this.Tab.Validators.Add(new Validator(this.inputs["SEARCH_TAG"], new MatchPattern(tagPattern), "Тег содержит запрешённые символы&nbsp;(разрешено a-z а-я 0-9 -_)", Random(10000)));
	this.Tab.Validators.Init(this.inputs["Errors"]);

	var req = new delayedRequestor(this, this.inputs["SEARCH_TAG"]);
	req.Submitter = this.inputs["AddTag"];
};

Tags.prototype.request = function (params, callback) {
	var s = new ParamsBuilder(params).add('RECORD_ID', this.RECORD_ID);
	this.BaseRequest(s.build(), callback);
};

Tags.prototype.AddNewTag = function (input) {
	if (input && input.obj) {
		var value = input.obj.inputs["SEARCH_TAG"].value;
		var tag = new tagdto(value, value);
		tag.obj = this;
		this.AT(tag);
	}
};

Tags.prototype.AT = function (tag) {
	if (this.Tab.AddedTags.Count() >= maxTags) {
		this.inputs["Errors"].innerHTML = "<li> Можно добавить не более " + maxTags + " тегов";
		return false;
	}
	this.Tab.AddedTags.Add(tag);
	this.ShowTags();
	this.inputs["SEARCH_TAG"].value = "";
	return true;
};

Tags.prototype.DT = function (id) {
	this.Tab.AddedTags.Delete(id);
	this.ShowTags();
};

Tags.prototype.ShowTags = function () {
	this.SetTabElementValue("TagsContainer", this.Tab.AddedTags.Count() > 0 ? "" : "не указаны");
	this.Tab.AddedTags.ToString(this.inputs["TagsContainer"]);
};

/*
	Tag Data Transfer Object
*/

function tagdto(id, title) {
	this.fields = ["Id", "Title"];
	this.Init(arguments);
};

tagdto.prototype = new DTO();

tagdto.prototype.ToString = function (holder, index) {
	holder.appendChild(d.createTextNode((index ? ",	" : "") + this.Id));
	var a = d.createElement("a");
	a.href = voidLink;
	a.className = "CloseSign Small";
	a.obj = this;
	a.onclick = function () {
		this.obj.obj.DT(this.obj.Id);
	};
	a.innerHTML = "x";
	holder.appendChild(a);
};

tagdto.prototype.ToSelect = function (holder) {
	var li = d.createElement("li");
	var a = d.createElement("a");
	a.href = voidLink;
	a.obj = this;
	a.onclick = function () {
		this.obj.obj.AT(this.obj);
	};
	a.innerHTML = this.Title;
	li.appendChild(a);
	holder.appendChild(li);
};

tagdto.prototype.Gather = function (index) {
	return (index ? "|" : "") + this.Title;
};
"use strict";

//4.1
/*
    Scheduled Tasks management
*/

function ScheduledTasks() {
    this.fields = ["SCHEDULED_TASK_ID", "TYPE", "EXECUTION_DATE", "PERIODICITY", "IS_ACTIVE", "inactivated", "status", "unban", "expired_sessions", "ratings"];
    this.ServicePath = servicesPath + "scheduled_tasks.service.php";
    this.Template = "scheduled_tasks";
    this.ClassName = "ScheduledTasks";
    this.GridId = "ScheduledTasksGrid";
    this.Columns = 5;
};

ScheduledTasks.prototype = new EditablePagedGrid();

ScheduledTasks.prototype.BaseBind = function () {};

ScheduledTasks.prototype.InitPager = function () {
    this.Pager = new Pager(this.inputs[this.PagerId], function () {
        this.Tab.ScheduledTasks.SwitchPage();
    }, this.PerPage);
};

ScheduledTasks.prototype.requestCallback = function (req) {
    this.requestBaseCallback(req);
    this.Bind(this.data, this.Total);
};

ScheduledTasks.prototype.TemplateLoaded = function (req) {
    this.TemplateBaseLoaded(req);
    this.GroupSelfAssign(["RefreshScheduledTasks"]);

    // System log checkboxes
    BindEnterTo(this.inputs["status"], this.inputs["RefreshScheduledTasks"]);
    BindEnterTo(this.inputs["unban"], this.inputs["RefreshScheduledTasks"]);
    BindEnterTo(this.inputs["expired_sessions"], this.inputs["RefreshScheduledTasks"]);
    BindEnterTo(this.inputs["ratings"], this.inputs["RefreshScheduledTasks"]);
    BindEnterTo(this.inputs["inactivated"], this.inputs["RefreshScheduledTasks"]);
};

/* Status Data Transfer Object */

function stdto(id, rights, color, title) {
    this.fields = ["Id", "Type", "ExecutionDate", "Periodicity", "IsActive"];
    this.Init(arguments);
};

stdto.prototype = new EditableDTO();

stdto.prototype.ToShowView = function (index, obj) {
    var tr = MakeGridRow(index);

    var td1 = d.createElement("td");
    td1.className = "Centered";
    td1.appendChild(CreateBooleanImage(this.IsActive));
    tr.appendChild(td1);

    var td2 = d.createElement("td");
    td2.innerHTML = this.Type;
    tr.appendChild(td2);

    var td3 = d.createElement("td");
    td3.innerHTML = this.ExecutionDate;
    tr.appendChild(td3);

    var td4 = d.createElement("td");
    td4.innerHTML = this.Periodicity;
    tr.appendChild(td4);

    tr.appendChild(this.MakeButtonsCell());
    return tr;
};

stdto.prototype.ToEditView = function (index, obj) {
    var tr = MakeGridRow(index);

    // Rights
    var td1 = d.createElement("td");
    td1.className = "Centered";
    this.IsActiveInput = CreateCheckBox("IsActive", this.IsActive);
    td1.appendChild(this.IsActiveInput);
    tr.appendChild(td1);

    var td2 = d.createElement("td");
    td2.innerHTML = this.Type;
    tr.appendChild(td2);

    var td3 = d.createElement("td");

    this.ExecutionDateInput = d.createElement("input");
    this.ExecutionDateInput.value = this.ExecutionDate;
    td3.appendChild(this.ExecutionDateInput);
    new DatePicker(this.ExecutionDateInput, 1);

    tr.appendChild(td3);

    var td4 = d.createElement("td");
    this.PeriodicityInput = d.createElement("input");
    this.PeriodicityInput.value = this.Periodicity;
    this.PeriodicityInput.className = "Wide";
    td4.appendChild(this.PeriodicityInput);
    tr.appendChild(td4);

    tr.appendChild(this.MakeButtonsCell());
    return tr;
};
"use strict";

//1.0
/*
    Timing functions
*/

function Wait(milliseconds) {
    var date = new Date();
    var curDate = null;

    do {
        curDate = new Date();
    } while (curDate - date < milliseconds);
};
"use strict";

//2.8
/*
	Rooms management
*/

function Rooms() {
	this.fields = ["ROOM_ID", "TITLE", "IS_DELETED", "IS_LOCKED", "IS_INVITATION_REQUIRED", "locked", "by_invitation", "deleted"];
	this.ServicePath = servicesPath + "rooms.service.php";
	this.Template = "rooms";
	this.ClassName = "Rooms";
	this.GridId = "RoomsGrid";
	this.Columns = 2;
};

Rooms.prototype = new EditableGrid();

Rooms.prototype.BaseBind = function () {};

Rooms.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.Bind(this.data);
};

Rooms.prototype.request = function (params, callback) {
	if (!params) {
		params = this.Gather();
	}
	this.ClearRecords(true);
	this.BaseRequest(params, callback);
	this.HasEmptyRow = false;
};

Rooms.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);
	this.GroupSelfAssign(["AddRoom", "RefreshRooms"]);

	// System log checkboxes
	BindEnterTo(this.inputs["locked"], this.inputs["RefreshRooms"]);
	BindEnterTo(this.inputs["by_invitation"], this.inputs["RefreshRooms"]);
	BindEnterTo(this.inputs["deleted"], this.inputs["RefreshRooms"]);
};

/* Room Data Transfer Object editable methods */

rdto.prototype.ToShowView = function (index, obj) {
	var tr = MakeGridRow(index);

	var td2 = d.createElement("td");
	if (this.IsDeleted) {
		td2.className = "Deleted";
	}
	if (this.IsInvitationRequired) {
		td2.className += " Dude";
	} else if (this.IsLocked) {
		td2.className += " Locked";
	}
	td2.innerHTML = this.Title;
	tr.appendChild(td2);
	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

rdto.prototype.ToEditView = function (index, obj) {
	var tr = MakeGridRow(index);

	var td2 = d.createElement("td");
	this.TitleInput = d.createElement("input");
	this.TitleInput.className = "Wide";
	this.TitleInput.value = this.Title;
	td2.appendChild(this.TitleInput);

	this.IsLockedInput = CreateCheckBox("IsLocked", this.IsLocked);
	td2.appendChild(this.IsLockedInput);
	td2.appendChild(CreateLabel(this.IsLockedInput, "заблокированная"));

	this.IsInvitationRequiredInput = CreateCheckBox("IsInvitationRequired", this.IsInvitationRequired);
	td2.appendChild(this.IsInvitationRequiredInput);
	td2.appendChild(CreateLabel(this.IsInvitationRequiredInput, "по приглашению"));

	this.IsDeletedInput = CreateCheckBox("IsDeleted", this.IsDeleted);
	td2.appendChild(this.IsDeletedInput);
	td2.appendChild(CreateLabel(this.IsDeletedInput, "удалённая"));

	tr.appendChild(td2);

	tr.appendChild(this.MakeButtonsCell());
	return tr;
};

/* Helper methods */

function AddNewRoom(a) {
	if (a.obj) {
		a.obj.AddRow(new rdto(0, "", 0, 0, 0));
	}
};
"use strict";

//2.3
/*
    Bots creation
*/

function Bots() {
    this.fields = ["TYPE", "FIND_USER", "BOT_USER_ID", "ROOM"];
    this.ServicePath = servicesPath + "bots.service.php";
    this.Template = "bots";
    this.ClassName = "Bots";
};

Bots.prototype = new OptionsBase();

Bots.prototype.Bind = function (data) {
    if (data) {
        var s = "";
        var holder = this.Inputs["FoundUsers"];
        holder.innerHTML = "";

        for (var i = 0, l = data.length; i < l; i++) {
            holder.appendChild(data[i].ToLiString(i, data[i], this));
        }
    }
};

Bots.prototype.RequestCallback = function (req, obj) {
    if (obj) {
        obj.RequestBaseCallback(req, obj);
        obj.FillFrom(obj.data);
        obj.Bind(obj.data);
    }
};

Bots.prototype.TemplateLoaded = function (req) {
    this.TemplateBaseLoaded(req);
    this.FindRelatedControls();

    var a = new DelayedRequestor(this, this.Inputs["FIND_USER"]);

    // Filling Rooms ddl
    BindRooms(this.Inputs["ROOM"]);

    /* Submit button */
    this.Tab.AddSubmitButton("SaveObject(this)", "", this);
};

Bots.prototype.Select = function (obj) {
    this.SetTabElementValue("SELECTED_HOLDER", StripTags(obj.Login) + " (ID: " + obj.Id + ")");
    this.SetTabElementValue("BOT_USER_ID", obj.Id);
    this.SetTabElementValue("FIND_USER", "");
    this.SetTabElementValue("FoundUsers", "");
};

Bots.prototype.Preset = function (input, name) {};
"use strict";

//1.0
/*
    Service class for authorization pop-up.
*/

/* Change Nickname class */

function AuthForm() {
    this.Template = "auth_form";
};

AuthForm.prototype.CreateControls = function (container) {
    this.Holder = d.createElement("div");
    this.Holder.className = "AuthForm";

    this.Holder.innerHTML = loadingIndicator;

    container.appendChild(this.Holder);
};

AuthForm.prototype.TemplateLoaded = function (responseText) {
    KeepRequestedContent(this.Template, responseText);
    this.Holder.innerHTML = responseText;
};

AuthForm.prototype.requestData = function () {
    RequestContent(this);
};
"use strict";

//2.0
/*
    OpenIDs associated with this user
*/

var OpenIdProviders = [];

function OpenIds() {
    this.fields = ["USER_OPENID_ID", "OPENID_PROVIDER_ID", "LOGIN"];
    this.ServicePath = servicesPath + "openids.service.php";
    this.Template = "openids";
    this.ClassName = "OpenId";
    this.GridId = "OpenIdsGrid";
    this.Columns = 3;
};

OpenIds.prototype = new EditableGrid();

OpenIds.prototype.BaseBind = function () {};

OpenIds.prototype.requestCallback = function (req) {
    this.requestBaseCallback(req);
    this.Bind(this.data);
};

OpenIds.prototype.TemplateLoaded = function (req) {
    this.TemplateBaseLoaded(req);
    this.GroupSelfAssign(["AddOpenId", "RefreshOpenIds"]);
};

/* UserOpenId Data Transfer Object */

function oidto(id, provider_id, login) {
    this.fields = ["Id", "ProviderId", "Login"];
    this.Init(arguments);
    if (provider_id && OpenIdProviders[provider_id]) {
        this.Provider = OpenIdProviders[provider_id];
    } else {
        this.Provider = new oipdto(0, "Unknown", "");
    }
};

oidto.prototype = new EditableDTO();

oidto.prototype.ToShowView = function (index, obj) {
    var tr = MakeGridRow(index);

    // Rights
    var td1 = d.createElement("td");
    td1.className = "Centered";
    td1.appendChild(this.Provider.ToPrint());
    tr.appendChild(td1);

    var td2 = d.createElement("td");
    td2.style.verticalAlign = "middle";
    td2.innerHTML = this.Login;
    tr.appendChild(td2);

    tr.appendChild(this.MakeButtonsCell());
    return tr;
};

oidto.prototype.ToEditView = function (index, obj) {
    var tr = MakeGridRow(index);

    // Rights
    var td1 = d.createElement("td");
    var input = d.createElement("input");
    input.type = "hidden";
    input.value = this.ProviderId;
    td1.appendChild(input);

    for (p in OpenIdProviders) {
        td1.appendChild(OpenIdProviders[p].ToSelect(this.ProviderId, input));
    }
    this.ProviderIdInput = input;
    tr.appendChild(td1);

    var td2 = d.createElement("td");
    this.LoginInput = d.createElement("input");
    this.LoginInput.value = this.Login;
    this.LoginInput.className = "Wide";
    td2.appendChild(this.LoginInput);
    tr.appendChild(td2);

    tr.appendChild(this.MakeButtonsCell());
    return tr;
};

/* OpenId Provider Data Transfer Object */

function oipdto(id, title, image) {
    this.fields = ["Id", "Title", "Image"];
    this.Init(arguments);
};

oipdto.prototype = new DTO();

oipdto.prototype.ToPrint = function () {
    var img = new Image();
    img.src = openIdPath + this.Image;
    img.title = this.Title;
    return img;
};

var selectedLink;

oipdto.prototype.ToSelect = function (id, input) {
    this.Input = input;

    var a = d.createElement("a");
    a.href = voidLink;
    if (id == this.Id) {
        a.className = "Selected";
        selectedLink = a;
    }
    a.obj = this;
    a.onclick = this.Select;
    a.appendChild(this.ToPrint());
    return a;
};

oipdto.prototype.Select = function () {
    this.obj.Input.value = this.obj.Id;
    if (selectedLink) {
        selectedLink.className = "";
    }
    selectedLink = this;
    this.className = "Selected";
    this.blur();
};

/* Helper methods */

function AddOpenId(a) {
    if (a.obj) {
        a.obj.AddRow(new oidto(0, 1, "login"));
    }
};
"use strict";

//2.0
/* 
	Chat properties initialization 
*/

var users = new Collection();
var rooms = new Collection();
var recepients = new Collection();
var co = new Confirm();

// Below values to be updated with
// values received from server

var CurrentRoomId = 1;
var me = "";

// OnLoad actions

var menuInitilized = 0;
function InitMenu(div) {
	var menu = new MenuItemsCollection(true);
	var main = new MenuItem(1, "Команды");

	/*	main.SubItems.Items.Add(new MenuItem(1, "/me сообщение", "MI('me')"));
 		var w = new MenuItem(2, "Вейкап");
 		w.SubItems.Items.Add(new MenuItem(1, "Поиск...", "", 1));
 		main.SubItems.Items.Add(w);*/
	main.SubItems.Items.Add(new MenuItem(3, "Отойти&nbsp;(Away)", "MI('away')"));
	main.SubItems.Items.Add(new MenuItem(4, "Сменить статус", "MI('status')"));

	main.SubItems.Items.Add(new MenuItem(5, "Сменить никнейм", "ChangeName()"));
	if (me.Rights >= topicRights) {
		var t = new MenuItem(6, "Сменить тему", "MI('topic')");
		if (me.Rights >= adminRights) {
			t.SubItems.Items.Add(new MenuItem(1, "С блокировкой", "MI('locktopic')"));
			t.SubItems.Items.Add(new MenuItem(2, "Разблокировать", "MI('unlocktopic')"));
		}
		main.SubItems.Items.Add(t);
	}
	main.SubItems.Items.Add(new MenuItem(7, "<b>Меню</b>", "ShowOptions()"));
	main.SubItems.Items.Add(new MenuItem(8, "Выход из чата", "MI('quit')"));

	menu.Items.Add(main);
	menu.Create(div);
	menuInitilized = 1;
};

function OnLoad() {
	DisplayElement(alerts.element, false);
	co.Init("AlertContainer", "AlertBlock");
	if (window.Pong) {
		Ping();
	}
	if (window.OpenReplyForm) {
		OpenReplyForm();
	}
};
"use strict";

//1.0
/*
	Functions to be overridden.
*/

var spoilerNames = [];
var spoilerInits = [];

function showBlog() {};
function showUser() {};
function createAdminTab() {};
function umExtraButtons() {};
function umAdditionalExtraButtons() {};
"use strict";

//2.7
/*
	Admin only functionality.
	Will be loaded only if server rights checking is == adminRights.
*/

/* Options Admin tab */

function createAdminTab() {
    return new Tab(7, "Администрирование", 1, "", function (tab) {
        new AdminOptions().loadTemplate(tab, me.Id);
    });
};

/* Usermanager admins' section */

function umExtraButtons(tr, id, login, obj) {
    var td1 = makeSection("Опции администратора:");
    var ul1 = d.createElement("ul");

    ul1.appendChild(makeUserMenuLink(makeButtonLink("showUser(" + id + ",'" + login + "')", "Профиль", obj, "")));
    if (me.isSuperAdmin()) {
        umAdditionalExtraButtons(ul1, id, login, obj);
    }
    td1.appendChild(ul1);
    tr.appendChild(td1);

    var td2 = makeSection("Операции:", "Red");
    var ul2 = d.createElement("ul");
    ul2.appendChild(makeUserMenuLink(makeButtonLink("deleteUser(" + id + ",'" + login + "', this)", "Удалить", obj, "Red")));
    td2.appendChild(ul2);
    tr.appendChild(td2);
};

/* Custom Tabs creation */

function customTab(id, name, o, tab_prefix, name_prefix) {
    var tab_id = tab_prefix + id;
    createUserTab(id, name, new o(), name_prefix, "", tab_id);
};

function showUser(id, name) {
    customTab(id, name, Profile, "u", "");
};

function deleteUser(id, name, a) {
    co.AlertType = false;
    co.Show(function () {
        deleteUserConfirmed(id, a.obj);
    }, "Удалить пользователя?", "Пользователь	<b>" + name + "</b> и все данные,	относящиеся к нему	(фотографии,	записи в журнале и форумах,	профиль) будут удалены.<br>Вы уверены?");
};

function deleteUserConfirmed(id, obj) {
    var req = new Requestor(servicesPath + "user_delete.service.php", obj);
    req.callback = refreshList;
    req.request(["user_id"], [id]);
    obj.inputs["FILTER_BANNED"].delayedRequestor.request();
};

/* Admin Options */

var spoilerNames = ["Кодекс администратора	(обязателен для прочтения)", "Запреты", "Комнаты"];

var spoilerInits = [function (tab) {
    new LawCode().loadTemplate(tab);
}, function (tab) {
    new BannedAddresses().loadTemplate(tab);
}, function (tab) {}];
'use strict';

//2.6
/*
    Collection of entities (users, rooms etc.)
*/

function Collection() {
    this.Base = {};
    this.LastId = null;
};

Collection.prototype.Get = function (id) {
    if (this.Base['_' + id]) {
        return this.Base['_' + id];
    }
    return null;
};

Collection.prototype.Add = function (e) {
    this.Base['_' + e.Id] = e;
    this.LastId = e.Id;
};

Collection.prototype.BulkAdd = function (elements) {
    elements.forEach(function (e) {
        if (!e.Id) e.Id = _.uniqueId();
        this.Add(e);
    });
};

Collection.prototype.Delete = function (id) {
    if (this.Base['_' + id]) delete this.Base['_' + id];
};

Collection.prototype.Clear = function () {
    this.Base = {};
};

Collection.prototype.Count = function () {
    return _.size(this.Base);
};

Collection.prototype.invoke = function (method, holder) {
    var index = 0;
    return _.reduce(this.Base, function (result, element) {
        if (element[method]) {
            if (holder) {
                element[method](holder, index++);
            } else {
                result += element[method](index++);
            }
        }
        return result;
    }, '');
};

Collection.prototype.ToString = function (holder) {
    return this.invoke('ToString', holder);
};

Collection.prototype.Gather = function (holder) {
    return this.invoke('Gather', holder);
};
"use strict";

/* Service methods */

function getElement(el) {
    if (el && (el.nodeType || el.jquery)) {
        return el;
    }
    return "#" + el;
};

var displayOptions = { effect: "fade", easing: "easeInOutBack", duration: 600 };

function displayElement(el, state) {
    if (!el) {
        return;
    }

    el = getElement(el);
    if (state) {
        $(el).show();
    } else {
        $(el).hide();
    }
}

function doShow(el) {
    displayElement(el, true);
};

function doHide(el) {
    displayElement(el, false);
};

function switchVisibility(el) {
    el = getElement(el);
    $(el).toggle(displayOptions);
};

var empty_pass = "**********";
function clearInput(el) {
    if (el.value == empty_pass) {
        el.value = "";
    } else {
        el.previousValue = el.value;
    }
};

function restoreInput(el, relatedBlockId) {
    if (el.value != el.previousValue) {
        displayElement(relatedBlockId, el.value);
    }
    if (!el.value) {
        el.value = empty_pass;
    }
};

/*
   Workaround the IE bug with assigning
   names to dynamically created elements
   Upd.: Opera has document.all property
   Upd.: Mozilla doesn't know try-catch
*/
function createElement(tag, name) {
    var result;
    if (d.all) {
        try {
            result = d.createElement("<" + tag + (name ? " name=\"" + name + "\" id=\"" + name + "\"" : "") + ">");
        } catch (e) {}
    }
    if (!result) {
        result = d.createElement(tag);
        if (name) {
            result.name = name;
            result.id = name;
        }
    }
    return result;
};

function CreateBitInput(name, checked, is_radio) {
    var result;
    var type = is_radio ? "radio" : "checkbox";

    if (d.all) {
        try {
            result = d.createElement("<input type=\"" + type + "\" name=\"" + name + "\"" + (is_radio ? "" : " id=\"" + name + "\"") + (checked ? " checked" : "") + ">");
        } catch (e) {}
    }
    if (!result) {
        result = d.createElement("input");
        result.type = type;
        result.name = name;
        if (!is_radio) {
            result.id = name;
        }
        if (checked) {
            result.setAttribute("checked", "true");
        }
    }
    return result;
};

function CreateRadio(name, checked) {
    return CreateBitInput(name, checked, 1);
};

function CreateCheckBox(name, checked) {
    return CreateBitInput(name, checked, 0);
};

function CreateLabel(target, text) {
    label = createElement("label");
    label.innerHTML = text;
    if (target) {
        label.setAttribute("for", target.name ? target.name : target);
    }
    return label;
};

function CreateBooleanImage(state) {
    var img = new Image();
    img.src = state ? "/img/icons/done.gif" : "/img/delete_icon.gif";
    return img;
};

function CheckEmpty(value, def) {
    if (!value || value == "undefined") {
        value = def ? def : "";
    }
    return value;
};

function makeButtonLink(target, text, obj, css, alt) {
    var a = d.createElement("a");
    a.href = voidLink;
    a.className = css;
    a.obj = obj;
    if (text) {
        a.innerHTML = text;
    }
    alt = CheckEmpty(alt);
    a.alt = alt;
    a.title = alt;
    eval("a.onclick=function(){" + target + "}");
    return a;
};

function MakeButton(target, src, obj, css, alt) {
    alt = CheckEmpty(alt);
    css = CheckEmpty(css);

    var a = makeButtonLink(target, "", obj, css, alt);
    a.className = "Button " + css;
    a.innerHTML = "<img src='" + imagesPath + src + "' alt='" + alt + "' title='" + alt + "' />";
    return a;
};

function MakeDiv(text, tag) {
    var div = d.createElement(tag ? tag : "div");
    div.innerHTML = text;
    return div;
};

function IndexElementChildElements(el, hash) {
    if (!hash) hash = [];

    if (el) {
        if (el.id) hash[el.id] = el;

        _.each(el.childNodes, function (child) {
            IndexElementChildElements(child, hash);
        });
    }
    return hash;
};

function insertAfter(new_node, existing_node) {
    if (existing_node.nextSibling) {
        existing_node.parentNode.insertBefore(new_node, existing_node.nextSibling);
    } else {
        existing_node.parentNode.appendChild(new_node);
    }
};

function BindList(list, holder, obj, empty_message) {
    var l = list.length;
    if (l) {
        for (var i = 0; i < l; i++) {
            holder.appendChild(list[i].ToString(i, obj));
        }
    } else {
        holder.innerHTML = empty_message;
    }
};

/* Methods to set/get value of group of radios in holder element */

function RenameRadioGroup(holder, name) {
    if (!name) {
        name = "group" + Math.random(9999);
    }
    for (var i = 0, l = holder.childNodes.length; i < l; i++) {
        var el = holder.childNodes[i];
        if (el && el.type == "radio") {
            el.name = name;
        }
    }
};

// Recursive methods
function SetRadioValue(holder, value) {
    result = "";
    for (var i = 0, l = holder.childNodes.length; i < l; i++) {
        var el = holder.childNodes[i];
        if (el.hasChildNodes()) {
            result = result || SetRadioValue(el, value);
        } else if (el && el.type == "radio") {
            m = el.value == "" + value;
            el.checked = m;
            result = m ? el : result;
        }
    }
    return result;
};

function GetRadioValue(holder) {
    for (var i = 0, l = holder.childNodes.length; i < l; i++) {
        var el = holder.childNodes[i];
        if (el.hasChildNodes()) {
            var v = GetRadioValue(el);
            if (v) {
                return v;
            }
        } else if (el && el.type == "radio") {
            if (el.checked) {
                return el.value;
            }
        }
    }
    return "";
};

// Bubbling

function CancelBubbling(e) {
    if (!e) {
        var e = window.event;
    }
    e.cancelBubble = true;
    if (e.stopPropagation) {
        e.stopPropagation();
    }
};

// Create Pop & Push methods For Array if not supported

function ArrayInsertFirst(a, p, len) {
    if (!a) {
        return false;
    }
    for (var i = a.length - 1; i >= 0; i--) {
        a[i + 1] = a[i];
    }
    a[0] = p;
    if (a.length > len) {
        a.length = len;
    }
};

// DOM Helper methods

function AddSelectOption(select, name, value, selected) {
    var opt = d.createElement("option");
    opt.value = value;
    opt.text = name;
    opt.selected = selected ? true : false;

    try {
        select.add(opt, null); // standards compliant; doesn't work in IE
    } catch (ex) {
        select.add(opt); // IE only
    }
};

function Random(n, not_null) {
    return (not_null ? 1 : 0) + Math.round(n * Math.random());
};
// Bind ddl with all rooms
function BindRooms(ddl) {
    if (ddl && opener && opener.rooms) {
        opener.rooms.Gather(ddl);
    }
};
"use strict";

//2.2
/*
    Menu items
*/

var menuItemWidth = 100;
var menuItemHeight = 20;

function MenuItem(id, title, action, is_locked) {
    this.Id = id;
    this.Title = title;
    this.Action = action;
    this.IsLocked = is_locked;
    this.SubItems = new MenuItemsCollection();
};

MenuItem.prototype.Gather = function (holder) {
    var a = d.createElement('a');
    a.innerHTML = this.Title;
    a.href = voidLink;
    if (this.Action) {
        a.onclick = this.Action;
    };

    var li = document.createElement("li");
    li.RelatedItem = this;
    li.onmouseover = function () {
        DisplaySubmenu(this, true);
    };
    li.onmouseout = function () {
        DisplaySubmenu(this, false);
    };
    li.onclick = li.onmouseout;

    if (this.SubItems.Items.Count() > 0) {
        this.SubItems.Create(li);
    }

    li.appendChild(a);
    holder.appendChild(li);
};

function MenuItemsCollection(shown) {
    this.Items = new Collection();
    this.Container = document.createElement("ul");
    if (!shown) {
        displayElement(this.Container, false);
    }
};

MenuItemsCollection.prototype.Create = function (where) {
    this.Container.innerHTML = "";
    if (this.Items.Count() > 0) {
        this.Items.Gather(this.Container);
        where.appendChild(this.Container);
    }
};

MenuItemsCollection.prototype.Display = function (state) {
    displayElement(this.Container, state);
};

function DisplaySubmenu(el, state, force) {
    if (el.RelatedItem && el.RelatedItem.SubItems) {
        el.RelatedItem.SubItems.Display(state);
        el.className = state ? "Selected" : "";
    }
};
'use strict';

/*

MyFrame class
Handles window resize and update object's properties correspondingly

*/
function MyFrame(obj, min_width, min_height) {
    this.x = 0;
    this.y = 0;
    this.width = 0;
    this.height = 0;

    this.element = obj || {};
    this.minWidth = min_width ? parseInt(min_width) : 0;
    this.minHeight = min_height ? parseInt(min_height) : 0;
    this.GetPosAndSize();
};

MyFrame.prototype.GetPosAndSize = function () {
    if (this.element === window) {
        return this.GetWindowSize();
    }

    this.width = parseInt(this.element.clientWidth);
    this.height = parseInt(this.element.clientHeight);

    var obj = this.element;

    if (this.element.offsetParent) {
        this.x = obj.offsetLeft;
        this.y = obj.offsetTop;
        while (obj = obj.offsetParent) {
            this.x += obj.offsetLeft;
            this.y += obj.offsetTop;
        }
    }
};

MyFrame.prototype.GetWindowSize = function () {
    this.x = 0;
    this.y = 0;

    if (self.innerWidth) {
        this.width = self.innerWidth;
        this.height = self.innerHeight;
    } else if (document.documentElement && document.documentElement.clientWidth) {
        this.width = document.documentElement.clientWidth;
        this.height = document.documentElement.clientHeight;
    } else if (document.body) {
        this.width = document.body.clientWidth;
        this.height = document.body.clientHeight;
    }

    if (navigator.appVersion.indexOf("Chrome") > 0) {
        this.height -= 24;
    }
};

MyFrame.prototype.Replace = function (x, y, w, h) {
    if (this.element == window || !this.element.style) {
        return;
    }

    if (x >= 0) {
        this.element.style.left = x + 'px';
    }
    if (y >= 0) {
        this.element.style.top = y + 'px';
    }
    if (w >= 0) {
        if (w < this.minWidth) {
            w = this.minWidth;
        }
        this.element.style.width = w + 'px';
    }
    if (h >= 0) {
        if (h < this.minHeight) {
            h = this.minHeight;
        }
        this.element.style.height = h + 'px';
    }
    this.GetPosAndSize();
};

MyFrame.prototype.Info = function () {
    var s = 'x=' + this.x + ', ';
    s += 'y=' + this.y + ', ';
    s += 'width=' + this.width + ', ';
    s += 'height=' + this.height;
    return s;
};
'use strict';

//2.7
/*
	Custom confirm contents.
*/

/* Nickname class */

var nicknames = new Collection();
var nicknames1;
var max_names = 5;
var name_length = 20;

function Nickname(index, id, name) {
	this.Index = index;
	this.Id = id;
	this.OldName = name;
	this.Name = name;
	this.Mode = 'show';
};

Nickname.prototype.IsEmpty = function () {
	return this.Name == '';
};

Nickname.prototype.HasChanged = function () {
	return this.OldName != this.Name;
};

Nickname.prototype.CreateButton = function (src, action) {
	var button = d.createElement('input');
	button.type = 'image';
	button.RelatedItem = this;
	eval('button.onclick = function(){' + action + '}');
	button.className = 'Button';
	button.style.width = '15px';
	button.style.height = '15px';
	button.src = imagesPath + src;
	return button;
};

Nickname.prototype.CreateViewControls = function () {
	this.Div.innerHTML = '';
	if (this.Mode == 'show') {
		this.Div.innerHTML += (this.Name ? this.Name + (this.Name == me.Login ? '&nbsp;(ваш логин)' : '') : '&lt;не задано&gt;') + '&nbsp;';
		if (this.Id) {
			this.Div.appendChild(this.CreateButton('edit_icon.gif', 'Edit(this)'));
			if (this.Name) {
				this.Div.appendChild(this.CreateButton('delete_icon.gif', 'Clear(this)'));
			}
		}
	} else {
		this.Input = d.createElement('input');
		this.Input.className = 'NewNick';
		this.Input.value = this.Name;
		this.Input.setAttribute('maxlength', name_length);
		this.Div.appendChild(this.Input);
		if (this.Id) {
			this.Div.appendChild(this.CreateButton('icons/done.gif', 'StopEditing(true)'));
			this.Div.appendChild(this.CreateButton('icons/cancel.gif', 'StopEditing(false)'));
		}
	}
};

Nickname.prototype.ToString = function (holder) {
	if (!this.Li) {
		this.Li = d.createElement('li');
	} else {
		this.Li.innerHTML = '';
	}
	this.Radio = CreateRadio('nickname', !me.Nickname && this.Name == me.Login || me.Nickname && this.Name == me.Nickname);
	this.Radio.RelatedItem = this;
	eval('this.Radio.onclick = function(){Select(this)}');

	this.Li.appendChild(this.Radio);
	this.Div = d.createElement('span');

	this.CreateViewControls();

	this.Li.appendChild(this.Div);
	holder.appendChild(this.Li);
};

Nickname.prototype.Gather = function (index) {
	var s = new ParamsBuilder().add('id' + index, this.Id > 0 ? this.Id : '').add('name' + index, this.Name);
	if (this.Radio.checked) s.add('selected', index);
	return s.build();
};

/* Change Nickname class */

function ChangeNickname() {};

ChangeNickname.prototype.CreateControls = function (container) {
	this.Holder = d.createElement('ul');
	this.Holder.className = 'NamesList';

	this.Holder.innerHTML = loadingIndicator;

	container.appendChild(this.Holder);

	this.Status = d.createElement('div');
	this.Status.className = 'Status';
	container.appendChild(this.Status);
	nicknames1 = this;
};

ChangeNickname.prototype.requestData = function () {
	$.get(servicesPath + 'nickname.service.php').done(NamesResponse);
};

function NamesResponse(responseText) {
	if (nicknames1.Holder) {

		nicknames.Clear();
		nicknames.Add(new Nickname(0, 0, me.Login));

		try {
			eval(responseText);
		} catch (e) {}
		for (var i = nicknames.Count(); i <= max_names; i++) {
			nicknames.Add(new Nickname(i + 1, -(i + 1), ''));
		}
		if (NewNickname != '-1') {
			me.Nickname = NewNickname;
			if (PrintRooms) {
				PrintRooms();
			}
		}
		nicknames1.Holder.innerHTML = '';
		nicknames.ToString(nicknames1.Holder);
	}
};

var activeItem;
function Select(e) {
	if (e.RelatedItem) {
		StopEditing(true);
		var item = e.RelatedItem;
		if (item.IsEmpty()) {
			Edit(e);
		}
	}
};

function Edit(e) {
	if (e.RelatedItem) {
		StopEditing(true);
		var item = e.RelatedItem;
		item.Mode = 'edit';
		item.CreateViewControls();
		item.Input.focus();
		activeItem = item;
	}
};

function Clear(e) {
	if (e.RelatedItem) {
		var item = e.RelatedItem;
		item.Name = '';
		item.CreateViewControls();
	}
};

function StopEditing(acceptChanges) {
	if (activeItem) {
		activeItem.Mode = 'show';
		if (acceptChanges) {
			activeItem.Name = activeItem.Input.value;
		}
		activeItem.CreateViewControls();
	}
};

var nicknameSaving = 0;
function SaveNicknameChanges() {
	if (nicknameSaving) {
		return;
	}
	StopEditing(true);
	nicknameSaving = 1;
	setTimeout('UnLockSaving()', 10000);
	$.post(servicesPath + 'nickname.service.php', nicknames.Gather()).done(SavingResults);
};

function UnLockSaving() {
	nicknameSaving = 0;
};

function SavingResults(req) {
	UnLockSaving();
	status = '';
	NamesResponse(req);
	if (!status) {
		SetStatus('Изменения сохранены.');
		setTimeout('co.Hide()', 2000);
	}
	ForcePing();
};

var status;
function SetStatus(text) {
	nicknames1.Status.innerHTML = text;
	status = text;
};
"use strict";

//7.10
/*
    User options UI and helper methods
*/

// Options base class

function OptionsBase() {
    this.defaultValues = [];
}

// Loading Template
OptionsBase.prototype.loadTemplate = function (tab, user_id, login) {
    // To be overriden
    this.LoadBaseTemplate(tab, user_id, login);
};

OptionsBase.prototype.LoadBaseTemplate = function (tab, user_id, login) {
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
OptionsBase.prototype.TemplateLoaded = function (req) {
    // To be overriden
    this.TemplateBaseLoaded(req);
};

OptionsBase.prototype.TemplateBaseLoaded = function (req) {
    var text = req || '';
    if (req) KeepRequestedContent(this.Template, text);
    this.Tab.RelatedDiv.innerHTML = text;
    this.Tab.initUploadFrame();
    this.request();
};

/* Checks if element type is allowed for value-operations */

OptionsBase.prototype.ValueType = function (t) {
    return ["text", "password", "hidden", "select-one", "textarea", "color", "date", "datetime"].indexOf(t) >= 0;
};

/* Gathering object properties from UI controls */

OptionsBase.prototype.GatherOne = function (name, property) {
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
};

OptionsBase.prototype.GatherFields = function (fields) {
    var properties = fields;
    this.FindRelatedControls();
    if (!fields) {
        if (!this.properties) {
            this.properties = this.fields; // In case if properties differs from elements ids
        }
        fields = this.fields;
        properties = this.properties;
    }

    var el,
        result = "";
    for (var i = 0, l = fields.length; i < l; i++) {
        result += this.GatherOne(fields[i], properties[i]);
    }
    return result;
};

OptionsBase.prototype.BaseGather = function () {
    var result = this.GatherFields();
    if (this.alt_fields) {
        result += this.GatherFields(this.alt_fields);
    }
    return result;
};

OptionsBase.prototype.Gather = function () {
    // Method to override
    return this.BaseGather();
};

/* Filling object properties with values from source array */

OptionsBase.prototype.FillBase = function (source, fields) {
    if (!fields) {
        fields = this.fields;
    } else {
        this.alt_fields = fields;
    }

    var doClear = 0;
    if (!source || !source.length) {
        source = this.defaultValues; // Do reset fields to default values
    }
    if (source.length != fields.length) {
        doClear = 1;
    }

    var el;
    for (var i = 0, l = fields.length; i < l; i++) {
        this[fields[i]] = doClear ? "" : source[i];
    }
};

OptionsBase.prototype.FillFrom = function (source, fields) {
    // Method to override
    this.FillBase(source, fields);
};

/* Binding tba controls with object's properties */

OptionsBase.prototype.FindRelatedControls = function (force) {
    if ((force || !this.inputs) && this.Tab) {
        this.inputs = IndexElementChildElements(this.Tab.RelatedDiv);
    }
};

OptionsBase.prototype.BindFields = function (fields) {
    this.FindRelatedControls();
    _.each(fields, function (field) {
        console.log(field, this[field]);
        var value = this[field];
        if (_.isUndefined(value)) return;
        this.SetTabElementValue(field, value);
    }, this);
};

OptionsBase.prototype.BaseBind = function () {
    this.BindFields(this.fields);
    if (this.alt_fields) {
        this.BindFields(this.alt_fields);
    }
};

OptionsBase.prototype.Bind = function () {
    // Method to override
    return this.BaseBind();
};

OptionsBase.prototype.AssignObjectTo = function (id, obj, name) {
    this.FindRelatedControls();
    var el = this.inputs[id];
    if (el) {
        el[name] = obj;
    }
};

OptionsBase.prototype.AssignTabTo = function (id) {
    this.AssignObjectTo(id, this.Tab, "Tab");
};

OptionsBase.prototype.AssignSelfTo = function (id) {
    this.AssignObjectTo(id, this, "obj");
};

OptionsBase.prototype.SetTabElementValue = function (element, value) {
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
};

OptionsBase.prototype.DisplayTabElement = function (element, state) {
    var el = this.inputs[element];
    if (el) {
        displayElement(el, state);
    }
};

OptionsBase.prototype.Clear = function () {
    this.FindRelatedControls();
    for (var i = 0, l = this.fields.length; i < l; i++) {
        this[this.fields[i]] = "";
    }
};

OptionsBase.prototype.Reset = function () {
    if (!this.defaultValues || this.defaultValues.length < this.fields.length) {
        return;
    }
    this.FindRelatedControls();
    for (var i = 0, l = this.fields.length; i < l; i++) {
        this.SetTabElementValue(this.fields[i], this.defaultValues[i]);
    }
};

/* Request methods */

OptionsBase.prototype.BaseRequest = function (params, callback) {
    var s = new ParamsBuilder(params);
    s.add('USER_ID', this.USER_ID);
    $.post(this.ServicePath, s.build()).done((callback || this.requestCallback).bind(this));
};

OptionsBase.prototype.request = function (params, callback) {
    /* Method to override */
    this.BaseRequest(params, callback);
};

OptionsBase.prototype.Save = function (callback) {
    var s = new ParamsBuilder(this.Gather());
    s.add('go', 'save');
    this.request(s.build(), callback);
};

OptionsBase.prototype.Delete = function (callback) {
    var s = new ParamsBuilder(this.Gather());
    s.add('go', 'delete');
    this.request(s.build(), callback);
};

/* Common callback */

OptionsBase.prototype.requestBaseCallback = function (req) {
    this.data = [];
    this.Total = 0;

    this.Tab.Alerts.Clear();
    eval(req);
};

// Reaction method to be overriden
OptionsBase.prototype.React = function (value) {
    alert("Reaction handler is unset.");
};

OptionsBase.prototype.GroupAssign = function (method, items) {
    if (this[method]) {
        for (var i = 0, l = items.length; i < l; i++) {
            this[method](items[i]);
        }
    }
};

OptionsBase.prototype.GroupSelfAssign = function (items) {
    this.GroupAssign("AssignSelfTo", items);
};

OptionsBase.prototype.GroupTabAssign = function (items) {
    this.GroupAssign("AssignTabTo", items);
};

OptionsBase.prototype.UpdateToPrintableDate = function (field) {
    this.SetTabElementValue(field, ParseDate(this[field]).ToPrintableString(1));
};

/* Helper methods */

var optionsWindow;
function ShowOptions() {
    var tab = tabs.tabsCollection.Get('Меню');
    if (!tab) {
        var menuTab = new Tab('menu', 'Меню');
        tabs.Add(menuTab);
        menuTab.switchTo();
        tabs.Print();
        $(menuTab.RelatedDiv).load('/options/menu.php', function () {
            initLayout(pages.menu, $('#MessagesContainer')[0]);
            $(menuTab.RelatedDiv).trigger('load');
        });
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
        req = $.get('/options/' + obj.Template + '.php').done(obj.TemplateLoaded.bind(obj));
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
"use strict";

//6.8
/*
	User profile data & helper methods
*/

function Profile() {
	this.fields = new Array("LOGIN", "EMAIL", "NAME", "GENDER", "BIRTHDAY", "CITY", "ICQ", "URL", "PHOTO", "AVATAR", "ABOUT", "REGISTERED", "LAST_VISIT");
	this.ServicePath = servicesPath + "profile.service.php";
	this.Template = "userdata";
	this.ClassName = "Profile"; // Optimize?
};

Profile.prototype = new OptionsBase();

Profile.prototype.Gather = function () {
	var result = this.BaseGather();
	result += this.GatherOne("PASSWORD");
	result += this.GatherOne("PASSWORD_CONFIRM");
	result += this.GatherOne("BANNED");
	return result;
};

Profile.prototype.Bind = function () {
	this.BaseBind();

	/* Bind images */
	displayElement(this.inputs["liDeletePhoto"], this.PHOTO);
	displayElement(this.inputs["liDeleteAvatar"], this.AVATAR);

	this.BindImage(this.Photo1);
	this.BindImage(this.Avatar1);

	/* Ban Status */
	this.SetTabElementValue("BANNED", this.BANNED_BY > 0);
	this.DisplayTabElement("BanDetails", !this.ADMIN && this.BANNED_BY > 0);

	var ban_status = "";
	if (this.ADMIN) {
		ban_status += "Пользователь забанен администратором&nbsp;<b>" + this.ADMIN + "</b>";
		ban_status += "&nbsp;" + (this.BANNED_TILL ? "до " + this.BANNED_TILL : "бессрочно");
		if (this.BAN_REASON) {
			ban_status += "&nbsp;по причине&nbsp;&laquo;" + this.BAN_REASON + "&raquo;";
		}
	}
	this.SetTabElementValue("BanStatus", ban_status);

	// Correct dates
	this.UpdateToPrintableDate("REGISTERED");
	this.UpdateToPrintableDate("LAST_VISIT");
};

Profile.prototype.BindImage = function (img) {
	if (this[img.Field]) {
		this.ReloadImage(img);
	} else {
		img.ImageObject = "";
		this.PrintLoadedImage(img);
	}
};

Profile.prototype.CheckPhoto = function (img) {
	if (!img.HasImage()) {
		img.ImageObject = d.createElement("img");
		img.ImageObject.Profile = this;
		img.ImageObject.Img = img;
		img.ImageObject.onload = function () {
			ImageLoaded(this);
		};
	}
};

Profile.prototype.ReloadImage = function (img) {
	if (!this.Tab.Alerts.HasErrors) {
		this.SetTabElementValue(img.Container, loadingIndicator);
		this.CheckPhoto(img);
		img.ImageObject.src = img.Path + this[img.Field] + "?" + Math.random(100);
		if (this.inputs[img.UploadForm]) {
			this.inputs[img.UploadForm].reset();
		}
	}
};

Profile.prototype.PrintLoadedImage = function (img) {
	var p = this.inputs[img.Container],
	    result = "не загружено";
	if (p) {
		if (img.ImageObject) {
			var dim = "width='" + img.MaxWidth + "'";
			if (img.ImageObject.width < img.MaxWidth) {
				dim = "width='" + img.ImageObject.width + "' height='" + img.ImageObject.height + "'";
			}
			result = "<img class='Photo' src='" + img.ImageObject.src + "' " + dim + ">";
		}
		p.innerHTML = result;
	}
};

Profile.prototype.requestCallback = function (req) {
	this.requestBaseCallback(req);
	this.Bind();
	this.Initialized = false;
};

function ImageLoaded(e) {
	e.Profile.PrintLoadedImage(e.Img);
};

// Loading template
Profile.prototype.TemplateLoaded = function (req) {
	this.TemplateBaseLoaded(req);

	/* Init images (photo & avatar) */
	this.Tab.initUploadFrame("AvatarUploadFrame");

	/* Assign Tab to links */
	this.Photo1 = new Img("PHOTO", "Photo", "uploadForm", this.Tab.UploadFrame, userPhotosPath, 300);
	this.Avatar1 = new Img("AVATAR", "Avatar", "avatarUploadForm", this.Tab.AvatarUploadFrame, avatarsPath, 120);

	this.GroupTabAssign(["linkDeletePhoto", "linkDeleteAvatar", "BANNED", "PASSWORD"]);
	this.GroupSelfAssign(["linkRefresh", "linkLockIP"]);

	/* Viewing my profile hide Status & Ban sections */
	if (this.USER_ID == me.Id) {
		this.DisplayTabElement("NotForMe", false);
	}

	/* Date pickers */
	new DatePicker(this.inputs["BIRTHDAY"]);
	new DatePicker(this.inputs["BANNED_TILL"], 1);

	/* OpenIDs associated with this user */
	var oid = new Spoiler(1, "OpenID", 0, 0, function (tab) {
		new OpenIds().loadTemplate(tab, this.USER_ID);
	});
	oid.USER_ID = this.USER_ID;
	oid.ToString(this.inputs["OpenIds"]);

	/* Admin comments spoiler */
	if (me.IsAdmin()) {
		var acs = new Spoiler(2, "Комментарии администраторов	&	логи", 0, 0, function (tab) {
			new AdminComments().loadTemplate(tab, this.USER_ID);
		});
		acs.USER_ID = this.USER_ID;
		acs.ToString(this.inputs["AdminComments"]);
	}

	/* Submit button */
	this.Tab.AddSubmitButton("SaveProfile(this)", "", this);
};

/* Save profile */

function UploadImage(profile, img) {
	var form = profile.inputs[img.UploadForm];
	if (form) {
		var p = form["PHOTO1"];
		if (p && p.value) {
			form["tab_id"].value = profile.Tab.Id;
			form["USER_ID"].value = profile.USER_ID;
			form.target = img.Frame.name;
			form.submit();
		}
	}
};

function SaveProfile(a) {
	if (a.obj) {
		a.obj.Tab.Alerts.Clear();

		/* Saving Photo & Avatar */
		UploadImage(a.obj, a.obj.Photo1);
		UploadImage(a.obj, a.obj.Avatar1);

		/* Saving profile */
		a.obj.Save(ProfileSaved);
	}
};

function ProfileSaved(responseText) {
	if (responseText) {
		this.requestCallback(responseText);

		this.FindRelatedControls(true);
		DoClick(this.inputs["RefreshAdminComments"]);
	};
};

/* Links actions */

function DeletePhotoConfirmed(a, image) {
	if (a.Tab) {
		a.Tab.Alerts.Clear();
		a.Tab.Profile.request('go=delete_' + image);
	}
};

function ShowBanDetails(cb) {
	if (cb.Tab) {
		cb.Tab.Profile.DisplayTabElement("BanDetails", !cb.Tab.Profile.ADMIN && cb.checked);
	}
};

function restoreInput(el, relatedBlockId) {
	var tab = el.Tab;
	if (!tab) {
		return;
	}
	if (el.value != el.previousValue) {
		tab.Profile.DisplayTabElement(relatedBlockId, el.value);
	}
	if (!el.value) {
		el.value = empty_pass;
	}
};

/* Profile Image helper class */

function Img(field, container, form, frame, path, max_width) {
	this.Field = field;
	this.Container = container;
	this.UploadForm = form;
	this.Frame = frame;
	this.Path = path;
	this.MaxWidth = max_width;
};

Img.prototype.HasImage = function () {
	return this.ImageObject;
};

/* Confirms */

function DeletePhoto(a) {
	co.Show(function () {
		DeletePhotoConfirmed(a, "photo");
	}, "Удалить фотографию?", "Фотография пользователя будет удалена из профиля.<br>Вы уверены?");
};

function DeleteAvatar(a) {
	co.Show(function () {
		DeletePhotoConfirmed(a, "avatar");
	}, "Удалить аватар?", "Автар будет удален из профиля.<br>Вы уверены?");
};
"use strict";

//6.0
/*
    Contains all global script settings, constants, variables and common methods
*/

var debug = 0;

var d = document;
var w = window;
var voidLink = "javascript:void(0);";
var voidHref = "href=\"" + voidLink + "\"";

var imagesPath = "/img/";
var servicesPath = "/services/";
var userPhotosPath = "/img/photos/";
var avatarsPath = "/img/avatars/";
var skinsPreviewPath = "/img/journals/";
var openIdPath = "/img/openid/";

var adminRights = 75;
var keeperRights = 20;
var topicRights = 10;

var loadingIndicator = "<div class='LoadingIndicator'></div>";

var severityCss = ["Warning", "Error"];

var replaceTagsExpr = new RegExp("\<[\/a-z][^\>]*\>", "gim");
"use strict";

//1.6
/*
	Contains misc string-related functions.
*/

var chars = "абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";
var ascii = new Array();

for (var i = 0; i < chars.length; i++) {
    var ch = chars.charAt(i);
    ascii[ch] = i + 1;
};

function CheckSum(source) {
    source = "" + source;
    var sum = 0;
    if (source != "undefined" && source != "") {
        source = " " + source;

        var i;
        if (source && source.length) {
            var code;
            for (i = 1; i < source.length; i++) {
                code = ascii[source.charAt(i)];
                if (!code) {
                    code = source.charCodeAt(i);
                    if (code > 255) {
                        code = 1;
                    }
                }
                sum += code;
                //DebugLine(source.charAt(i) + " = " + code + " -> " + sum);
            }
        }
    }
    //DebugLine(source + " = " + sum + "<hr>");
    return sum;
};

function ParamsBuilder(prefix) {
    this.prefix = _.isUndefined(prefix) ? '' : prefix + '&';
    this.params = {};
    return this;
};
ParamsBuilder.prototype.add = function (name, value) {
    this.params[name] = value;
    return this;
};
ParamsBuilder.prototype.build = function () {
    return this.prefix + jQuery.param(this.params) + '&';
};

function TwoDigits(d) {
    if (d > 9) {
        return d;
    }
    return "0" + d;
};

var tagsRegex = new RegExp("\<[\/]{0,1}[a-z]+[^\>]*\>", "ig");
function StripTags(text) {
    return text.replace(tagsRegex, "");
};

function HtmlQuotes(text) {
    return text.replace(/"/g, "&quot;");
};

function StrongHtmlQuotes(text) {
    text = text.replace(/&/g, "&amp;");
    text = HtmlQuotes(text);
    text = text.replace(/</g, "&lt;");
    text = text.replace(/>/g, "&gt;");
    text = text.replace(/"/g, "&quot;");
    return text;
};

function Quotes(text) {
    //	return text.replace(/\"/g, "\\\"").replace(/'/g, "\\'");
    return text.replace(/'/g, "\\'").replace(/"/g, "&amp;quot;");
};

function Slash(text) {
    return text.replace(/(['"<>])/g, "\\$1");
};
"use strict";

//4.1
/*
	SuperAdmin only functionality.
	Will be loaded only if server rights checking is > adminRights.
*/

/* Usermanager admins' section */

function umAdditionalExtraButtons(el, id, login, obj) {
    el.appendChild(makeUserMenuLink(makeButtonLink("showSettings(" + id + ",\"" + login + "\")", "Настройки", obj, "")));
    el.appendChild(makeUserMenuLink(makeButtonLink("showBlog(" + id + ",\"" + login + "\")", "Журнал", obj, "")));
};

function showBlog(id, name) {
    customTab(id, name, JournalMessages, "j", "Журнал");
};

function showSettings(id, name) {
    customTab(id, name, Settings, "s", "Настройки");
};

/* Admin Options */

var spoilerNames = ["Кодекс администратора", "Новости чата", "Запреты", "Логи системы", "Лог сообщений чата", "Персональные статусы", "Комнаты", "Боты", "Задачи по расписанию"];

var spoilerInits = [function (tab) {
    new LawCode().loadTemplate(tab);
}, function (tab) {
    new News().loadTemplate(tab);
}, function (tab) {
    new BannedAddresses().loadTemplate(tab);
}, function (tab) {
    new SystemLog().loadTemplate(tab);
}, function (tab) {
    new MessagesLog().loadTemplate(tab);
}, function (tab) {
    new Statuses().loadTemplate(tab);
}, function (tab) {
    new Rooms().loadTemplate(tab);
}, function (tab) {
    new Bots().loadTemplate(tab);
}, function (tab) {
    new ScheduledTasks().loadTemplate(tab);
}];
'use strict';

//4.1
/*
	Tab class. Entity of Tabs one.
*/

/* Base tab class */
function TabBase() {};

TabBase.prototype.initUploadFrame = function (property) {
    if (!property) {
        property = 'UploadFrame';
    }
    if (!this[property]) {
        this[property] = createElement('iframe', 'UploadFrame' + _.random(10, 99));
        this[property].className = 'UploadFrame';
        if (this.RelatedDiv) {
            this.RelatedDiv.appendChild(this[property]);
        }
    }
};

/* obj - object to be assigned as a.obj (Tab by default) */
TabBase.prototype.AddSubmitButton = function (method, holder, obj) {
    var m1 = d.createElement("div");
    m1.className = "ConfirmButtons";
    this.SubmitButton = MakeButton(method, "ok_button.gif", obj || this, "", "Сохранить изменения");
    m1.appendChild(this.SubmitButton);
    this[holder || "RelatedDiv"].appendChild(m1);
};

/* Tab object reaction by outside call */
TabBase.prototype.React = function (value) {
    if (this.Reactor) {
        this.Reactor.React(value);
    }
};

/* Sets additional className to RelatedDiv */
TabBase.prototype.SetAdditionalClass = function (className) {
    this.RelatedDiv.className = "TabContainer" + (className ? " " + className : "");
};

/* Tab class */
Tab.prototype = new TabBase();

function Tab(id, title, is_locked, is_private, on_select) {
    this.Id = id;
    this.Title = title;
    this.IsLocked = is_locked;
    this.IsPrivate = is_private;
    this.onSelect = on_select;

    this.UnreadMessages = 0;
    this.lastMessageId = -1;
    this.Alt = '';

    this.recepients = new Collection();
    if (this.IsPrivate) {
        this.recepients.Add(new Recepient(id, title, 1));
    }
};

Tab.prototype.ToString = function (index) {
    var isSelected = _.get(this, 'collection.current.Id') === this.Id;
    this.DisplayDiv(isSelected);

    var li = document.createElement('li'),
        title;
    li.className = (isSelected ? 'Selected ' : '') + (this.UnreadMessages ? 'HasUnread' : '');
    li.alt = this.Alt;
    li.title = this.Alt;

    title = document.createElement('button');
    if (!isSelected) {
        title.onclick = this.switchTo.bind(this);
        title.onfocus = this.switchTo.bind(this);
    };
    title.innerHTML = this.Title + (this.UnreadMessages ? ' (' + this.UnreadMessages + ')' : '');

    li.appendChild(title);
    if (!this.IsLocked) {
        var close = document.createElement('button');
        close.className = 'CloseSign';
        close.onclick = this.close.bind(this);
        close.innerHTML = '&times;';
        li.appendChild(close);
    };
    return li;
};

Tab.prototype.DisplayDiv = function (state) {
    displayElement(this.RelatedDiv, state);
    displayElement(this.TopicDiv, state);
};

Tab.prototype.Clear = function () {
    this.TopicDiv.innerHTML = '';
    this.RelatedDiv.innerHTML = '';
    this.lastMessageId = -1;
};

Tab.prototype.switchTo = function () {
    if (this.collection) this.collection.switchTo(this.Id);
};

Tab.prototype.close = function () {
    if (this.collection) this.collection.Delete(this.Id);
};

/*
	Tabs collection class.
*/

function Tabs(tabsContainer, contentContainer) {
    this.TabsContainer = tabsContainer;
    this.ContentContainer = contentContainer;
    this.tabsCollection = new Collection();
    this.current = null;
    this.history = [];

    this.tabsList = document.createElement("ul");
    this.tabsList.className = "Tabs";
    this.TabsContainer.appendChild(this.tabsList);
};

Tabs.prototype.Print = function () {
    var tabsContainer = this.tabsList;
    tabsContainer.innerHTML = '';
    return _.each(this.tabsCollection.Base, function (tab) {
        tabsContainer.appendChild(tab.ToString());
    });
};

Tabs.prototype.Add = function (tab, existing_container) {
    var topic = document.createElement("div");
    topic.className = "TabTopic";
    this.ContentContainer.appendChild(topic);
    tab.TopicDiv = topic;
    tab.collection = this;
    this.history.push(tab.Id);

    if (!existing_container) {
        existing_container = document.createElement("div");
        existing_container.className = "TabContainer";
        this.ContentContainer.appendChild(existing_container);
    }
    tab.RelatedDiv = existing_container;

    this.tabsCollection.Add(tab);
    tab.DisplayDiv(false);
};

Tabs.prototype.Delete = function (id) {
    var tab = this.tabsCollection.Get(id);
    if (tab) {
        this.ContentContainer.removeChild(tab.TopicDiv);
        this.ContentContainer.removeChild(tab.RelatedDiv);
        this.tabsCollection.Delete(id);

        _.pull(this.history, id);
        if (this.current.Id == id) this.switchTo(this.history.pop());
        this.Print();
    }
};

Tabs.prototype.switchTo = function (id) {
    var tab = this.tabsCollection.Get(id);
    if (tab) {
        if (_.last(this.history) === id) this.history.push(id);
        this.current = tab;
        tab.UnreadMessages = 0;

        recepients = tab.recepients;
        _.result(window, 'ShowRecepients');
        this.Print();

        if (tab.onSelect) {
            tab.RelatedDiv.innerHTML = loadingIndicator;
            tab.onSelect(tab);
        };

        _.result(window, 'onResize');
    }
};

/* Service functions */

function CloseTab(id) {
    var tab = tabs.tabsCollection.Get(id);
    if (tab) {
        tabs.Delete(id);
        SwitchToTab(MainTab.Id);
        tabs.Print();
    }
}
"use strict";

//3.1
/*
    Validation of controls against rules given.
*/

function ValidatorsCollection() {
    this.Clear();
};

ValidatorsCollection.prototype = new Collection();

ValidatorsCollection.prototype.Init = function (summary_control, summary_text) {
    this.Summary = $(getElement(summary_control))[0];
    this.SummaryText = summary_text ? "<h2>" + summary_text + "</h2>" : "";
    this.InitSummary();

    for (var id in this.Base) {
        if (id && this.Base[id].Init) {
            this.Base[id].Init();
        }
    }
};

ValidatorsCollection.prototype.InitSummary = function () {
    if (this.Summary) {
        this.Summary.innerHTML = this.SummaryText;
        doHide(this.Summary);
    }
};

ValidatorsCollection.prototype.ShowSummary = function (errors) {
    if (this.Summary && errors && errors.length) {
        this.Summary.innerHTML = this.SummaryText + "<li> " + errors.join("<li> ");
        doShow(this.Summary);
    }
};

ValidatorsCollection.prototype.AreValid = function () {
    this.InitSummary();

    var result = true;
    for (var id in this.Base) {
        if (id && this.Base[id].Validate) {
            if (!this.Base[id].Validate(this.Summary)) {
                result = false;
            }
        }
    }
    return result;
};

var PageValidators = new ValidatorsCollection();

function ValueHasChanged() {
    return PageValidators.AreValid();
};

/* --------------- Single Validator --------------- */

function Validator(control, rule, message, summarize, on_the_fly) {
    this.Control = $(getElement(control))[0];
    this.Rule = rule;
    this.Message = message;
    this.ShowInSummary = summarize;
    this.OnTheFly = on_the_fly;

    this.Id = Random(1000, 1);
    this.Enabled = true;
};

Validator.prototype.Init = function () {
    if (this.OnTheFly) {
        this.Control.onchange = ValueHasChanged;
    }

    this.ErrorContainer = d.createElement("div");
    if (!this.ShowInSummary) {
        this.Display(false);
        this.ErrorContainer.innerHTML = this.Message;

        insertAfter(this.ErrorContainer, this.Control);
    }
};

Validator.prototype.Validate = function (summary_control) {
    if (this.Control && this.Rule.Check(this.Control.value, this.Control)) {
        this.Display(false);
        return true;
    }
    this.Control.focus();
    this.Display(true, summary_control);
    return false;
};

Validator.prototype.Display = function (state, summary_control) {
    if (summary_control && this.ShowInSummary) {
        summary_control.innerHTML += "<li>" + this.Message;
        doShow(summary_control);
    } else {
        this.ErrorContainer.className = "Validator" + (state ? "" : " Hidden");
    }
};

/* -------------------- Validation Rules -------------------- */
// Required Field

function RequiredField() {};

RequiredField.prototype.Check = function (value) {
    return value.length > 0;
};

// Field Length

function LengthRange(min_length, max_length) {
    this.MinLength = min_length;
    this.MaxLength = max_length;
};

LengthRange.prototype.Check = function (value) {
    var l = value.length;
    return l >= this.MinLength && l <= this.MaxLength;
};

// Equal To

function EqualTo(control) {
    this.Control = control;
};

EqualTo.prototype.Check = function (value) {
    return this.Control && this.Control.value == value;
};

// Match the pattern
var emailPattern = new RegExp("^[0-9a-zA-Z\!\#\$\'\*\+\-\/\=\?\^_\.\`\{\|\}\~]+\@[0-9a-zA-Z\!\#\$\'\*\+\-\/\=\?\^_\`\{\|\}\~]{2,50}([\.][0-9a-zA-Z\!\#\$\'\*\+\-\/\=\?\^_\`\{\|\}\~]{2,50})+$");

function MatchPattern(pattern) {
    this.Pattern = pattern;
};

MatchPattern.prototype.Check = function (value) {
    return value.match(this.Pattern);
};

// Is Checked
function IsChecked() {};

IsChecked.prototype.Check = function (x, control) {
    return control.checked;
};
"use strict";

//2.0
/*
	Opens pop-up wakeup windows with messages.
*/

var wakeups = new Collection();
var WakeupsHolder;
var MaxShownWakeups = 10;
var ReceivedWakeups = 0;

function Wakeup(id, sender, reset) {
	if (!me) {
		return;
	}
	if (me.Settings.ReceiveWakeups) {
		ShowWakeup(id);
	} else {
		wakeups.Add(new WakeupMessage(id, sender));
	}
};

function ShowWakeup(id, remove) {
	var wakeupWindow = window.open("wakeup.php?id=" + id, "wakeup" + id, "width=500,height=300,toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=1");
	if (remove) {
		wakeups.Delete(id);
		PrintWakeups();
	}
};

function PrintWakeups() {
	if (!WakeupsHolder) {
		WakeupsHolder = $("#Wakeups");
	}
	if (WakeupsHolder) {
		ReceivedWakeups = wakeups.Count();
		if (ReceivedWakeups > 0) {
			WakeupsHolder.innerHTML = "<h3>Вейкапы	<span class='Count'>(" + ReceivedWakeups + ")</span>:</h3>";
			wakeups.ToString(WakeupsHolder);
			displayElement(WakeupsHolder, true);
		} else {
			displayElement(WakeupsHolder, false);
		}
	}
};

function WakeupMessage(id, sender) {
	this.Id = id;
	this.Sender = sender;
};

WakeupMessage.prototype.ToString = function (holder, i) {
	if (i == MaxShownWakeups) {
		holder.appendChild(d.createTextNode(",	и ещё " + (ReceivedWakeups - MaxShownWakeups)));
		return;
	} else if (i > MaxShownWakeups) {
		return;
	}
	var a = d.createElement("a");
	a.innerHTML = this.Sender;
	a.href = voidLink;
	a.wId = this.Id;
	a.onclick = function () {
		ShowWakeup(this.wId, 1);
	};
	if (i) {
		holder.appendChild(d.createTextNode(",	"));
	}
	holder.appendChild(a);
};
"use strict";

var layoutConfigs = [function () {
	this.frames[4].Replace(10, 10, this.winSize.width - 20, 28);
	this.frames[0].Replace(10, this.frames[4].y + this.frames[4].height + 10, 150, this.winSize.height - 60);
	this.frames[1].Replace(10 + this.frames[0].x + this.frames[0].width, this.frames[0].y, this.winSize.width - 20 - this.frames[0].x - this.frames[0].width, -1);

	this.frames[2].Replace(this.frames[1].x, this.frames[1].y + this.frames[1].height + 10, this.frames[1].width + 2, this.winSize.height - 80 - this.frames[1].height);

	this.frames[3].Replace(-1, -1, -1, this.frames[2].height - 40);
}, function () {
	this.frames[4].Replace(10, 10, this.winSize.width - 20, 26);
	this.frames[0].Replace(this.winSize.width - 160, this.frames[4].y + this.frames[4].height + 10, 150, this.winSize.height - 60);
	this.frames[1].Replace(10, this.frames[0].y, this.winSize.width - 30 - this.frames[0].width, -1);

	this.frames[2].Replace(this.frames[1].x, this.frames[1].y + this.frames[1].height + 10, this.frames[1].width + 2, this.winSize.height - 80 - this.frames[1].height);

	this.frames[3].Replace(-1, -1, -1, this.frames[2].height - 40);
}, function () {
	this.frames[4].Replace(10, 10, this.winSize.width - 20, 28);
	this.frames[0].Replace(10, this.frames[4].y + this.frames[4].height + 10, 150, this.winSize.height - this.frames[4].height - 30);

	this.frames[2].Replace(10 + this.frames[0].x + this.frames[0].width, this.frames[0].y, this.winSize.width - this.frames[0].width - 30, this.winSize.height - 80 - this.frames[1].height);

	this.frames[3].Replace(-1, -1, -1, this.frames[2].height - 40);

	this.frames[1].Replace(this.frames[2].x, this.winSize.height - 110, this.winSize.width - 20 - this.frames[0].x - this.frames[0].width, 100);
}, function () {
	this.frames[4].Replace(10, 10, this.winSize.width - 20, 28);

	this.frames[2].Replace(10, this.frames[4].y + this.frames[4].height + 10, this.winSize.width - this.frames[0].width - 28, this.winSize.height - 70 - this.frames[1].height);

	this.frames[3].Replace(-1, -1, -1, this.frames[2].height - 40);

	this.frames[0].Replace(this.winSize.width - 160, this.frames[2].y, 150, this.winSize.height - this.frames[4].height - 30);
	this.frames[1].Replace(this.frames[2].x, this.winSize.height - 110, this.winSize.width - this.frames[0].width - 30, 100);
}];

var textForm = $("#Message")[0];
function _s(text, erase) {
	if (textForm) {
		textForm.value = (erase ? "" : textForm.value) + text;
		textForm.focus();
	}
};

function _(text, erase) {
	_s(text + ", ", erase);
};

function __(el) {
	if (el.hasChildNodes && el.childNodes[0].hasChildNodes && el.childNodes[0].childNodes[0]) {
		el = el.childNodes[0];
	}
	_(el.innerText || el.textContent);
};
"use strict";

function Feedback() {
    document.location = "mai" + "lto:" + "info" + "@" + "bezumnoe." + "ru";
};

var infoPopUp;
function info(id) {
    infoPopUp = open("/user/" + id + ".html", "info", "width=550,height=600,toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=1");
    return false;
}

/* Letterizing */
letterize = ['.Register h4', '.Forum h4', '.Gallery h4', '.Blogs h4', 'h1'];

$(document).ready(function () {
    $(letterize.join(',')).lettering();

    $("#auth_form").dialog({
        title: 'Авторизация в чате',
        autoOpen: false,
        height: 250,
        width: 420,
        modal: true,
        buttons: {
            "Авторизоваться": function _() {
                $("form#auth").submit();
                $(this).dialog("close");
            },
            "Отмена": function _() {
                $(this).dialog("close");
            }
        }
    });
    $(".submitter").keypress(function (e) {
        if (e.which == 13) {
            $("form#auth").submit();
        }
    });
    if (window.startup) {
        startup();
    }
});
"use strict";

var container = new MyFrame($("#InfoContainer")[0]);
var content = new MyFrame($("#InfoContent")[0]);
var winSize = new MyFrame(window);

function AdjustDivs(e) {
    if (!e) {
        var e = window.event;
    }

    winSize.GetPosAndSize();
    container.Replace(10, 10, winSize.width - 20, winSize.height - 20);
    content.Replace(-1, -1, -1, container.height - 40);
}

AdjustDivs();

window.onresize = AdjustDivs;
if (window.addEventListener) {
    window.addEventListener("resize", AdjustDivs, true);
};

/* Tabs */
var tabs;
$(document).ready(function () {
    tabs = new Tabs($("#InfoContainer")[0], $("#InfoContent")[0]);
    CurrentTab = new Tab(1, "Инфо", 1);
    tabs.Add(CurrentTab, $("#Info")[0]);
    //tabs.Add(new Tab(2, "Блабла1", 1));
    //tabs.Add(new Tab(3, "Блабла2", 1));
    tabs.Print();
});
'use strict';

var frames,
    configIndex = 0,
    co = new Confirm(),
    MainTab,
    pages = {
    inside: {
        containers: [['#Users', 0, 100], ['#MessageForm', 500], ['#Messages', 500, 100], '#MessagesContainer', ['#Status', 660], '#AlertContainer'],
        onResize: function onResize() {
            this.frames[5].Replace(-1, -1, this.winSize.width, this.winSize.height);
            layoutConfigs[configIndex].call(this);
            $('body').removeClass().addClass('Layout' + configIndex);
        },
        onLoad: function onLoad() {
            this.users = new Collection();
            this.rooms = new Collection();
            this.recepients = new Collection();
            this.co = new Confirm();
            this.CurrentRoomId = 1;
            this.me = null;

            this.tabs = new Tabs($("#Messages")[0], $("#MessagesContainer")[0]);
            chatTab = new Tab(1, "Чат", true);
            this.tabs.Add(chatTab);
            chatTab.switchTo();
            this.tabs.main = chatTab;

            $('#AlertContainer').hide();
            this.co.Init("AlertContainer", "AlertBlock");

            var chat = new Chat(this.tabs);
            chat.initMenu($("#MenuContainer")[0]);
        }
    },
    info: {
        containers: ['#InfoContainer', '#InfoContent'],
        onResize: function onResize() {
            this.frames[0].Replace(10, 10, this.winSize.width - 20, this.winSize.height - 20);
            this.frames[1].Replace(-1, -1, -1, this.frames[0].height - 40);
        },
        onLoad: function onLoad() {
            this.tabs = new Tabs($('#InfoContainer')[0], $('#InfoContent')[0]);
            this.tabs.Add(new Tab(1, 'Инфо', 1), $('#Info')[0]);
            this.tabs.Print();
        }
    },
    menu: {
        containers: [['#OptionsContainer'], '#OptionsContent', '#AlertContainer'],
        onResize: function onResize() {
            this.frames[0].Replace(10, 10, this.winSize.width - 20, this.winSize.height - 16);
            this.frames[1].Replace(-1, -1, -1, this.frames[0].height - 30);
            this.frames[2].Replace(-1, -1, this.winSize.width, this.winSize.height);
        },
        onLoad: function onLoad() {
            var me = window.me;
            if (me) {
                this.UploadFrame = $('#uploadFrame')[0];

                this.tabs = new Tabs($('#OptionsContainer')[0], $('#OptionsContent')[0]);
                var profileTab = new Tab(1, 'Личные данные', true);
                this.tabs.Add(profileTab);

                this.tabs.Add(new Tab(2, 'Настройки', true, '', function (tab) {
                    new Settings().loadTemplate(tab, me.Id);
                }));

                this.tabs.Add(new Tab(3, 'Журнал', true, '', function (tab) {
                    new JournalsManager().loadTemplate(tab, me.Id);
                }));

                this.tabs.Add(new Tab(5, 'Сообщения', true, '', function (tab) {
                    new Wakeups().loadTemplate(tab, me.Id);
                }));

                if (me.Rights >= this.adminRights) {
                    this.tabs.Add(new Tab(6, 'Пользователи', true, '', function (tab) {
                        new Userman().loadTemplate(tab, me.Id);
                    }));

                    this.MainTab = new Tab(7, 'Администрирование', 1, '', function (tab) {
                        new AdminOptions().loadTemplate(tab, me.Id);
                    });
                    this.tabs.Add(MainTab);
                } else {
                    profileTab.switchTo();
                }
                this.tabs.Print();
                new Profile().loadTemplate(profileTab, me.Id);
            }
        }
    },
    wakeup: {
        containers: [['#WakeupContainer', 400], ['#WakeupReply', 400]],
        onResize: function onResize() {
            this.frames[0].Replace(10, 40, this.winSize.width - 20, this.winSize.height - 50 - offset);
            this.frames[1].Replace(10, this.winSize.height - replyFormHeight, this.winSize.width - 20, replyFormHeight - 10);
        }
    }
};

function initLayout(layout, container) {
    var context = {
        winSize: new MyFrame(container || window),
        frames: layout.containers.map(function (params) {
            params = _.flatten([params, null, null]);
            return new MyFrame($(params[0])[0], params[1], params[2]);
        })
    },
        onResize = function onResize() {
        context.winSize.GetPosAndSize();
        layout.onResize.call(context);
    };

    $(window).on('resize', onResize);
    onResize();
    if (layout.onLoad) {
        if (!container) {
            $(window).on('load', layout.onLoad.bind(window));
        } else {
            layout.onLoad.call(container);
        }
    };
};
"use strict";

// Scripts related to menu only

var container = new MyFrame($("#OptionsContainer")[0], 580, 400);
var content = new MyFrame($("#OptionsContent")[0]);
var alerts = new MyFrame($("#AlertContainer")[0]);
var winSize = new MyFrame(window);

function AdjustDivs(e) {
    if (!e) {
        var e = window.event;
    }

    winSize.GetPosAndSize();
    container.Replace(10, 10, winSize.width - 20, winSize.height - 20);
    content.Replace(-1, -1, -1, container.height - 40);

    alerts.Replace(-1, -1, winSize.width, winSize.height);
}

AdjustDivs();

window.onresize = AdjustDivs;
if (window.addEventListener) {
    window.addEventListener("resize", AdjustDivs, true);
};

/* Initial data request */

if (opener) {
    var me = opener.me;
    if (me) {
        var UploadFrame = $("#uploadFrame")[0];

        /* Tabs */
        var tabs = new Tabs($("#OptionsContainer")[0], $("#OptionsContent")[0]);
        ProfileTab = new Tab(1, "Личные данные", 1, "");
        CurrentTab = ProfileTab;
        tabs.Add(CurrentTab);

        tabs.Add(new Tab(2, "Настройки", 1, "", function (tab) {
            new Settings().LoadTemplate(tab, me.Id);
        }));

        var journalTab = new Tab(3, "Журнал", 1, "", function (tab) {
            new JournalsManager().LoadTemplate(tab, me.Id);
        });
        tabs.Add(journalTab);

        WakeupsTab = new Tab(5, "Сообщения", 1, "", function (tab) {
            new Wakeups().LoadTemplate(tab, me.Id);
        });
        tabs.Add(WakeupsTab);

        if (me.Rights >= adminRights) {
            tabs.Add(new Tab(6, "Пользователи", 1, "", function (tab) {
                new Userman().LoadTemplate(tab, me.Id);
            }));

            MainTab = new Tab(7, "Администрирование", 1, "", function (tab) {
                new AdminOptions().LoadTemplate(tab, me.Id);
            });
            tabs.Add(MainTab);
        } else {
            MainTab = ProfileTab;
        }

        tabs.Print();

        new Profile().LoadTemplate(ProfileTab, me.Id); // Loading profile to 1st tab
    } else {
            alert("Меню работает только пока вы находитесь в чате.");
        }
}

var co = new Confirm();
"use strict";

function Chat(tabs) {
    var Topic = $("#TopicContainer")[0];
    var Status = $("#Status")[0];
    var PongImg = $("#pong")[0];
    var pongImage = new Image();
    pongImage.src = imagesPath + 'pong.gif';

    var topicMessage = '';
    var lastMessageId = -1;
    var showRooms = 0;
    var newRoomId = 0;
    var pingTimer;

    /* Pong: Messages, Rooms, Users, Topic */

    this.pong = function (responseText) {
        busy = 0;
        requestSent = 0;
        clearTimeout(tiomeoutTimer);
        PongImg.src = pongImage.src;

        wakeups.Clear();

        try {
            eval(responseText);
            if (showRooms) {
                showRooms = 0;
                PrintRooms();
            }
        } catch (e) {
            DebugLine(e.description);
            DebugLine(responseText);
        }

        PrintWakeups();

        if (me && me.Settings.Frameset != configIndex) {
            configIndex = me.Settings.Frameset;
            _.result(window, 'onResize');
        }

        var currentName = $('#CurrentName');
        if (currentName) {
            var oldName = currentName.text();
            if (me.Nickname && oldName != me.Nickname || !me.Nickname && currentName.innerHTML != me.Nickname) {
                currentName.text(me.Nickname ? me.Nickname : me.Login);
            }
        }
    };

    var tiomeoutTimer;
    function PingTimeout() {
        busy = 0;
    };

    var busy = 0;
    var requestSent = 0;
    this.ping = function (doCheck) {
        CompareSessions();
        if (!busy) {
            var s = new ParamsBuilder();
            if (doCheck) s.add('SESSION_CHECK', SessionCheck);

            /* Rooms */
            _.each(rooms.Base, function (room, id) {
                s.add('r' + id, room.CheckSum());
            });

            /* Users */
            _.each(users.Base, function (user, id) {
                s.add('u' + id, user.CheckSum());
            });

            /* Messages */
            s.add('last_id', lastMessageId);

            /* Move to room */
            if (newRoomId) {
                s.add('room_id', newRoomId);
                newRoomId = 0;
            };

            $.post(servicesPath + 'pong.service.php', s.build()).done(this.pong.bind(this));

            requestSent = 1;
            tiomeoutTimer = setTimeout(PingTimeout, 20000);
            busy = 1;
        }
        pingTimer = setTimeout(this.ping.bind(this), 10000);
    };

    this.forcePing = function (doCheck) {
        if (!requestSent) {
            busy = 0;
            clearTimeout(pingTimer);
            this.ping(doCheck);
        }
    };

    function CompareSessions() {
        cookieSession = getCookie(SessionKey);
        if (cookieSession != Session) {
            Quit();
        }
    };

    this.send = function () {
        var recepients = tabs.current.recepients.Gather(),
            textField = $('#Message');
        if (!recepients && !textField.val()) {
            return;
        }
        var s = new ParamsBuilder();
        s.add('message', textField.val());
        s.add('type', messageType);
        s.add('recepients', recepients);

        $.post(servicesPath + 'message.service.php', s.build()).done(this.received.bind(this));

        if (!tabs.current.IsPrivate) {
            tabs.current.recepients.Clear();
            messageType = '';
            ShowRecepients();
        }
        ArrayInsertFirst(MessagesHistory, textField.val(), historySize);
        HistoryGo(-1);

        textField.val('');
        return false;
    };
    $('form[name=messageForm]').on('submit', this.send.bind(this));

    this.received = function (req) {
        try {
            if (req.responseText) {
                eval(req.responseText);
            }
        } catch (e) {}
        this.forcePing();
    };

    /* Alerts & Confirmations */

    function StopPings() {
        clearTimeout(pingTimer);
        clearTimeout(tiomeoutTimer);
    };

    function Quit() {
        StopPings();
        co.AlertType = true;
        co.Show("./", "Сессия завершена", "Ваша сессия в чате завершена. Повторите авторизацию для повторного входа в чат.", "", true);
    };

    function Kicked(reason) {
        StopPings();
        co.AlertType = true;
        co.Show("./", "Вас выгнали из чата", "Формулировка:<ul class='Kicked'>" + reason + "</ul>");
    };

    function Banned(reason, admin, admin_id, till) {
        StopPings();
        co.AlertType = true;
        var s = Format("Администратор #info# забанил вас " + (till ? "до " + till : ""), admin_id, admin);
        s += reason ? " по причине <h4>&laquo;" + reason + "&raquo;</h4>" : "";
        s += "Пожалуйста, ознакомьтесь с <a href=/rules.php>правилами</a> чата.<br>До свидания.";
        co.Show("/rules.php", "Пользователь забанен", s);
    };

    function Forbidden(reason) {
        StopPings();
        co.AlertType = true;
        co.Show(".", "Доступ в чат закрыт", "Администратор закрыл для вас доступ в чат" + ($reason ? "<br>с формулировкой &laquo;" + reason + "&raquo;" : ""));
    };

    function ChangeName() {
        co.Show(SaveNicknameChanges, "Смена имени в чате", "Выберите имя:", new ChangeNickname(), true);
    };

    function MessageForbiddenAlert() {
        co.AlertType = true;
        co.Show("", "Публикация сообщения невозможна!", "Публикация сообщений невозможна в приватных комнатах, если у вас нет туда допуска.");
    };

    this.initMenu = function (div) {
        var menu = new MenuItemsCollection(true),
            mainItem = new MenuItem(1, 'Команды');

        mainItem.SubItems.Items.Add(new MenuItem(3, 'Отойти (Away)', _.partial(MI, 'away')));
        mainItem.SubItems.Items.Add(new MenuItem(4, 'Сменить статус', _.partial(MI, 'status')));

        mainItem.SubItems.Items.Add(new MenuItem(5, 'Сменить никнейм', ChangeName));
        if (me && me.Rights >= topicRights) {
            var t = new MenuItem(6, 'Сменить тему', _.partial(MI, 'topic'));
            if (me.Rights >= adminRights) {
                t.SubItems.Items.Add(new MenuItem(1, 'С блокировкой', _.partial(MI, 'locktopic')));
                t.SubItems.Items.Add(new MenuItem(2, 'Разблокировать', _.partial(MI, 'unlocktopic')));
            }
            mainItem.SubItems.Items.Add(t);
        }
        mainItem.SubItems.Items.Add(new MenuItem(7, '<b>Меню</b>', ShowOptions));
        mainItem.SubItems.Items.Add(new MenuItem(8, 'Выход из чата', _.partial(MI, 'quit')));

        menu.Items.Add(mainItem);
        menu.Create(div);
    };

    this.ping();
}

/* Messages */

function addLine(container, line) {
    container.innerHTML = line + "\n" + container.innerHTML;
};

function AM(text, message_id, author_id, author_name, to_user_id, to_user) {
    message_id = 1 * message_id;
    var tab = tabs.main;
    var renewTabs = false;
    if (to_user_id > 0) {
        tab = tabs.tabsCollection.Get(to_user_id);
        if (!tab) {
            tab = new Tab(to_user_id, to_user, 0, 1);
            tabs.Add(tab);
            renewTabs = true;
        }
    }

    if (tab && (!message_id || message_id && tab.lastMessageId < message_id)) {
        text = Format(text, author_id, author_name);
        addLine(tab.RelatedDiv, text);
        if (message_id) {
            lastMessageId = message_id;
            tab.lastMessageId = message_id;
        }
        if (tab.Id != tabs.current.Id) {
            tab.UnreadMessages++;
            renewTabs = true;
        }
    }
    if (renewTabs) tabs.Print();
};

function ClearMessages() {
    if (MainTab) {
        MainTab.RelatedDiv.innerHTML = "";
    }
};

/* Rooms */

var newRoomTab;
function PrintRooms() {
    $('#UsersContainer').html(rooms.ToString());

    var container = $("#NewRoom")[0];
    if (!newRoomTab && me && me.Rights >= 11) {
        // Allowed to create rooms
        MakeNewRoomSpoiler(container);
    }
    displayElement(container, rooms.Count() < 10);
    UpdateTopic();
};

function MoveToRoom(id) {
    MainTab.Clear();
    newRoomId = id;
    this.forcePing();
};

function MakeNewRoomSpoiler(container) {
    newRoomTab = new Spoiler(100, "Создать комнату", 0, "", function (tab) {
        new RoomLightweight().loadTemplate(tab, me.Id);
    });
    newRoomTab.ToString(container);
};

/* Topic */

function UpdateTopic() {
    if (CurrentRoom) {
        if (me && me.HasAccessTo(CurrentRoom)) {
            SetTopic(CurrentRoom.Topic, CurrentRoom.TopicAuthorId, CurrentRoom.TopicAuthorName, CurrentRoom.TopicLock);
        } else {
            SetTopic("Доступ в комнату ограничен. Ожидайте допуска.");
        }
    }
};

function SetTopic(text, author_id, author_name, lock) {
    topicMessage = text;
    if (MainTab) {
        var s = "";
        if (text) {
            s = "<div>" + (1 * lock ? "<em>x</em>" : "") + "&laquo;<strong>" + MakeSmiles(topicMessage) + "</strong>&raquo;";
            if (author_name) {
                s += ", ";
                if (author_id) {
                    s += Format("#info#", author_id, author_name);
                } else {
                    s += author_name;
                }
            }
            s += "</div>";
        }
        MainTab.TopicDiv.innerHTML = s;
        MainTab.TopicDiv.className = "TabTopic" + (!author_id && !author_name ? " Alert" : "");
    }

    document.title = "(" + users.Count() + ") " + (author_name ? "\"" : "") + StripTags(text) + (author_name ? "\", " + author_name : "");
};

/* Recepients */

var messageType = '';
function ShowRecepients() {
    if (tabs.current) {
        var rec = tabs.current.recepients.ToString(),
            recepients = $('#RecepientsContainer');

        recepients.html(rec);
        if (rec) {
            var prefix;
            if (tabs.current.Id == tabs.main.Id) {
                try {
                    prefix = {
                        'wakeup': 'Wakeup для ',
                        'kick': 'Выгнать ',
                        'ban': 'Забанить ',
                        'me': 'О себе в третьем лице',
                        'status': 'Установить статус',
                        'topic': 'Установить тему',
                        'locktopic': 'Установить и заблокировать тему',
                        'unlocktopic': 'Установить и разблокировать тему',
                        'away': 'Отойти',
                        'quit': 'Выйти из чата'
                    }[messageType];
                } catch (e) {
                    prefix = 'Для ';
                }
            }
            recepients.html(prefix + rec);
        } else {
            messageType = '';
        }
    }
    $('#Message').focus();
};

// Add recepient
function AR(id, name, type) {
    if (id == me.Id) {
        return;
    };
    if (tabs.main) {
        if (type != messageType) {
            messageType = type;
            tabs.main.recepients.Clear();
        }
        if (id && name) {
            tabs.main.recepients.Add(new Recepient(id, name));
            if (tabs.current.Id != tabs.main.Id) {
                tabs.main.switchTo();
            }
        }
        ShowRecepients();
    }
};

// Delete recepient
function DR(id) {
    if (tabs.main) {
        tabs.main.recepients.Delete(id);
        ShowRecepients();
    }
};

// Menu item
function MI(type) {
    AR(-1, ' ', type);
};

function IG(id, state) {
    var s = new ParamsBuilder();
    s.add('user_id', id);
    s.add('state', state);
    $.post(servicesPath + 'ignore.service.php', s.build()).done(Ignored);
};

function Ignored(data) {
    var id = data.responseText;
    if (id) {
        var u = users.Get(Math.abs(id));
        if (u) {
            u.IsIgnored = id > 0 ? 1 : "";
        }
    }
    PrintRooms();
};

// Grant room access
function AG(id, state) {
    var s = ParamsBuilder();
    s.add('user_id', id);
    s.add('state', state);
    $.post(servicesPath + 'room_user.service.php', s.build());
};

var MessagesHistory = [];
var historySize = 100;
var historyPointer = -1;
function HistoryGo(i) {
    if (i >= -1 && i < MessagesHistory.length) {
        historyPointer = i;
        $('#History').text('История сообщений (' + (historyPointer + 1) + '/' + MessagesHistory.length + ')');
        $('#Message').val(i >= 0 ? MessagesHistory[historyPointer] : '');
    }
};
HistoryGo(0);

var eng = "qwertyuiop[]asdfghjkl;'zxcvbnm,.QWERTYUIOP{}ASDFGHJKL\\:ZXCVBNM<>/&?@`~";
var rus = "йцукенгшщзхъфывапролджэячсмитьбюЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ.?,\"ёЁ";

function Translit() {
    var textField = $('#Message'),
        val = textField.val(),
        out = '';

    for (i = 0, l = val.length; i < l; i++) {
        s = val.charAt(i);
        engIndex = eng.indexOf(s);
        if (engIndex >= 0) {
            s = rus.charAt(engIndex);
        } else {
            rusIndex = rus.indexOf(s);
            if (rusIndex >= 0) {
                s = eng.charAt(rusIndex);
            }
        }
        out += s;
    }
    textField.val(out);
};
"use strict";

var hiderElement, replyFormElement;
var isVisible = 0,
    lastLink;
var replyErrorElement, replyTitleElement, replyContentElement, replyIsProtected;

var replyMessageId, forumId;

var citeLevel;

var brClean = new RegExp("(<br[^>]*>)", "g");
var addCite = new RegExp("(\\n)", "g");

function FindReplyElements() {
    // Finds all reply form elements necessary

    hiderElement = $("#Hider")[0];
    replyFormElement = $("#ReplyForm")[0];

    replyErrorElement = $("#ERROR");
    replyTitleElement = $("#TITLE");
    replyContentElement = $("#CONTENT");
    replyIsProtected = $("#IS_PROTECTED");
};

function SubstrCount(s, subStr, offset) {
    var ex = new RegExp("(" + subStr + ")");
    while (s.match(ex)) {
        citeLevel += offset;
        s = s.replace(ex, "");
    }
    return s;
}

function MakeCite() {
    // Makes cite from text
    if (lastLink) {
        var text = lastLink.parentNode.parentNode.previousSibling.innerHTML;
        text = text.replace(brClean, "");

        cites = new Array(10).join('>');
        citeLevel = 1;

        lines = text.split("\n");
        text = "";
        for (i = 0; i < lines.length; i++) {
            line = lines[i];

            line = SubstrCount(line, "<cite>", 1);
            line = SubstrCount(line, "</cite>", -1);

            text += cites.substr(0, citeLevel) + line + "\n";
        }

        if (replyContentElement) {
            replyContentElement.val(text);
        }
    }
};

function ClearHash() {
    document.location.hash = "";
};

function CancelReply() {
    hiderElement.appendChild(replyFormElement);
    isVisible = 0;
    ClearHash();
};

function FindTargetElement(el) {
    var div = el.parentNode.parentNode;
    if (div.nextSibling) {
        return div.nextSibling;
    } else {
        var ul = d.createElement("ul");
        div.parentNode.appendChild(ul);
        return ul;
    }
};

function FindParentTag(tag, el, skip) {
    var parent = el.parentNode;
    if (!skip) {
        skip = 0;
    };
    if (parent && parent.tagName) {
        if (parent.tagName.toLowerCase() === tag) {
            if (!skip) {
                return parent;
            }
            skip--;
        }
        return FindParentTag(tag, parent, skip);
    }
    return null;
};

function SetElementClass(tag, el, className) {
    if (el.tagName && el.tagName.toLowerCase() === tag) {
        el.className = className;
    }
};

function SetChildClass(tag, el, className) {
    if (!el) {
        return;
    }

    SetElementClass(tag, el, className);

    for (var i = 0, l = el.childNodes.length; i < l; i++) {
        var child = el.childNodes[i];
        SetElementClass(tag, child, className);

        if (child.hasChildNodes()) {
            SetChildClass(tag, child, className);
        }
    }
};

var linkExpr = new RegExp('^#[a-z]\\d+$');
var linkNewExpr = new RegExp('^#new_comment$');

// Opens reply form after authentication
function OpenReplyForm() {
    var l = window.location.hash;
    if (l) {
        if (linkExpr.test(l)) {
            $('a[name=' + l.substr(1) + ']').click();
        } else if (linkNewExpr.test(l)) {
            $('a[name=new_comment]').click();
        }
    }
};

function ForumReply(a, id, forum_id) {
    $("#callback").val(a.href);
    if (!GetCurrentSession()) {
        $("#auth_form").dialog("open");
        return false;
    }

    if (!replyFormElement) {
        FindReplyElements();
    }
    if (!replyFormElement) {
        return false;
    } else {
        ForumClearReply();
        replyMessageId = id;
        forumId = forum_id;
        if (isVisible && lastLink == a) {
            CancelReply();
        } else {
            // Treat protected replies
            LockProtection(a.parentNode.previousSibling);
            replyErrorElement.hide();

            insertAfter(replyFormElement, a.parentNode.parentNode);
            isVisible = 1;
            if (replyTitleElement) {
                replyTitleElement.focus();
            }
        }
    }
    lastLink = a;
};

function LockProtection(el) {
    if (!el || !replyIsProtected) {
        return;
    }
    var state = el.className && el.className.indexOf("Protected") >= 0;
    replyIsProtected.attr('checked', state);
    if (state) replyIsProtected.attr('disabled', 'disabled');else replyIsProtected.removeAttr('disabled');
};

// Clears reply form
function ClearReply() {
    if (replyTitleElement) {
        replyErrorElement.html('');
        replyTitleElement.val('');
        replyContentElement.val('');
    }
};

// Clears form and message and forum relations
function ForumClearReply() {
    replyMessageId = "";
    forumId = "";
    ClearReply();
};

// Sends new message information to server
function AddMessage(lnk) {
    // Tries to submit the form
    if (replyTitleElement) {
        lnk.disabled = true;
        var s = new ParamsBuilder().add('RECORD_ID', replyMessageId).add('FORUM_ID', forumId).add('TITLE', replyTitleElement.val()).add('CONTENT', replyContentElement.val()).add('IS_PROTECTED', replyIsProtected.is('checked') ? 1 : 0);

        sendRequest(servicesPath + "forum.service.php", ForumMessageAddcallback, s.build(), lastLink);
    }
};

// New message adding callback
function ForumMessageAddcallback(reesponseText, el) {
    var newId = "",
        newRecord = "",
        error = "",
        logged_user = "";
    eval(reesponseText);

    if (!error) {
        CancelReply();
        var ul = FindTargetElement(el);
        if (ul) {
            var li = d.createElement("li");
            li.innerHTML = newRecord;

            if (ul.hasChildNodes()) {
                ul.insertBefore(li, ul.firstChild);
            } else {
                ul.appendChild(li);
            }
            if (newId) {
                window.location.hash = "cm" + newId;
            }
        }
    } else {
        if (replyErrorElement) {
            replyErrorElement.html(error).show().delay(5000).hide('blind', {}, 'slow');
        }
    }
    $("#SubmitMessageButton").removeAttr("disabled");
};

// Message deletion
function ForumDelete(a, id, forum_id) {
    var s = new ParamsBuilder().add('RECORD_ID', id).add('FORUM_ID', forum_id).add('go', 'delete');

    sendRequest(servicesPath + "forum.service.php", ForumMessageDelcallback, s.build(), a);
}

// Deletion callback
function ForumMessageDelcallback(responseText, a) {
    var error = "",
        className = "";
    eval(responseText);
    if (!error) {
        var li = FindParentTag("li", a, 1);
        SetChildClass("li", li, className);
    } else {
        alert(error); // TODO:
    }
};

// OpenID provider visual selection
var selectedProvider = "";
function SetOpenID(id, el, a) {
    el = $("#" + el);
    if (!el || !id || !a) {
        return;
    }
    if (selectedProvider) {
        selectedProvider.className = "";
    }
    selectedProvider = a;
    a.className = "Selected";
    a.blur();
    el[0].val(id);
};

function startup() {
    OpenReplyForm();
};
"use strict";

function GetSmilesContainer() {
    c = $("#Smiles")[0];
    if (!c) {
        return;
    }
    return c;
};

function SwitchSmiles() {
    c = GetSmilesContainer();
    if (!c) {
        return;
    }
    c.className = c.className == "On" ? "" : "On";
    _.result(window, 'onResize');
    return true;
};

function InitSmiles(arr) {
    c = GetSmilesContainer();
    if (!c) {
        return;
    }
    div = d.createElement("div");
    sc = new Collection();
    for (var i = 0, l = arr.length; i < l; i++) {
        var s = new Smile(arr[i]);
        sc.Add(s);
    }
    sc.ToString(div);
    c.appendChild(div);
};

// Smile class

function Smile(src) {
    this.Id = src;
    this.Token = "*" + src.substr(0, src.indexOf(".")) + "*";
    this.Rendered = new Image();
    this.Rendered.src = "/img/smiles/" + src;
};

Smile.prototype.ToString = function (holder, index) {
    var a = d.createElement("a");
    a.href = voidLink;
    a.Obj = this;
    a.onclick = function () {
        _s(this.Obj.Token);SwitchSmiles();
    };
    a.appendChild(this.Rendered);
    holder.appendChild(a);
    holder.appendChild(d.createTextNode(" "));
};
// Scripts related to site pages only, not menu

/*var alerts = new MyFrame($('AlertContainer'));
var winSize = new MyFrame(window);

function AdjustDivs() {
    winSize.GetPosAndSize();
    xo = d.all ? d.body.scrollLeft : window.pageXOffset;
    yo = d.all ? d.body.scrollTop : window.pageYOffset;

    alerts.Replace(xo, yo, winSize.width, winSize.height);
}


DisplayElement(alerts.element, false);
AdjustDivs();

window.onresize = AdjustDivs;
window.onscroll = AdjustDivs;
if (window.addEventListener) {
    window.addEventListener("resize", AdjustDivs, true);
    window.addEventListener("scroll", AdjustDivs, true);
};
*/
"use strict";
"use strict";

d = document;
t = d.getElementById("bb");

thick = 3;
gap = 5;
genWidth = 400;
half = 5;

function dot(x, y, color) {
    this.x = x;
    this.y = y;
    this.color = color;
}

function line(x, y, x1, y1, color) {
    if (x > x1) {
        x = x + x1;
        x1 = x - x1;
        x = x - x1;
    }
    if (y > y1) {
        y = y + y1;
        y1 = y - y1;
        y = y - y1;
    }

    div = d.createElement("p");
    try {
        div.style.left = x + "px";
        div.style.top = y + "px";
        div.style.width = x1 - x + "px";
        div.style.height = y1 - y + "px";
    } catch (e) {
        // IE doesn't like "px"
    }
    if (color) {
        div.style.backgroundColor = color;
    }
    div.inerHTML = "&nbsp;";
    t.appendChild(div);
};

function hl(x, y, w, color) {
    line(x, y, x + w, y + thick, color);
};

function vl(x, y, w, color) {
    line(x, y, x + thick, y + w, color);
};

function b(x, y, u, color, is_current) {
    span = d.createElement("label");
    color = is_current ? "orangered" : color;
    a = d.createElement("a");
    a.User = u;
    a.innerHTML = u.Login;
    a.onclick = function () {
        DrawTree(this);
    };
    a.href = "javascript:void(0)";
    span.appendChild(a);
    span.style.left = x + "px";
    span.style.top = y + "px";
    if (color) {
        span.style.backgroundColor = color;
    }
    t.appendChild(span);
};

function g(gen, x) {
    h4 = d.createElement("h4");
    h4.innerHTML = gen;
    h4.style.left = x + 10 + "px";
    h4.style.top = "100px";
    t.appendChild(h4);
};

function relation(arr) {
    this.From = arr[0];
    this.To = arr[1];
    this.Type = arr[2];
};

function user(arr) {
    this.Id = arr[0];
    this.Login = arr[1];
    this.Generation = arr[2];
};

function AppendSubArray(arr, key, value) {
    a = arr[key];
    if (!a) {
        a = [];
    }
    a[a.length] = value;
    arr[key] = a;
};

users = [];
generations = [];
maxGeneration = 0;
if (!window['u']) {
    u = [];
    r = [];
};

for (i in u) {
    user1 = new user(u[i]);
    users[user1.Id] = user1;
    AppendSubArray(generations, user1.Generation, user1);
    maxGeneration = maxGeneration > user1.Generation ? maxGeneration : user1.Generation;
}

relations = [];
userRelations = [];

for (i in r) {
    rel1 = new relation(r[i]);
    relations[relations.length] = rel1;
    AppendSubArray(userRelations, rel1.From, rel1);
}

function MarkRelation(id1, id2) {
    exists[id1 + "_" + id2] = 1;
    exists[id2 + "_" + id1] = 1;
};

function MarkRelations(arr) {
    for (i = 0, l = arr.length; i < l - 1; i++) {
        for (k = i + 1; k < l; k++) {
            MarkRelation(arr[i], arr[k]);
        }
    }
};

function RelationExists(rel) {
    return exists[rel.From + "_" + rel.To] == 1;
};

function AddGenLevel(arr, generation) {
    arr[generation] = arr[generation] ? arr[generation] + 1 : 1;
};

function CheckLess(old, v1, v2) {
    if (v1 < v2) {
        return v1 < old ? v1 : old;
    } else {
        return v2 < old ? v2 : old;
    }
};

function CheckMore(old, v1, v2) {
    if (v1 < v2) {
        return v2 > old ? v2 : old;
    } else {
        return v1 > old ? v1 : old;
    }
};

function DrawTree(u) {
    queue = [];
    if (u == undefined) {
        u = "";
    } else {
        u = u.User;
        queue[u.Id] = 1;
    }
    t.innerHTML = "";

    exists = [];

    lefts = [];
    rights = [];
    parentsLinks = [];
    recommendedColors = [];

    generationsX = [];

    index = 0;
    colors = ["red", "green", "blue", "orangered", "brown", "darkblue", "darkorange", "purple", "olive", "lightseagreen"];

    actually = 0;
    for (i = 0; i < maxGeneration; i++) {
        gen = generations[i];
        people = gen.length;

        generationsX[i] = i ? generationsX[i - 1] + (120 + (u ? 30 : people * 6)) * (actually ? 1 : 0) : 50;
        actually = 0;

        for (y = 0; y < people; y++) {
            user1 = gen[y];
            passed = true;
            if (u) {
                if (queue[user1.Id] != 1) {
                    passed = false;
                    rels = userRelations[user1.Id];
                    if (rels) {
                        for (k = 0, l = rels.length; k < l; k++) {
                            rel = rels[k];
                            if (rel.From == u.Id || rel.To == u.Id) {
                                id = rel.From == u.Id ? rel.To : rel.From;
                                queue[id] = 1;
                                passed = true;
                            }
                        }
                    }
                }
            } else {
                actually = y;
            }

            if (passed) {
                user1.x = generationsX[i];
                user1.y = actually * 20 + 150;
                b(user1.x, user1.y, user1, "orange", u && user1.Id == u.Id);
                gen[y] = user1;
                actually++;
            }
        }
        if (actually) {
            g(i, generationsX[i]);
        }
    };

    for (kk in generations) {
        for (i in generations[kk]) {
            user1 = generations[kk][i];

            left = 10 + (lefts[user1.Generation] ? gap * lefts[user1.Generation] : 0);
            right = 100 + (rights[user1.Generation] ? gap * rights[user1.Generation] : 0);
            color = recommendedColors[user1.Login] ? recommendedColors[user1.Login] : colors[index++ % colors.length];
            newColor = colors[index++ % colors.length];

            flagLeft = false;
            flagRight = false;
            parents = [];
            parentId = 0;
            hasBrothers = false;

            linked = [user1.Id];

            rightTop = user1.y;
            rightBottom = user1.y;

            leftTop = user1.y;
            leftBottom = user1.y;

            for (k in userRelations[user1.Id]) {
                rel1 = userRelations[user1.Id][k];

                user1 = users[rel1.From]; // ?
                user2 = users[rel1.To];

                if (u && (queue[rel1.From] != 1 || queue[rel1.To] != 1)) {
                    continue;
                }

                if (rel1.Type == "b" || rel1.Type == "s") {
                    hasBrothers = true;
                }

                if (!RelationExists(rel1)) {
                    if (user1 && user2) {
                        if (rel1.Type == "b" || rel1.Type == "s") {
                            hl(user1.x - left, user1.y + half, left, color);
                            hl(user2.x - left, user2.y + half, left, color);
                            vl(user1.x - left, user1.y + half, user2.y - user1.y, color);

                            flagLeft = true;
                            linked[linked.length] = user2.Id;

                            leftTop = CheckLess(leftTop, user1.y + half, user2.y + half);
                            leftBottom = CheckMore(leftBottom, user1.y + half, user2.y + half);
                        } else if (rel1.Type == "h" || rel1.Type == "w") {
                            hl(user1.x, user1.y + half, right + thick, newColor);
                            hl(user2.x, user2.y + half, right + thick, newColor);
                            vl(user1.x + right, user1.y + half, user2.y - user1.y, newColor);

                            linked[linked.length] = user2.Id;
                            flagRight = true;

                            rightTop = CheckLess(rightTop, user1.y + half, user2.y + half);
                            rightBottom = CheckMore(rightBottom, user1.y + half, user2.y + half);

                            parents[parents.length] = user1.Id;
                            parents[parents.length] = user2.Id;
                        } else if (rel1.Type == "m" || rel1.Type == "f") {
                            if (!recommendedColors[user2.Login]) {
                                recommendedColors[user2.Login] = newColor;
                            }
                        } else if (rel1.Type == "c") {
                            parentId = user2.Id;
                        }
                    }
                }
            }
            MarkRelations(linked);

            if (parents.length > 0) {
                mid = rightTop + Math.round((rightBottom - rightTop) / 2);
                dot1 = new dot(user1.x + right, mid, newColor);

                for (i = 0, l = parents.length; i < l; i++) {
                    parentsLinks[parents[i]] = dot1;
                }
            }
            if (flagRight) {
                AddGenLevel(rights, user1.Generation);
            }
            if (parentId && parentsLinks[parentId]) {
                parentsDot = parentsLinks[parentId];
                prevGen = user1.Generation - 1;
                if (hasBrothers) {
                    mid = leftTop + Math.round((leftBottom - leftTop) / 2) + index % 8;
                    left = 10 + (lefts[user1.Generation] ? gap * lefts[user1.Generation] : 0);
                } else {
                    mid = user1.y + half;
                    left = -thick;
                }

                lefter = generationsX[prevGen] + 100 + (rights[prevGen] ? gap * rights[prevGen] : 0);

                hl(parentsDot.x, parentsDot.y, lefter - parentsDot.x + thick, color);
                hl(lefter, mid, user1.x - left - lefter, color);
                vl(lefter, parentsDot.y, mid - parentsDot.y, color);

                AddGenLevel(rights, prevGen);

                parentsLinks[parentId] = 0;
            }
            if (flagLeft) {
                AddGenLevel(lefts, user1.Generation);
            }
        }
    }
}
'use strict';

var replyFormHeight = 75;
var offset = 0;

function ReplyForm() {
    $('#WakeupReply').toggle(!offset);
    offset = replyFormHeight - offset;
    _.result(window, 'onResize');
    $('#reply').focus();
}

function SendWakeup(message_id) {
    var s = new ParamsBuiler();
    s.add('message', $('#reply').val());
    s.add('reply_to', message_id);
    $.post('../services/wakeup.service.php', s.build).done(MessageAdded);

    $('#status').removeClass().addClass('RoundedCorners').css('background-color', '#404040').html('Отправка сообщения...');
    $('#reply').val('');
}

function MessageAdded(responseText) {
    if (responseText) {
        $('#status').css('background-color', responseText.charAt(0) == '-' ? '#983418' : '#728000').html(responseText.substring(1));
    }
    setTimeout(self.close(), 2000);
}