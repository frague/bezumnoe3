import $ from 'jquery';
import {utils} from './utils';

/*
  Displays inline confirmation window
  blocking all content behind and handling callback.
*/

export class Confirm {
  constructor() {
    this.Cover = null;
    this.Holder = null;
    this.callbackObject = null;
    this.AlertType = null;
  }

  Init(coverId, holderId) {
    this.Cover = $(getElement(coverId))[0];
    this.Holder = $(getElement(holderId))[0];

    this.Holder.className = "ConfirmContainer";
  }

  Display(state) {
    utils.displayElement(this.Holder, state);
    utils.displayElement(this.Cover, state);
  }

  SetBodyOverflow(state) {
    var body = document.documentElement || document.body;
    body.style.overflow = state ? "hidden" : "auto";
  }

  Show(callback, title, message, customContent, keep_opened) {
    if (!this.Holder) {
      return false;
    } else {
      this.Holder.innerHTML = "";
    }

    this.SetBodyOverflow(0);

    title = title ? title : 'Confirmation';

    this.callbackObject = callback;
    this.KeepOpened = keep_opened;

    var h1 = document.createElement("h1");
    h1.innerHTML = title;
    this.Holder.appendChild(h1);

    var m = document.createElement("div");
    m.innerHTML = message;
    this.Holder.appendChild(m);

    if (customContent && customContent.CreateControls) {
      customContent.CreateControls(this.Holder);
      if (customContent.requestData) {
        customContent.requestData();
      }
    }

    var index = utils.checkEmpty(this.ButtonUrlIndex);
    var m1 = document.createElement("div");
    m1.className = "ConfirmButtons";
    if (this.AlertType) {
      m1.appendChild(utils.makeButton("ConfirmObject.Ok()", "ok_button"+index+".gif"));
    } else {
      m1.appendChild(utils.makeButton("ConfirmObject.Ok()", "yes_button"+index+".gif"));
      m1.appendChild(utils.makeButton("ConfirmObject.Cancel()", "no_button"+index+".gif"));
    }

    this.Holder.appendChild(m1);

    this.Display(true);
    window.ConfirmObject = this;

    return false;
  }

  Hide() {
    if (!this.Holder) {
      return;
    }
    this.Display(false);
    this.Holder.innerHTML = "";
    this.SetBodyOverflow(false);
  }

  Cancel() {
    if (!this.Holder) {
      return;
    }
    this.Hide();
    this.callbackObject = null;
  }

  Ok() {
    if (!this.Holder) {
      return;
    }
    if (!this.KeepOpened) {
      this.Hide();
    }
    if (this.callbackObject) {
      if (typeof(this.callbackObject) == 'function') {
        this.callbackObject();
      } else {
        if (this.callbackObject.click) {
          this.callbackObject.click();
        } else {
          location.href = this.callbackObject;
        }
      }
    }
  }
}