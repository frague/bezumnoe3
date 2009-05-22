//2.0
/*
	Opens pop-up wakeup windows with messages.
*/


var wakeups = new Collection();
var WakeupsHolder;
var MaxShownWakeups = 10;
var ReceivedWakeups = 0;

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
		WakeupsHolder = $("Wakeups");
	}
	if (WakeupsHolder) {
		ReceivedWakeups = wakeups.Count();
		if (ReceivedWakeups > 0) {
			WakeupsHolder.innerHTML = "<h3>Вейкапы	<span class='Count'>(" + ReceivedWakeups + ")</span>:</h3>";
			wakeups.ToString(WakeupsHolder);
			DisplayElement(WakeupsHolder, true);
		} else {
			DisplayElement(WakeupsHolder, false);
		}
	}
};

function WakeupMessage(id, sender) {
	this.Id = id;
	this.Sender = sender;
};

WakeupMessage.prototype.ToString = function(holder, i) {
	if (i == MaxShownWakeups) {
		holder.appendChild(d.createTextNode(",	и ещё " + (ReceivedWakeups - MaxShownWakeups)));
		return;
	} else if (i > MaxShownWakeups) {
		return;
	}
	var a = d.createElement("a");
	a.innerHTML = this.Sender;
	a.href = voidLink;
	a.wId = this.Id;
	a.onclick = function(){ShowWakeup(this.wId,1)};
	if (i) {
		holder.appendChild(d.createTextNode(",	"));
	}
	holder.appendChild(a);
};

