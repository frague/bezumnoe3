/*
  Admin options
*/

class AdminOptions extends OptionsBase {
  constructor() {
    super();
    this.Template = "admin_options";
    this.ClassName = "AdminOptions";
  }

  Request() {}

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);
    this.FindRelatedControls();

    var spoilers = this.Inputs["Spoilers"];
    if (spoilers) {
      for (var i = 0, l = spoilerInits.length; i < l; i++) {
        var s = new Spoiler(i + 1, spoilerNames[i], 0, 0, spoilerInits[i]);
        s.ToString(spoilers);
      }
    }
  }
}