/*
  Spoiler class.
  Displays/hides logical piece of content on demand.
*/

class Spoiler extends TabBase {
  constructor(id, title, height, is_opened, on_select) {
    super();
    this.Id = id;
    this.Title = title;
    this.Height = height;
    this.IsOpened = is_opened ? 1 : 0;
    this.OnSelect = on_select;
    this.Locked = 0;
  }

  UpdateTitle() {
    this.TitleHolder.innerHTML = this.Title;
  }

  ToString(holder) {
    this.Holder = document.createElement("div");
    this.Holder.id = "Spoiler" + this.Id;

    let link = document.createElement("a");
    link.href = voidLink;
    link.className = "Title";
    link.onclick = () => {
      this.Switch();
      return false
    };
        
    this.TitleHolder = document.createElement("h4");
    this.UpdateTitle();
    link.appendChild(this.TitleHolder);
    this.Holder.appendChild(link);

    this.TopicDiv = document.createElement("div");
    this.Holder.appendChild(this.TopicDiv);

    this.RelatedDiv = document.createElement("div");
    this.RelatedDiv.className = "RelatedDiv";
    if (this.Height) {
      this.RelatedDiv.style.height = this.Height;
    }
    this.Holder.appendChild(this.RelatedDiv);

    holder.appendChild(this.Holder);
    this.Display(this.IsOpened);
  }

  Display(state) {
    if (this.Disabled) {
      return;
    }
    this.IsOpened = state;
    this.Holder.className = "Spoiler " + (this.IsOpened ? "Opened" : "Closed");
  }

  Switch() {
    this.Display(!this.IsOpened);
    if (this.OnSelect) {
      this.RelatedDiv.innerHTML = loadingIndicator;
      this.OnSelect(this);
      this.OnSelect = "";
    }
  }
}