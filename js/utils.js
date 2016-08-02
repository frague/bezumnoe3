import $ from 'jquery';
import _ from 'lodash';
import settings from './settings';

export var utils = {
  getElement(element) {
    if (element && (element.nodeType || element.jquery)) {
      return element;
    }
    return '#' + element;
  },
  
  displayOptions: {effect: 'fade', easing: 'easeInOutBack', duration: 600},
  
  displayElement(el, state) {
    if (!el) {
      return;
    }
    el = this.getElement(el);
    if (state) {
      $(el).show();
    } else {
      $(el).hide();
    }
  },
  
  doShow(el) {
    this.displayElement(el, true);
  },
  
  doHide(el) {
    this.displayElement(el, false);
  },
  
  switchVisibility(el) {
    el = getElement(el);
    $(el).toggle(displayOptions);
  },
  
  emptyPass: '**********',
  
  clearInput(el) {
    if (el.value == this.emptyPass) {
      el.value = '';
    } else {
      el.previousValue = el.value;
    }
  },

  restoreInput(el, relatedBlockId) {
    if (el.value != el.previousValue) {
      this.displayElement(relatedBlockId, el.value);
    }
    if (!el.value) {
      el.value = this.emptyPass;
    }
  },

  createElement(tag, name) {
    var result;
    if (d.all) {
      try {
        result = document.createElement('<' + tag + (name ? ' name=\'' + name + '\' id=\'' + name + '\'' : '') + '>');
      }
      catch(e) {
      }
    }
    if (!result) {
      result = document.createElement(tag);
      if (name) {
        result.name = name;
        result.id = name;
      }
    }
    return result;
  },

  createBitInput(name, checked, isRadio) {
    var result;
    var type = (isRadio ? 'radio' : 'checkbox');

    if (d.all) {
      try {
        result = document.createElement('<input type=\'' + type + '\' name=\'' + name + '\'' + (isRadio ? '' : ' id=\'' + name + '\'') + (checked ? ' checked' : '') + '>');
      }
      catch(e) {
      }
    }
    if (!result) {
      result = document.createElement('input');
      result.type = type;
      result.name = name;
      if (!isRadio) {
        result.id = name;
      }
      if (checked) {
        result.setAttribute('checked', 'true');
      }
    }
    return result;
  },

  createRadio(name, checked) {
    return this.createBitInput(name, checked, true);
  },

  createCheckBox(name, checked) {
    return this.createBitInput(name, checked, false);
  },

  createLabel(target, text) {
    label = this.createElement('label');
    label.innerHTML = text;
    if (target) {
      label.setAttribute('for', target.name ? target.name : target);
    }
    return label;
  },

  createOnOffImage(state) {
    var img = new Image();
    img.src = state ? '/img/icons/done.gif' : '/img/delete_icon.gif';
    return img;
  },

  checkEmpty(value, defaultValue) {
    return _.isEmpty(value) ? defaultValue : value;
  },

  makeButtonLink(target, text, obj, css, alt = '') {
    var link = document.createElement('a');
    link.className = css;
    link.obj = obj;
    if (text) {
      link.innerHTML = text;
    }
    link.alt = alt;
    link.title = alt;
    link.onclick = _.isFunction(target) ? target : () => eval(target);
    return link;
  },

  makeButton(target, src, obj, css = '', alt = '') {
    var link = this.makeButtonLink(target, '', obj, css, alt);
    link.className = 'Button ' + css;
    link.innerHTML = '<img src=\'' + settings.imagesPath + src + '\' alt=\'' + alt + '\' title=\'' + alt + '\' />';
    return a;
  },

  makeDiv(text, tag = 'div') {
    var div = document.createElement(tag);
    div.innerHTML = text;
    return div;
  },

  indexElementChilds(element, hash = []) {
    if (element) {
      if (element.id) {
        hash[element.id] = element;
      }

      _.each(
        element.childNodes,
        (child) => this(child, hash)   // TODO: revise
      );
    }
    return hash;
  },

  insertAfter(newNode, existingNode) {
    if (existingNode.nextSibling) {
      existingNode.parentNode.insertBefore(newNode, existingNode.nextSibling);
    } else {
      existingNode.parentNode.appendChild(newNode);
    }
  },

  bindList(list, holder, obj, emptyMessage) {
    if (!_.isEmpty(list)) {
      _.each(list, (listItem, index) => {
        holder.appendChild(listItem.ToString(index, obj));
      });
    } else {
      holder.innerHTML = emptyMessage;
    }
  },

  /* Methods to set/get value of group of radios in holder element */

  renameRadioGroup(holder, name) {
    if (!name) {
      name = 'group' + Math.random(9999);
    }
    _.each(holder.childNodes, (child, index) => {
      if (child && child.type === 'radio') {
        child.name = name;
      }
    });
  },

  // Recursive methods
  setRadioValue(holder, value) {
    return _.reduce(holder.childNodes, (result, child, index) => {
      if (child.hasChildNodes()) {
        return result || this.setRadioValue(child, value);
      } else if (child && child.type === 'radio') {
        var isChecked = child.value == value;
        child.checked = isChecked;
        return isChecked ? child : result;
      }
    });
  },

  getRadioValue(holder) {
    var result = '';
    _.some(holder.childNodes.length, (child, index) => {
      if (child.hasChildNodes()) {
        var value = this(el);
        if (value) {
          result = value;
          return true;
        }
      } else if (child && child.type === 'radio' && child.checked) {
        result = el.value;  
        return true;
      }
      return false;
    });
    return result;
  },

  // Bubbling

  cancelBubbling(e = window.event) {
    e.cancelBubble = true;
    if (e.stopPropagation) {
      e.stopPropagation();
    }
  },

  addSelectOption(select, name, value, selected) {
    var opt = document.createElement('option');
    opt.value = value;
    opt.text = name;
    opt.selected = !!selected;

    try {
      select.add(opt, null); // standards compliant; doesn't work in IE
    } catch (e) {
      select.add(opt); // IE only
    }
  },

  random(max, notNull = false) {
    return _.random(notNull ? 1 : 0, max, false);
  }, 

  bindRooms(dropDownList) {
    if (dropDownList && opener && opener.rooms) {
      opener.rooms.Gather(dropDownList);
    }
  }
}