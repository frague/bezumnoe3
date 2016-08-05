import {settings} from './settings';
import {Font} from './font';
import {OptionsBase} from './options';

/*
  Represents settings entity on client-side.
*/

export class Settings extends OptionsBase {
  constructor(status, ignore_colors, ignore_sizes, ignore_fonts, ignore_styles, receive_wakes, frameset, font) {
    super();
    this.Status = status;
    this.IgnoreColors = ignore_colors;
    this.IgnoreSizes = ignore_sizes;
    this.IgnoreFonts = ignore_fonts;
    this.IgnoreStyles = ignore_styles;
    this.ReceiveWakeups = receive_wakes;
    this.Frameset = frameset;

    this.Font = new Font(...font);

    this.fields = ["LOGIN", "STATUS", "IGNORE_COLORS", "IGNORE_FONT_SIZE", "IGNORE_FONTS", "IGNORE_FONT_STYLE", "RECEIVE_WAKEUPS", "FRAMESET", "ENTER_MESSAGE", "QUIT_MESSAGE", "FONT_COLOR", "FONT_SIZE", "FONT_FACE", "FONT_BOLD", "FONT_ITALIC", "FONT_UNDERLINED"];
    this.ServicePath = settings.servicesPath + "settings.service.php";
    this.Template = "usersettings";
    this.ClassName = "Settings";
  };

  CheckSum() {
    var cs  = this.checkSum(this.Status);
    cs += this.checkSum(this.IgnoreColors);
    cs += this.checkSum(this.IgnoreSizes);
    cs += this.checkSum(this.IgnoreFonts);
    cs += this.checkSum(this.IgnoreStyles);
    cs += this.checkSum(this.ReceiveWakeups);
    cs += this.checkSum(this.Frameset);

    if (this.Font && this.Font.CheckSum) {
      cs += this.Font.CheckSum();
    }
    return cs;
  }

  Bind() {
    this.BaseBind();
    UpdateFontView();
  }

  RequestCallback(req, obj) {
    if (obj) {
      obj.RequestBaseCallback(req, obj);
      obj.Bind();
    }
  }

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);
    this.AssignSelfTo("linkRefresh");

    // Create Font object
    font = new Font();
    font.Inputs = this.Inputs;
    setTimeout("UpdateFontView()", 1000);   // Set delay for IE

    // Init ColorPicker
  //  var cp = new ColorPicker("FONT_COLOR");
    new ColorPicker("FONT_COLOR");
    
    /* Submit button */
    this.Tab.AddSubmitButton("SaveObject(this)", "", this);
  }
}
/* Links actions */

// function RefreshSettings(a) {
//   if (a.Tab) {
//     a.Tab.Alerts.Clear();
//     a.Tab.Settings.Request();
//   }
// };

// var font;
// function UpdateFontView() {
//   if (font && font.Inputs) {
//     var el = font.Inputs["fontExample"];
//     if (el) {
//       font.Gather();
//       font.ApplyTo(el);
//     }
//   }
// };