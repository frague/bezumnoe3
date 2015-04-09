//2.5
/*
    Represents font settings
*/


function Font(color, size, face, is_bold, is_italic, is_underlined) {
    this.Color = color;
    this.Size = size;
    this.Face = face;
    this.IsBold = is_bold;
    this.IsItalic = is_italic;
    this.IsUnderlined = is_underlined;

    this.fields = new Array("FONT_COLOR", "FONT_SIZE", "FONT_FACE", "FONT_BOLD", "FONT_ITALIC", "FONT_UNDERLINED");
    this.properties = new Array("Color", "Size", "Face", "IsBold", "IsItalic", "IsUnderlined");
};

Font.prototype = new OptionsBase();

// Methods

Font.prototype.ToCSS = function(observer) {
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
};

Font.prototype.ApplyTo = function(element) {
    if (element) {
        var result = (this.IsItalic ? "italic" : "normal") + " normal ";
        result += (this.IsBold ? "bold " : "normal ");
        result += (5 + 2 * this.Size) + "pt ";
        result += "'" + this.Face + "'";
        element.style.font = result;

        element.style.textDecoration = this.IsUnderlined ? "underline" : "none";
        element.style.color = this.Color;
    };
};

Font.prototype.CheckSum = function() {
    var cs = CheckSum(this.Color);
    cs+= CheckSum(this.Size);
    cs+= CheckSum(this.Face);
    cs+= CheckSum(this.IsBold);
    cs+= CheckSum(this.IsItalic);
    cs+= CheckSum(this.IsUnderlined);
    return cs;
};
