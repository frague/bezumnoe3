/*
    Recepient class.

*/
export class Recepient {
  constructor(id, title, is_locked) {
    this.Id = id;
    this.Title = title;
    this.IsLocked = is_locked;
  }

  ToString(index) {
    var s = (index ? ", " : "") + this.Title;
    if (!this.IsLocked) {
      s += "<a " + voidHref + " onclick='DR(" + this.Id + ")'>&times;</a>";
    }
    return s;
  }

  Gather(index) {
    return (index ? "," : "") + this.Id;
  }
}