import $ from 'jquery';
import {utils} from './utils';
import {Collection} from './collection';
/*
  Opens pop-up wakeup windows with messages.
*/

export class WakeupMessage {
  constructor(id, sender) {
    this.Id = id;
    this.Sender = sender;
  }

  render(container, index) {
    if (i == MaxShownWakeups) {
      container.appendChild(document.createTextNode(",  и ещё " + (ReceivedWakeups - MaxShownWakeups)));
      return;
    } else if (i > MaxShownWakeups) {
      return;
    }
    var a = document.createElement("a");
    a.innerHTML = this.Sender;
    a.wId = this.Id;
    a.onclick = () => ShowWakeup(this.wId, 1);
    if (index) {
      container.appendChild(document.createTextNode(",  "));
    }
    container.appendChild(a);
  }
};

export class WakeupsCollection extends Collection {
  constructor(userSettings, container) {
    super();
    this.userSettings = userSettings;
    this.container = container;
  }

  receive(id, sender) {
    if (!me) {
      return;
    }
    if (this.userSettings.ReceiveWakeups) {
      this.showWakeup(id);
    } else {
      this.Add(new WakeupMessage(id, sender));
    }
  }

  showWakeup(id, remove) {
    var wakeupWindow = window.open("wakeup.php?id=" + id, "wakeup" + id, "width=500,height=300,toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=1");
    if (remove) {
      this.Delete(id);
      this.printWakeups();
    }
  }

  render() {
    if (!this.container) {
      this.container = $("#Wakeups");
    }
    if (this.container) {
      if (this.Count()) {
        this.container.innerHTML = "<h3 class='wakeups'>Вейкапы  <span>(" + ReceivedWakeups + ")</span>:</h3>";
        this.render(this.container);
        utils.displayElement(this.container, true);
      } else {
        utils.displayElement(this.container, false);
      }
    }
  }
}

