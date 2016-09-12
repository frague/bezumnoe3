import _ from 'lodash';
import {settings} from './settings';
import {Collection} from './collection';
import {Recepient} from './recepient';
import {FlexFrame} from './flex_frame';

import React from 'react';
import {utils} from './utils';
/*
  Tab class. Entity of Tabs one.
*/

// export class TabBase {
//   constructor() {
//     this.RelatedDiv = {};
//     this.TopicDiv = {};
//   }

//   initUploadFrame(property) {
//     if (!property) {
//       property = 'UploadFrame';
//     }
//     if (!this[property]) {
//       this[property] = createElement('iframe', 'UploadFrame' + _.random(10, 99));
//       this[property].className = 'UploadFrame';
//       if (this.RelatedDiv) {
//         this.RelatedDiv.appendChild(this[property]);
//       }
//     }
//   }

export var Tab = React.createClass({
  propTypes: {
    name: React.PropTypes.string.isRequired,
    render: React.PropTypes.func.isRequired,
    activate: React.PropTypes.func,
    isPrivate: React.PropTypes.bool
  },

  getInitialState() {
    return {
      recepients: new Collection()
    };
  },

  getRecepients() {
    return this.state.recepients;
  },

  addRecepient(id, name, isLocked = false) {
    this.state.recepients.add(
      new Recepient({id, name, isLocked, deleteHandler: this.removeRecepient})
    );
  },

  removeRecepient(id) {
    this.state.recepients.delete(id);
  },

  activate() {
    if (this.props.activate) {
      this.props.activate(this);
    }
  },

  renderContent() {
    return this.props.render();
  },

  render(isActive = false) {
    return <li key={this.props.name} className={utils.classNames({active: isActive})}>
      {isActive ? 
        this.props.name 
      :
        <a onClick={() => this.activate()}>
          {this.props.name}
        </a>
      }
    </li>
  }
});


/*
  Tabs collection class.
*/

export var Tabs = React.createClass({
  propTypes: {
    setActiveTab: React.PropTypes.func.isRequired
  },

  getInitialState() {
    return {
      tabs: [],
      activeTab: null
    }
  },

  activate(tab) {
    this.setState({activeTab: tab});
    this.props.setActiveTab(tab);
  },

  add(parameters) {
    var tab = new Tab(
      _.extend({activate: this.activate}, parameters)
    );
    this.state.tabs.push(tab);
    if (parameters.switchToIt !== false) {
      this.activate(tab);
    }
    return tab;
  },

  getActiveTab() {
    return this.state.activeTab;
  },

  render() {
    let activeTabName = this.state.activeTab && this.state.activeTab.props.name;
    return <div className='tabs'>
      <FlexFrame key='tab-content' dimensions={[0, 0, 0, -20]}>
        {this.state.activeTab && this.state.activeTab.renderContent()}
      </FlexFrame>
      <ul className='tab-names'>
        {_.map(
          this.state.tabs, 
          (tab, index) => tab.render(tab.props.name === activeTabName)
        )}
      </ul>
    </div>;
  }
  // constructor(tabsContainer, contentContainer) {
  //   this.TabsContainer = tabsContainer;
  //   this.ContentContainer = contentContainer;
  //   this.tabsCollection = new Collection();
  //   this.current = {};
  //   this.history = [];

  //   this.tabsList = document.createElement("ul");
  //   this.tabsList.className = "Tabs";
  //   this.TabsContainer.appendChild(this.tabsList);
  // }

  // Print() {
  //   var tabsContainer = this.tabsList;
  //   tabsContainer.innerHTML = '';
  //   _.each(
  //     this.tabsCollection.Base,
  //     (tab, index) => {
  //       console.log(index);
  //       tabsContainer.appendChild(tab.ToString());
  //     }
  //   );
  // }

  // Add(tab, existingContainer) {
  //   var topic = document.createElement("div");
  //   topic.className = "TabTopic";
  //   this.ContentContainer.appendChild(topic);
  //   tab.TopicDiv = topic;
  //   tab.collection = this;
  //   this.history.push(tab.Id);

  //   if (!existingContainer) {
  //     existingContainer = document.createElement("div");
  //     existingContainer.className = "TabContainer";
  //     this.ContentContainer.appendChild(existingContainer);
  //   }
  //   tab.RelatedDiv = existingContainer;

  //   this.tabsCollection.Add(tab);
  //   tab.DisplayDiv(false);
  // }

  // Delete(id) {
  //   var tab = this.tabsCollection.Get(id);
  //   if (tab) {
  //     this.ContentContainer.removeChild(tab.TopicDiv);
  //     this.ContentContainer.removeChild(tab.RelatedDiv);
  //     this.tabsCollection.Delete(id);

  //     _.pull(this.history, id);
  //     if (this.current.Id == id) this.switchTo(this.history.pop());
  //     this.Print();
  //   }
  // }

  // switchTo(id) {
  //   var tab = this.tabsCollection.Get(id);
  //   if (tab) {
  //     if (_.last(this.history) === id) this.history.push(id);
  //     this.current = tab;
  //     tab.UnreadMessages = 0;

  //     //recepients = tab.recepients;
  //     _.result(window, 'ShowRecepients');
  //     this.Print();

  //     if (tab.onSelect) {
  //       tab.RelatedDiv.innerHTML = settings.loadingIndicator;
  //       tab.onSelect(tab);
  //     };

  //     _.result(window, 'onResize');
      
  //     tab.DisplayDiv(true);
  //   }
  // }
});


/* Service functions */


function CloseTab(id) {
  var tab = tabs.tabsCollection.Get(id);
  if (tab) {
    tabs.Delete(id);
    SwitchToTab(MainTab.Id);
    tabs.Print();
  }
}
