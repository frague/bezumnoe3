/*
  Base class for those who loads template only (static text)
  with no user actions involved
*/

class StaticText extends OptionsBase {
  constructor() {
  	super();
  };
  Request() {};
  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);
  }
}