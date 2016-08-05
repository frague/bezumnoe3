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
    var cs  = this.checkSum(this.Rights);
    cs += this.checkSum(this.Title);
    cs += this.checkSum(this.Color);

    return cs;
  }
}