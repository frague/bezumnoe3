<?php

class Settings extends EntityBase {
    // Constants
    const table = "settings";

    const SETTINGS_ID = "SETTINGS_ID";
    const USER_ID = "USER_ID";
    const ENTER_MESSAGE = "ENTER_MESSAGE";
    const QUIT_MESSAGE = "QUIT_MESSAGE";
    const STATUS = "STATUS";
    const FONT_COLOR = "FONT_COLOR";
    const FONT_SIZE = "FONT_SIZE";
    const FONT_FACE = "FONT_FACE";
    const FONT_BOLD = "FONT_BOLD";
    const FONT_ITALIC = "FONT_ITALIC";
    const FONT_UNDERLINED = "FONT_UNDERLINED";
    const IGNORE_FONTS = "IGNORE_FONTS";
    const IGNORE_COLORS = "IGNORE_COLORS";
    const IGNORE_FONT_SIZE = "IGNORE_FONT_SIZE";
    const IGNORE_FONT_STYLE = "IGNORE_FONT_STYLE";
    const RECEIVE_WAKEUPS = "RECEIVE_WAKEUPS";
    const CONFIRM_PRIVATES = "CONFIRM_PRIVATES";
    const FRAMESET = "FRAMESET";

    const IS_IGNORED = "IS_IGNORED";
    const IGNORES_YOU = "IGNORES_YOU";

    // Properties
    var $Id;
    var $UserId;
    var $EnterMessage;
    var $QuitMessage;
    var $Status;
    var $FontColor;
    var $FontSize;
    var $FontFace;
    var $FontIsBold;
    var $FontIsItalic;
    var $FontIsUnderlined;
    var $IgnoreFonts;
    var $IgnoreColors;
    var $IgnoreSizes;
    var $IgnoreStyles;
    var $ReceiveWakeups;
    var $ConfirmPrivates;
    var $Frameset;
    var $IsIgnored;
    var $IgnoresYou;

    // Fields
    var $db = 0;

    function Settings($id = -1) {
        $this->table = self::table;
        parent::__construct($id, self::SETTINGS_ID);
    }

    function Clear() {
        $this->Id = -1;
        $this->UserId = -1;
        $this->EnterMessage = "";
        $this->QuitMessage = "";
        $this->Status = "";
        $this->FontColor = "White";
        $this->FontSize = "3";
        $this->FontFace = "";
        $this->FontIsBold = false;
        $this->FontIsItalic = false;
        $this->FontIsUnderlined = false;
        $this->IgnoreFonts = false;
        $this->IgnoreColors = false;
        $this->IgnoreSizes = false;
        $this->IgnoreStyles = false;
        $this->ReceiveWakeups = true;
        $this->ConfirmPrivates = true;
        $this->Frameset = 0;
        $this->IsIgnored = false;
        $this->IgnoresYou = false;
    }

    function CheckSum($extended = false) {
/*

-	var $Id;
-	var $UserId;
-	var $EnterMessage;
-	var $QuitMessage;
+	var $Status;
+	var $IgnoreFonts;
+	var $IgnoreColors;
+	var $IgnoreSizes;
+	var $IgnoreStyles;
+	var $ReceiveWakeups;
-	var $ConfirmPrivates;
-	var $Refresh;

*/
        $cs = 0;
        $cs += CheckSum($this->Status);
        $cs += CheckSum(Boolean($this->IgnoreColors));
        $cs += CheckSum(Boolean($this->IgnoreSizes));
        $cs += CheckSum(Boolean($this->IgnoreFonts));
        $cs += CheckSum(Boolean($this->IgnoreStyles));
        $cs += CheckSum(Boolean($this->ReceiveWakeups));
        $cs += CheckSum($this->Frameset);
        $cs += $this->FontCheckSum();
        DebugLine("Settings sum: ".$cs);
        return $cs;
    }

    function FontCheckSum() {
/*

+	var $FontColor;
+	var $FontSize;
+	var $FontFace;
+	var $FontIsBold;
+	var $FontIsItalic;
+	var $FontIsUnderlined;

*/
        $cs = 0;
        $cs += CheckSum($this->FontColor);
        $cs += CheckSum($this->FontSize);
        $cs += CheckSum($this->FontFace);
        $cs += CheckSum(Boolean($this->FontIsBold));
        $cs += CheckSum(Boolean($this->FontIsItalic));
        $cs += CheckSum(Boolean($this->FontIsUnderlined));
        return $cs;
    }

