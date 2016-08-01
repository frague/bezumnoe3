/*
  Opens pop-up wakeup windows with messages.
*/

var wakeups = new Collection();
var WakeupsHolder;
var MaxShownWakeups = 10;
var ReceivedWakeups = 0;

class WakeupMessage {
  constructor(id, sender) {
    this.Id = id;
    this.Sender = sender;
  }

  ToString(holder, i) {
    if (i == MaxShownWakeups) {
      holder.appendChild(document.createTextNode(",  и ещё " + (ReceivedWakeups - MaxShownWakeups)));
      return;
    } else if (i > MaxShownWakeups) {
      return;
    }
    var a = document.createElement("a");
    a.innerHTML = this.Sender;
    a.wId = this.Id;
    a.onclick = () => {ShowWakeup(this.wId, 1)};
    if (i) {
      holder.appendChild(document.createTextNode(",  "));
    }
    holder.appendChild(a);
  }
}

function Wakeup(id, sender, reset) {
  if (!me) {
    return;
  }
  if (me.Settings.ReceiveWakeups) {
    ShowWakeup(id);
  } else {
    wakeups.Add(new WakeupMessage(id, sender));
  }
};

function ShowWakeup(id, remove) {
  var wakeupWindow = window.open("wakeup.php?id=" + id, "wakeup" + id, "width=500,height=300,toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=1");
  if (remove) {
    wakeups.Delete(id);
    PrintWakeups();
  }
};

function PrintWakeups() {
  if (!WakeupsHolder) {
    WakeupsHolder = $("#Wakeups");
  }
  if (WakeupsHolder) {
    ReceivedWakeups = wakeups.Count();
    if (ReceivedWakeups > 0) {
      WakeupsHolder.innerHTML = "<h3>Вейкапы  <span class='Count'>(" + ReceivedWakeups + ")</span>:</h3>";
      wakeups.ToString(WakeupsHolder);
      displayElement(WakeupsHolder, true);
    } else {
      displayElement(WakeupsHolder, false);
    }
  }
};
