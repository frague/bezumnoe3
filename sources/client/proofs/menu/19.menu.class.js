//1.0
/*
	Menu items
*/

var menuItemWidth = 100;
var menuItemHeight = 20;

function MenuItem(id, title) {
	this.Id = id;
	this.Title = title;
	this.SubItems = new MenuItemsCollection();
};

MenuItem.prototype.Gather = function(holder) {
	var a = document.createElement("a");
	a.innerHTML = this.Title;
	a.href = voidLink;

	var li = document.createElement("li");
	li.RelatedItem = this;
	li.onmouseover = function() {DisplaySubmenu(this, true)};
	li.onmouseout = function() {DisplaySubmenu(this, false)};
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
		this.Container.style.display = "none";
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
	this.Container.style.display = state ? "" : "none";
};

function DisplaySubmenu(el, state) {
	if (el.RelatedItem && el.RelatedItem.SubItems) {
		el.RelatedItem.SubItems.Display(state);
		el.className = state ? "Selected" : "";
	}
};