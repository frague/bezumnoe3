function MenuItem(title, action, is_locked) {
  this.Id = _.uniqueId();
  this.Title = title;
  this.Action = action;
  this.IsLocked = is_locked;
  this.children = new MenuItemsCollection();
  console.log(title, this.children.Count());
};

MenuItem.prototype.render = function(holder) {
  var a = document.createElement('a');
  a.innerHTML = this.Title;
  if (this.Action) {
    a.onclick = this.Action;
  };

  var li = document.createElement("li");
  li.RelatedItem = this;
  li.onmouseover = function() {DisplaySubmenu(this, true)};
  li.onmouseout = function() {DisplaySubmenu(this, false)};
  li.onclick = li.onmouseout;

  if (this.children.Count() > 0) {
    // console.log(this.Title, 'Create children', this.children);
    this.children.Create(li);
  }

  li.appendChild(a);
  holder.appendChild(li);
};

function MenuItemsCollection(shown) {
  Collection.call(this);
  this.Container = document.createElement("ul");
  if (!shown) {
    displayElement(this.Container, false);
  };
};

MenuItemsCollection.prototype = new Collection();

MenuItemsCollection.prototype.Create = function(container) {
  this.Container.innerHTML = "";
  if (this.Count() > 0) {
    this.render(this.Container);
    container.appendChild(this.Container);
  }
};

MenuItemsCollection.prototype.Display = function(state) {
  displayElement(this.Container, state);
};

function DisplaySubmenu(el, state, force) {
  if (el.RelatedItem && el.RelatedItem.children) {
    el.RelatedItem.children.Display(state);
    el.className = state ? "Selected" : "";
  }
};
