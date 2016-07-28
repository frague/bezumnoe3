/* Service methods */

function getElement(el) {
    if (el && (el.nodeType || el.jquery)) {
        return el;
    }
    return "#" + el;
};

var displayOptions = {effect: "fade", easing: "easeInOutBack", duration: 600};

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

        _.each(
            el.childNodes,
            function(child) {
                IndexElementChildElements(child, hash);
            }
        );
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
