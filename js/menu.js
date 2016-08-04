import _ from 'lodash';
import {utils} from './utils';
import {Collection} from './collection';

export class MenuItem {
  constructor(title, action, is_locked) {
    this.Id = _.uniqueId();
    this.title = title;
    this.action = action;
    this.isLocked = is_locked;
    this.children = new MenuItemsCollection();
  }

  render(holder) {
    var a = document.createElement('a');
    a.innerHTML = this.title;
    if (_.isFunction(this.action)) {
      a.onclick = this.action;
    };

    var li = document.createElement("li");
    li.onmouseover = () => this.displaySubmenu(true);
    li.onmouseout = () => this.displaySubmenu(false);
    li.onclick = li.onmouseout;

    if (this.children.Count() > 0) {
      this.children.render(li);
    }

    li.appendChild(a);
    holder.appendChild(li);
  }
  
  displaySubmenu(state) {
    if (this.children) {
      this.children.display(state);
    }
  }
}

export class MenuItemsCollection extends Collection {
  constructor(shown) {
    super();

    this.container = document.createElement("ul");
    utils.displayElement(this.container, shown);
  }

  render(container) {
    this.container.innerHTML = '';
    if (this.Count() > 0) {
      this.render(this.container);
      container.appendChild(this.container);
    }
  }

  display(state) {
    utils.displayElement(this.container, state);
  }
}
