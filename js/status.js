/*
  Represents status entity on client-side.
*/

class Status {
  constructor(rights, title, color) {
    this.Rights = rights;
    this.Title = title;
    this.Color = color;
  }

  MakeCSS() {
    return "color:" + this.Color + ";";
  }

  CheckSum() {
    var cs = CheckSum(this.Rights);
    cs += CheckSum(this.Title);
    cs += CheckSum(this.Color);

    return cs;
  }
}