/*
  Sliding panel class
*/

var panels = [];

class Panel {
  constructor() {
    this.IsOpened = 1;
    this.Step = 20;
    this.MinPosition = 10;

    this.BaseLinkClass = "Panel";
  }

  Init(id, size) {
    this.Id = id;
    this.Holder = $(GetElement(id))[0];
    this.Position = size;
    this.CurrentSize = size;

    this.CreateLink();

    if (this.Holder) {
      this.Resize(this.Position);
      if (this.SwitchLink) {
        this.SwitchLink.onclick = () => Slide(this);
        this.LinkSwitcher();
      };
      panels[id] = this;
    }
  }

  CreateLink() {
    this.SwitchLink = document.createElement("a");
    this.SwitchLink.onfocus = () => this.blur;
    if (this.Holder.hasChildNodes()) {
      this.Holder.insertBefore(this.SwitchLink, this.Holder.firstChild);
    } else {
      this.Holder.appendChild(this.SwitchLink);
    }
  }

  LinkSwitcher() {
    this.SwitchLink.className = this.BaseLinkClass + " " + (this.IsOpened ? "Opened" : "Closed");
    if (AdjustDivs) {
      AdjustDivs();
    }
  }

  ResizeBy(to) {
    var result = true;
    var size = this.CurrentSize + to;

    if (size < this.MinPosition) {
      size = this.MinPosition;
      this.IsOpened = false;
      result = false;
    };

    if (size > this.Position) {
      size = this.Position;
      this.IsOpened = true;
      result = false;
    };

    if (!result) {
      this.LinkSwitcher();
    };

    this.Resize(size);
    return result;
  }

  Resize(size) {}
};

function Slide(panel) {
  if (panel.ResizeBy(panel.IsOpened ? -panel.Step : panel.Step)) {
    setTimeout(function() {Slide(panel)}, 1);
  }
};