    function FillFromResult($result) {
        $this->Id = $result->Get(self::SETTINGS_ID);
        $this->UserId = $result->Get(self::USER_ID);

        $this->EnterMessage = $result->Get(self::ENTER_MESSAGE);
        $this->QuitMessage = $result->Get(self::QUIT_MESSAGE);
        $this->Status = $result->Get(self::STATUS);
        $this->FontColor = $result->Get(self::FONT_COLOR);
        $this->FontSize = $result->Get(self::FONT_SIZE);
        $this->FontFace = $result->Get(self::FONT_FACE);
        $this->FontIsBold = $result->Get(self::FONT_BOLD) > 0;
        $this->FontIsItalic = $result->Get(self::FONT_ITALIC) > 0;
        $this->FontIsUnderlined = $result->Get(self::FONT_UNDERLINED) > 0;
        $this->IgnoreFonts = $result->Get(self::IGNORE_FONTS) > 0;
        $this->IgnoreColors = $result->Get(self::IGNORE_COLORS) > 0;
        $this->IgnoreSizes = $result->Get(self::IGNORE_FONT_SIZE) > 0;
        $this->IgnoreStyles = $result->Get(self::IGNORE_FONT_STYLE) > 0;
        $this->ReceiveWakeups = $result->Get(self::RECEIVE_WAKEUPS) > 0;
        $this->ConfirmPrivates = $result->Get(self::CONFIRM_PRIVATES) > 0;
        $this->Frameset = $result->Get(self::FRAMESET);
        $this->IsIgnored = $result->Get(self::IS_IGNORED) > 0;
        $this->IgnoresYou = $result->Get(self::IGNORES_YOU) > 0;
    }

    function FillFromHash($hash) {
        $this->EnterMessage = UTF8toWin1251($hash[self::ENTER_MESSAGE]);
        $this->QuitMessage = UTF8toWin1251($hash[self::QUIT_MESSAGE]);
        $this->Status = UTF8toWin1251($hash[self::STATUS]);
        $this->FontColor = $hash[self::FONT_COLOR];
        $this->FontSize = $hash[self::FONT_SIZE];
        $this->FontFace = $hash[self::FONT_FACE];
        $this->FontIsBold = $hash[self::FONT_BOLD] > 0;
        $this->FontIsItalic = $hash[self::FONT_ITALIC] > 0;
        $this->FontIsUnderlined = $hash[self::FONT_UNDERLINED] > 0;
        $this->IgnoreFonts = $hash[self::IGNORE_FONTS] > 0;
        $this->IgnoreColors = $hash[self::IGNORE_COLORS] > 0;
        $this->IgnoreSizes = $hash[self::IGNORE_FONT_SIZE] > 0;
        $this->IgnoreStyles = $hash[self::IGNORE_FONT_STYLE] > 0;
        $this->ReceiveWakeups = $hash[self::RECEIVE_WAKEUPS] > 0;
        $this->Frameset = round($hash[self::FRAMESET]);
    }

    function Validate() {
        $result = "";
        if (!preg_match("/^[a-zA-Z0-9_ \-\.]*$/", $this->FontFace)) {
            $result .= "<li> Недопустимое название шрифта (возможные варианты: verdana, arial, tahoma и др.)";
        }
        if ($this->EnterMessage && !preg_match("/\%name/", $this->EnterMessage)) {
            $result .= "<li> Фраза о входе в чат не содержит ссылки на имя (%name)";
        }
        if ($this->QuitMessage && !preg_match("/\%name/", $this->QuitMessage)) {
            $result .= "<li> Фраза о выходе из чата не содержит ссылки на имя (%name)";
        }
        return $result;
    }

