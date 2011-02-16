//5.3
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

function $(id) {
	if (d.getElementById) {
		return d.getElementById(id);
	} else if (d.all) {
		return d.all[id];
	} else if (d.layers) {
		return d.layers[id];
	}
	return false;
};

function DisplayElement(el, state) {
	if (!el.style) {
		el = $(el);
	}
	if (el) {
		el.style.display = state ? "" : "none";
	}
}

function DoShow(el) {
	DisplayElement(el, true);
};

function DoHide(el) {
	DisplayElement(el, false);
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
		var el2 = $(relatedBlockId);
		if (el2) {
			DisplayElement(el2, el.value);
		}
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
		} 
		catch(e) {
		}
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
	var type = (is_radio ? "radio" : "checkbox");

	if (d.all) {
		try {
			result = d.createElement("<input type=\"" + type + "\" name=\"" + name + "\"" + (is_radio ? "" : " id=\"" + name + "\"") + (checked ? " checked" : "") + ">");
		} 
		catch(e) {
		}
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