import {OptionsBase} from './options';
import {Settings} from './user_settings';

/*
  Represents font settings
*/

export class Font extends OptionsBase {
  constructor(color, size, face, is_bold, is_italic, is_underlined) {
    super();

    this.Color = color;
    this.Size = size;
    this.Face = face;
    this.IsBold = is_bold;
    this.IsItalic = is_italic;
    this.IsUnderlined = is_underlined; 

    this.fields = ["FONT_COLOR", "FONT_SIZE", "FONT_FACE", "FONT_BOLD", "FONT_ITALIC", "FONT_UNDERLINED"];
    this.properties = ["Color", "Size", "Face", "IsBold", "IsItalic", "IsUnderlined"];
  }

  ToCSS(observer) {
    var s = (observer && observer.Settings) ? observer.Settings : new Settings('', 0, 0, 0, 0);

    var style = '';
    if (this.Color && !s.IgnoreColors) {
      style += "color:" + this.Color + ";";
    }
    if (this.Face && !s.IgnoreFonts) {
      style += "font-family:\"" + this.Face + "\";";
    }
    if (this.Size && !s.IgnoreSizes) {
      style += "font-size:" + (5 + 2 * this.Size) + "pt;";
    }
    if (!s.IgnoreStyles) {
      if (1 * this.IsBold) {
        style += "font-weight:bold;";
      }
      if (1 * this.IsItalic) {
        style += "font-style:italic;";
      }
      if (1 * this.IsUnderlined) {
        style += "text-decoration:underline;";
      }
    }
    return style;
  }

  ApplyTo(element) {
    if (element) {
      var result = (this.IsItalic ? "italic" : "normal") + " normal ";
      result += (this.IsBold ? "bold " : "normal ");
      result += (5 + 2 * this.Size) + "pt ";
      result += "'" + this.Face + "'";
      element.style.font = result;

      element.style.textDecoration = this.IsUnderlined ? "underline" : "none";
      element.style.color = this.Color;
    }
  }

  CheckSum() {
    var cs  = this.checkSum(this.Color);
    cs += this.checkSum(this.Size);
    cs += this.checkSum(this.Face);
    cs += this.checkSum(this.IsBold);
    cs += this.checkSum(this.IsItalic);
    cs += this.checkSum(this.IsUnderlined);
    return cs;
  }
}