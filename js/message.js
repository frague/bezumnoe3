import {Tab} from './tabs';
import {dateHelper} from './date_helper';

export class Message {
  constructor(message_id, room_id, author_id, author_name, to_user_id, to_user, text, date) {
    [
      this.Id, this.roomId, this.authorId, this.authorName, 
      this.toId, this.toUserName, this.text, this.date
    ] = arguments;
    this.date = new Date(...this.date);
  }

  addName(e) {
    var name = e.target.innerText;
    console.log(name);
  }

  renderMessage() {
    var p = document.createElement('p');
    var time = document.createElement('span');
    time.innerHTML = dateHelper.time(this.date);
    p.appendChild(time);

    var hash = document.createElement('a');
    hash.innerHTML = '#';
    hash.onclick = () => chat.addRecepient(this.authorId, this.authorName);
    p.appendChild(hash);

    var name = document.createElement('a');
    name.innerHTML = this.authorName;
    name.onclick = this.addName;
    p.appendChild(name);

    p.appendChild(document.createTextNode(this.text));
    return p;
  }

  render(tabs) {
    var renewTabs = false;
    var tab = tabs.main;
    if (+this.toId > 0) { // TODO: Fix on backend to null
      tab = tabs.tabsCollection.Get(this.toId);
      if (!tab) {
        tab = new Tab(this.toId, this.toUserName, false, true);
        tabs.Add(tab);
        renewTabs = true;
      }
    }
    if (tab.RelatedDiv) {
      // tab.RelatedDiv.appendChild(this.renderMessage());  // To add last
      tab.RelatedDiv.insertBefore(this.renderMessage(), tab.RelatedDiv.firstChild); // To add first
      tab.lastMessageId = this.Id;
      if (tab.Id !== tabs.current.Id) {
        tab.UnreadMessages++;
        renewTabs = true;
      }
    }
    return renewTabs;
  }
}
