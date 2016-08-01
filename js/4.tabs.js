/*
  Tab class. Entity of Tabs one.
*/

class TabBase {
  initUploadFrame(property) {
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
  }

  // obj - object to be assigned as a.obj (Tab by default) 
  AddSubmitButton(method, holder, obj) {
    var m1 = document.createElement("div");
    m1.className = "ConfirmButtons";
    this.SubmitButton = MakeButton(method, "ok_button.gif", obj || this, "", "Сохранить изменения");
    m1.appendChild(this.SubmitButton);
    this[holder || "RelatedDiv"].appendChild(m1);
  }

  // Tab object reaction by outside call 
  React(value) {
    if (this.Reactor) {
      this.Reactor.React(value);
    }
  }

  // Sets additional className to RelatedDiv 
  SetAdditionalClass(className) {
    this.RelatedDiv.className = "TabContainer" + (className ? " " + className : "");
  }
}


class Tab extends TabBase {
  constructor(id, title, is_locked, is_private, on_select) {
    super();
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
  }

  ToString(index) {
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
    title.innerHTML = this.Title + (this.UnreadMessages ? (' (' + this.UnreadMessages + ')') : '');

    li.appendChild(title);
    if (!this.IsLocked) {
      var close = document.createElement('button');
      close.className = 'CloseSign';
      close.onclick = this.close.bind(this);
      close.innerHTML = '&times;';
      li.appendChild(close);
    };
    return li;
  }

  DisplayDiv(state) {
    displayElement(this.RelatedDiv, state);
    displayElement(this.TopicDiv, state);
  }

  Clear() {
    this.TopicDiv.innerHTML = '';
    this.RelatedDiv.innerHTML = '';
    this.lastMessageId = -1;
  }

  switchTo() {
    if (this.collection) this.collection.switchTo(this.Id);
  }

  close() {
    if (this.collection) this.collection.Delete(this.Id);
  }
}

/*
  Tabs collection class.
*/

class Tabs {
  constructor(tabsContainer, contentContainer) {
    this.TabsContainer = tabsContainer;
    this.ContentContainer = contentContainer;
    this.tabsCollection = new Collection();
    this.current = null;
    this.history = [];

    this.tabsList = document.createElement("ul");
    this.tabsList.className = "Tabs";
    this.TabsContainer.appendChild(this.tabsList);
  }

  Print() {
    var tabsContainer = this.tabsList;
    tabsContainer.innerHTML = '';
    return _.each(
      this.tabsCollection.Base,
      (tab) => tabsContainer.appendChild(tab.ToString())
    );
  }

  Add(tab, existing_container) {
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
  }

  Delete(id) {
    var tab = this.tabsCollection.Get(id);
    if (tab) {
      this.ContentContainer.removeChild(tab.TopicDiv);
      this.ContentContainer.removeChild(tab.RelatedDiv);
      this.tabsCollection.Delete(id);

      _.pull(this.history, id);
      if (this.current.Id == id) this.switchTo(this.history.pop());
      this.Print();
    }
  }

  switchTo(id) {
    var tab = this.tabsCollection.Get(id);
    if (tab) {
      if (_.last(this.history) === id) this.history.push(id);
      this.current = tab;
      tab.UnreadMessages = 0;

      //recepients = tab.recepients;
      _.result(window, 'ShowRecepients');
      this.Print();

      if (tab.onSelect) {
        tab.RelatedDiv.innerHTML = loadingIndicator;
        tab.onSelect(tab);
      };

      _.result(window, 'onResize');
    }
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
