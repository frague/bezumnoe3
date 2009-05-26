//3.2
/*
	Tab class. Entity of Tabs one.
*/

    /* Base tab class */
	function TabBase() {
	};

	TabBase.prototype.InitUploadFrame = function(property) {
		if (!property) {
			property = "UploadFrame";
		}
		if (!this[property]) {
			this[property] = CreateElement("iframe", "UploadFrame" + Math.round(100000*Math.random()));
			this[property].className = "UploadFrame";
			if (this.RelatedDiv) {
				this.RelatedDiv.appendChild(this[property]);
			}
		}
	};

	/* obj - object to be assigned as a.obj (Tab by default) */
	TabBase.prototype.AddSubmitButton = function(method, holder, obj) {
		var m1 = d.createElement("div");
		m1.className = "ConfirmButtons";
		this.SubmitButton = MakeButton(method, "ok_button.gif", obj ? obj : this);
		m1.appendChild(this.SubmitButton);
		this[holder ? holder : "RelatedDiv"].appendChild(m1);
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

	Tab.prototype.ToString = function(index) {
		var isSelected = CurrentTab.Id == this.Id;
		if (isSelected) {
			CurrentTab = this;
		}
		this.DisplayDiv(isSelected);
		var s = "<li class='" + (isSelected ? "Selected " : "") + (this.UnreadMessages ? "HasUnread" : "") + "'" + (this.Alt ? " alt='"+this.Alt+"' title='"+this.Alt+"'" : "") + "><div>";
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

	Tab.prototype.DisplayDiv = function(state) {
		DisplayElement(this.RelatedDiv, state);
		DisplayElement(this.TopicDiv, state);
	};

	Tab.prototype.Clear = function() {
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

	Tabs.prototype.Print = function() {
		this.tabsList.innerHTML = this.tabsCollection.ToString();
	};

	Tabs.prototype.Add = function(tab, existing_container) {
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

	Tabs.prototype.Delete = function(id) {
		var tab = this.tabsCollection.Get(id);
		if (tab) {
			this.ContentContainer.removeChild(tab.TopicDiv);
			this.ContentContainer.removeChild(tab.RelatedDiv);
			this.tabsCollection.Delete(id);
		}
	};

	Tabs.prototype.PrintTo = function(id, text) {
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
				tab.onSelect = "";	/* TODO: Treat failure */
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
