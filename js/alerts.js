import {utils} from './utils';
/*
    Displays messages on given container.
*/

class Alerts {
  constructor(container) {
    this.Holder = container;
    this.Holder.className = "AlertsHolder";
    this.Container = RoundCorners(container, "orange");
    this.Clear();
  }

  Add(message, isError) {
    utils.displayElement(this.Holder, true);
    this.Container.innerHTML += "<p class='" + (isError ? "Error" : "") + "'>" + message + "</p>";
    this.HasErrors = this.HasErrors || isError;
    this.IsEmpty = false;
  }

  Clear() {
    utils.displayElement(this.Holder, false);
    this.Container.innerHTML = "";
    this.HasErrors = false;
    this.IsEmpty = true;
  }
}