    function GetByUserId($id) {
        return $this->FillByCondition("t1.".self::USER_ID."=".SqlQuote($id));
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li>".self::SETTINGS_ID.": ".$this->Id."</li>\n";
        $s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
        $s.= "<li>".self::ENTER_MESSAGE.": ".$this->EnterMessage."</li>\n";
        $s.= "<li>".self::QUIT_MESSAGE.": ".$this->QuitMessage."</li>\n";
        $s.= "<li>".self::STATUS.": ".$this->Status."</li>\n";
        $s.= "<li>".self::FONT_COLOR.": ".$this->FontColor."</li>\n";
        $s.= "<li>".self::FONT_SIZE.": ".$this->FontSize."</li>\n";
        $s.= "<li>".self::FONT_FACE.": ".$this->FontFace."</li>\n";
        $s.= "<li>".self::FONT_BOLD.": ".$this->FontIsBold."</li>\n";
        $s.= "<li>".self::FONT_ITALIC.": ".$this->FontIsItalic."</li>\n";
        $s.= "<li>".self::FONT_UNDERLINED.": ".$this->FontIsUnderlined."</li>\n";
        $s.= "<li>".self::IGNORE_FONTS.": ".$this->IgnoreFonts."</li>\n";
        $s.= "<li>".self::IGNORE_COLORS.": ".$this->IgnoreColors."</li>\n";
        $s.= "<li>".self::IGNORE_FONT_SIZE.": ".$this->IgnoreSizes."</li>\n";
        $s.= "<li>".self::IGNORE_FONT_STYLE.": ".$this->IgnoreStyles."</li>\n";
        $s.= "<li>".self::RECEIVE_WAKEUPS.": ".$this->ReceiveWakeups."</li>\n";
        $s.= "<li>".self::CONFIRM_PRIVATES.": ".$this->ConfirmPrivates."</li>\n";
        $s.= "<li>".self::FRAMESET.": ".$this->Frameset."</li>\n";
        $s.= "<li>".self::IS_IGNORED.": ".$this->IsIgnored."</li>\n";
        $s.= "<li>".self::IGNORES_YOU.": ".$this->IgnoresYou."</li>\n";
        $s.= "<li>Checksum: ".$this->CheckSum()."\n";
        $s.= "<li style=\"".$this->ToCSS()."\">Font example</li>\n";

        if ($this->IsEmpty()) {
            $s.= "<li> <b>Settings are not saved!</b>";
        }

        $s.= "</ul>";
        return $s;
    }

    function ToCSS() {
        $s = "";
        if ($this->FontFace) {$s .= "font-family:'".$this->FontFace."';";}
        if ($this->FontColor) {$s .= "color:".(preg_match("/^[a-f0-9]{1,6}$/i", $this->FontColor) ? "#" : "").$this->FontColor.";";}
        if ($this->FontSize) {$s .= "font-size:".(8 + $this->FontSize)."pt;";}
        if ($this->FontIsBold) {$s .= "font-weight:bold;";}
        if ($this->FontIsItalic) {$s .= "font-style:italic;";}
        if ($this->FontIsUnderlined) {$s .= "text-decoration:underline;";}
        return $s;
    }

    function FontToJs() {
        return "new Font(\"".JsQuote($this->FontColor)."\",\"".JsQuote($this->FontSize)."\",\"".JsQuote($this->FontFace)."\",".Boolean($this->FontIsBold).",".Boolean($this->FontIsItalic).",".Boolean($this->FontIsUnderlined).")";
    }

    function FontToJSON() {
        return array(
            "color" => $this->FontColor,
            "size" => $this->FontSize,
            "face" => $this->FontFace,
            "bold" => Boolean($this->FontIsBold),
            "italic" => Boolean($this->FontIsItalic),
            "underlined" => Boolean($this->FontIsUnderlined)
        );
    }

    function ToJs() {
        return "new Settings(\"".JsQuote($this->Status)."\",".Boolean($this->IgnoreColors).",".Boolean($this->IgnoreSizes).",".Boolean($this->IgnoreFonts).",".Boolean($this->IgnoreStyles).",".Boolean($this->ReceiveWakeups).",".round($this->Frameset).",".$this->FontToJs().")";
    }

    function ToJSON() {
        return array(
            "status" => $this->Status,
            "ignore_colors" => Boolean($this->IgnoreColors),
            "ignore_sizes" => Boolean($this->IgnoreSizes),
            "ignore_fonts" => Boolean($this->IgnoreFonts),
            "ignore_styles" => Boolean($this->IgnoreStyles),
            "open_wakeups" => Boolean($this->ReceiveWakeups),
            "frameset" => round($this->Frameset),
            "font" => $this->FontToJSON()
        );
    }

    function ToJsProperties($user) {
        if (!$user || $user->IsEmpty()) {
            return;
        }
        return "new Array(\"".
JsQuote($user->Login)."\",\"".
JsQuote($this->Status)."\",".
Boolean($this->IgnoreColors).",".
Boolean($this->IgnoreSizes).",".
Boolean($this->IgnoreFonts).",".
Boolean($this->IgnoreStyles).",".
Boolean($this->ReceiveWakeups).",".
round($this->Frameset).",\"".
JsQuote($this->EnterMessage)."\",\"".
JsQuote($this->QuitMessage)."\",\"".
JsQuote($this->FontColor)."\",\"".
JsQuote($this->FontSize)."\",\"".
JsQuote($this->FontFace)."\",".
Boolean($this->FontIsBold).",".
Boolean($this->FontIsItalic).",".
Boolean($this->FontIsUnderlined).")";
    }

