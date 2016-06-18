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

MenuItem.prototype.Gather = function(holder) {
    var a = d.createElement('a');
    a.innerHTML = this.Title;
    a.href = voidLink;
    if (this.Action) {
        a.onclick = this.Action;
    };

    var li = document.createElement("li");
    li.RelatedItem = this;
    li.onmouseover = function() {DisplaySubmenu(this, true);};
    li.onmouseout = function() {DisplaySubmenu(this, false);};
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

MenuItemsCollection.prototype.Create = function(where) {
    this.Container.innerHTML = "";
    if (this.Items.Count() > 0) {
        this.Items.Gather(this.Container);
        where.appendChild(this.Container);
    }
};

MenuItemsCollection.prototype.Display = function(state) {
    displayElement(this.Container, state);
};

function DisplaySubmenu(el, state, force) {
    if (el.RelatedItem && el.RelatedItem.SubItems) {
        el.RelatedItem.SubItems.Display(state);
        el.className = state ? "Selected" : "";
    }
};
