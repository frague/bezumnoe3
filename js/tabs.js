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

//   // obj - object to be assigned as a.obj (Tab by default) 
//   AddSubmitButton(method, holder, obj) {
//     var m1 = document.createElement("div");
//     m1.className = "ConfirmButtons";
//     this.SubmitButton = utils.makeButton(method, "ok_button.gif", obj || this, "", "Сохранить изменения");
//     m1.appendChild(this.SubmitButton);
//     this[holder || "RelatedDiv"].appendChild(m1);
//   }

//   // Tab object reaction by outside call 
//   React(value) {
//     if (this.Reactor) {
//       this.Reactor.React(value);
//     }
//   }

//   // Sets additional className to RelatedDiv 
//   SetAdditionalClass(className) {
//     this.RelatedDiv.className = "TabContainer" + (className ? " " + className : "");
//   }
// }
export var Tab = React.createClass({
  propTypes: {
    name: React.PropTypes.string.isRequired,
    render: React.PropTypes.func.isRequired,
    activate: React.PropTypes.func
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
  getInitialState() {
    return {
      tabs: [],
      activeTab: null
    }
  },

  activate(tab) {
    this.setState({activeTab: tab});
  },

  add(name, render, switchToIt = true) {
    var tab = new Tab({name, render, activate: this.activate});
    this.state.tabs.push(tab);
    if (switchToIt) {
      this.activate(tab);
    }
  },

  render() {
    return <div className='tabs'>
      <FlexFrame key='tab-content' dimensions={[0, 0, 0, -20]}>
        {this.state.activeTab && this.state.activeTab.renderContent()}
      </FlexFrame>
      <ul className='tab-names'>
        {_.map(
          this.state.tabs, 
          (tab, index) => tab.render(tab.props.name === this.state.activeTab.props.name)
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