    // SQL
    function ReadExpression() {
        return "SELECT
    t1.".SETTINGS_ID.",
    t1.".self::USER_ID.",
    t1.".self::ENTER_MESSAGE.",
    t1.".self::QUIT_MESSAGE.",
    t1.".self::STATUS.",
    t1.".self::FONT_COLOR.",
    t1.".self::FONT_SIZE.",
    t1.".self::FONT_FACE.",
    t1.".self::FONT_BOLD.",
    t1.".self::FONT_ITALIC.",
    t1.".self::FONT_UNDERLINED.",
    t1.".self::IGNORE_FONTS.",
    t1.".self::IGNORE_COLORS.",
    t1.".self::IGNORE_FONT_SIZE.",
    t1.".self::IGNORE_FONT_STYLE.",
    t1.".self::RECEIVE_WAKEUPS.",
    t1.".self::CONFIRM_PRIVATES.",
    t1.".self::FRAMESET.",
    0 AS ".self::IS_IGNORED.",
    0 AS ".self::IGNORES_YOU."
FROM
    ".$this->table." AS t1
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table."
(".self::USER_ID.",
".self::ENTER_MESSAGE.",
".self::QUIT_MESSAGE.",
".self::STATUS.",
".self::FONT_COLOR.",
".self::FONT_SIZE.",
".self::FONT_FACE.",
".self::FONT_BOLD.",
".self::FONT_ITALIC.",
".self::FONT_UNDERLINED.",
".self::IGNORE_FONTS.",
".self::IGNORE_COLORS.",
".self::IGNORE_FONT_SIZE.",
".self::IGNORE_FONT_STYLE.",
".self::RECEIVE_WAKEUPS.",
".self::CONFIRM_PRIVATES.",
".self::FRAMESET."
)
VALUES
('".SqlQuote($this->UserId)."',
'".SqlQuote($this->EnterMessage)."',
'".SqlQuote($this->QuitMessage)."',
'".SqlQuote($this->Status)."',
'".SqlQuote($this->FontColor)."',
'".SqlQuote($this->FontSize)."',
'".SqlQuote($this->FontFace)."',
'".Boolean($this->FontIsBold)."',
'".Boolean($this->FontIsItalic)."',
'".Boolean($this->FontIsUnderlined)."',
'".Boolean($this->IgnoreFonts)."',
'".Boolean($this->IgnoreColors)."',
'".Boolean($this->IgnoreSizes)."',
'".Boolean($this->IgnoreStyles)."',
'".Boolean($this->ReceiveWakeups)."',
'".Boolean($this->ConfirmPrivates)."',
'".SqlQuote($this->Frameset)."'
)";
    }

    function UpdateExpression() {
        $result = "UPDATE ".$this->table." SET
".self::USER_ID."='".SqlQuote($this->UserId)."',
".self::ENTER_MESSAGE."='".SqlQuote($this->EnterMessage)."',
".self::QUIT_MESSAGE."='".SqlQuote($this->QuitMessage)."',
".self::STATUS."='".SqlQuote($this->Status)."',
".self::FONT_COLOR."='".SqlQuote($this->FontColor)."',
".self::FONT_SIZE."='".SqlQuote($this->FontSize)."',
".self::FONT_FACE."='".SqlQuote($this->FontFace)."',
".self::FONT_BOLD."='".Boolean($this->FontIsBold)."',
".self::FONT_ITALIC."='".Boolean($this->FontIsItalic)."',
".self::FONT_UNDERLINED."='".Boolean($this->FontIsUnderlined)."',
".self::IGNORE_FONTS."='".Boolean($this->IgnoreFonts)."',
".self::IGNORE_COLORS."='".Boolean($this->IgnoreColors)."',
".self::IGNORE_FONT_SIZE."='".Boolean($this->IgnoreSizes)."',
".self::IGNORE_FONT_STYLE."='".Boolean($this->IgnoreStyles)."',
".self::RECEIVE_WAKEUPS."='".Boolean($this->ReceiveWakeups)."',
".self::CONFIRM_PRIVATES."='".Boolean($this->ConfirmPrivates)."',
".self::FRAMESET."='".SqlQuote($this->Frameset)."'
WHERE
    ".self::SETTINGS_ID."=".SqlQuote($this->Id);
        return $result;
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table." WHERE ".self::SETTINGS_ID."=".SqlQuote($this->Id);
    }
}

?>
