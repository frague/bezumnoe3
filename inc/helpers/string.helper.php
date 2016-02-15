<?

    define(allowedTags, "<strike><pre><br><object><param><a><b><em><u><body><blockquote><center><div><font><h1><h2><h3><h4><h5><h6><head><html><hr><i><img><li><meta><ol><p><span><strong><style><table><tr><td><th><colgroup><col><ul><sub><sup>");

    $chars = "абвгдеёжзийклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ";

    $counting = array();
    $counting["пользователь"]       = "пользователя|пользователей";
    $counting["комментарий"]        = "комментария|комментариев";
    $counting["новый комментарий"]  = "новых комментария|новых комментариев";
    $counting["комната"]            = "комнаты|комнат";
    $counting["сообщение"]          = "сообщения|сообщений";
    $counting["новое"]              = "новых|новых";
    $counting["новое сообщение"]    = "новых сообщения|новых сообщений";
    $counting["ответ"]              = "ответа|ответов";
    $counting["фотография"]         = "фотографии|фотографий";
    $counting["тема"]               = "темы|тем";
    $counting["символ"]             = "символа|символов";
    $counting["запись"]             = "записи|записей";
    $counting["сообщение"]          = "сообщения|сообщений";

    function CheckSum($source) {
     global $chars, $debug;

        $s = 0;
        for ($i = 0; $i < mb_strlen($source); $i++) {
            $char = mb_substr($source, $i, 1);
            $pos = mb_strpos($chars, $char);
            if ($pos) {
                $s1 = $pos + 1;
            } else {
                $s1 = ord($char);
                if ($s1 > 127) {
                    $s1 = 1;
                }
            }
            $s += $s1;
//            DebugLine($char." = ".$s1." -> ".$s);
        }
//        DebugLine($source." = ".$s);
        return $s;
    }

    function MakeGuid($length) {
        $result = "";
        for ($i = 0; $i < $length; $i++) {
            if (($i + rand(0, 1))%2) {
                $result .= chr(65 + rand(0, 25));
            } else {
                $result .= chr(48 + rand(0, 9));
            }
        }
        return $result;
    }

    function Nullable($value) {
        return $value ? (
            substr($value, -1) == ")" ? SqlQuote($value) : "'".SqlQuote($value)."'"
            ) : "NULL";
    }

    function NullableId($value) {
        $value = round($value);
        return ($value && $value > 0) ? $value : "NULL";
    }

    function NullableIdExt($value) {
        $value = round($value);
        return ($value != 0 ? $value : "NULL");
    }

    function Boolean($value) {
        return $value ? 1 : 0;
    }

    function IdIsNull($id) {
        $id = round($id);
        return (!$id || $id < 0);
    }

    function SqlQuote($source) {
        return mysql_real_escape_string($source);
    }

    function SqlFnQuote($source) {
        $p = strpos($source, "fn:");
        if ($p !== false && $p == 0) {
            return str_replace("fn:", "", $source);
        }
        return "'".SqlQuote($source)."'";
    }

    function JsQuote($source) {
        $source = str_replace("\\", "\\\\", $source);
        $source = str_replace("'", "\\'", $source);
        $source = str_replace("\"", "\\\"", $source);
        $source = str_replace("\n", "\\n", $source);
        $source = str_replace("\r", "\\r", $source);
        return $source;
    }

    function StripTags($source, $all = true) {
        $source = strip_tags($source, ($all === true ? "" : constant("allowedTags")));
        return $source;
    }

    function HtmlQuote($source) {
        return MakeTagsPrintable($source);
    }

    function MakeTagsPrintable($source) {
        $source = str_replace("&", "&amp;", $source);
        $source = str_replace("<", "&lt;", $source);
        $source = str_replace(">", "&gt;", $source);
        return $source;
    }

    function MetaContent($source) {
        $source = StripTags($source);
        $source = str_replace("\n", " ", $source);
        $source = str_replace("\r", "", $source);
        $source = str_replace("\"", "&quot;", $source);
        $source = str_replace(">", "&gt;", $source);
        $source = str_replace("<", "&lt;", $source);
        $source = preg_replace("/\s{2,}/", " ", $source);
        return $source;
    }

    function GetNonEmpty($a, $b) {
        if ($a) {
            return $a;
        }
        return $b;
    }

    function UTF8toWin1251($s) {
        return $s;

        $out = "";
        $c1 = "";
        $byte2 = false;
        for ($c = 0; $c < strlen($s); $c++){
            $i = ord($s[$c]);
            if ($i <= 127) {
                $out .= $s[$c];
            }
            if ($byte2) {
                $new_c2 = ($c1 & 3) * 64 + ($i & 63);
                $new_c1 = ($c1 >> 2) & 5;
                $new_i = $new_c1 * 256 + $new_c2;
                if ($new_i == 1025) {
                    $out_i=168;
                } else {
                    if ($new_i == 1105) {
                        $out_i = 184;
                    } else {
                        $out_i = $new_i - 848;
                    }
                }
                $out .= chr($out_i);
                $byte2 = false;
            }
            if (($i >> 5) == 6) {
                $c1 = $i;
                $byte2 = true;
            }
        }
        return $out;
    }

    function Error($msg, $toPrint = true) {
        $s = "<div class='Error'>".$msg."</div>\n";
        if ($toPrint) {
            echo $s;
        }
        return $s;
    }

    function Encode($string) {
        return md5($string);
    }

    /* -------------------- Making Links -------------------- */

    function MakeLink($a, $used = 0, $imageMode = false) {
        if ($used) {
            return str_replace("\\", "", $used).$a;
        }

        $atPosition = strpos($a, "@");
        if ($atPosition) {
            $a = "<a class=Link href='javascript:void(0)' onclick='window.location=\"mailto:".substr($a, 0, $atPosition)."\"+\"@\"+\"".substr($a, $atPosition + 1, strlen($a))."\"'>".str_replace("@", "&#64;", $a)."</a>";
        } else if (ereg("^((ftp|http|shttp|https)://|www\.)", $a)) {
            $ext = "";
            if (ereg(".([^.]+)$", $a, $m)) {
                $ext = $m[1];
            }

            $a1 = $a;
            if (ereg("^www\.", $a)) {
                $a1 = "http://".$a;
            }

            if (preg_match("/^(gif|jpg|bmp|png|jpeg)$/i",$ext)) {
                if ($imageMode) {
                    $filename = substr($a1, 1 + strrpos($a1, "/"));
                    $a = "<span class='imageLink'><a href='".$a1."' target='_image'>".$filename."</a></span>";
                } else {
                    $a = "<center><img src='".$a1."' alt='".$a1."' hspace=5 vspace=5 border=1></center>";
                }
            } else {
                $a = "<a class=Link href='".$a1."' target='_blank'>".$a."</a>";
            }
        }
        return $a;
    }

    function MakeUrls($source) {
        return preg_replace("/(=?\'?\"?)([a-z0-9_\.()~\-]+@[a-z0-9\-]+(\.[a-z0-9~\-_\!]+)+)/ie","MakeLink('\\2','\\1')", $source);
    }

    function MakeLinks($a, $imagesMode = false) {
        $a = MakeUrls($a);
        $a = preg_replace("/((image:url\()?=?\'?\"?)((ftp:\/\/|http:\/\/|shttp:\/\/|https:\/\/)[a-z0-9_\.\(\)~\-]+(\.[a-z0-9~\-\/%#@&\+_\?=\[\]:;,]+)+)/ie","MakeLink('\\3','\\1',\$imagesMode)", $a);
        return $a;
    }

    function OuterLink($url, $proto, $btw) {
        $allowedHosts = array("bezumnoe.ru", "www.bezumnoe.ru");
        for ($i = 0; $i < sizeof($allowedHosts); $i++) {
            if (eregi(str_replace(".", "\.", $allowedHosts[$i]), $url)) {
                return "<a ".$btw." href='".$proto."://".$url."'";
            }
        }

        $url = rawurlencode($url);
        return "<a ".$btw." rel='nofollow' href='/go/?url=".$url."&proto=".$proto."'";
    }

    function OuterLinks($a) {
        $linkExp = "/<a ([^>]*)href=[\'\"]{0,1}((http|shttp|ftp|https)):\/\/([^\ \'\">]+)[\'\"]?/ie";
        $a = preg_replace($linkExp, "OuterLink(\"$4\",\"$2\", \"$1\")", $a);
        return $a;
    }

    /* -------------------- Making Links -------------------- */

    function DebugLine($text) {
      global $debug;

        if ($debug) {
            echo("DebugLine(\"Server: ".JsQuote($text)."\");");
        }
    }

    /* -------------------- Search Helper ------------------- */

    function ValidateSearchRequest($request) {
        return substr(trim($request), 0, 1024);
    }

    function MakeSqlSearchRequest($request, $template) {
        $words = split(" ", ValidateSearchRequest($request));
        $result = "";
        for ($i = 0; $i < sizeof($words); $i++) {
            if ($i == 1) {
                $result = "(".$result.")";
            }
            $result .= ($i ? " OR (" : "").str_replace("#WORD#", $words[$i], $template).($i ? ")" : "");
        }
        return $result;
    }

    /* --------------------- Making quotes ------------------- */

    function UpdateQuotation($fromLevel, $toLevel) {
        $resul = "";
        for ($i = 0; $i < abs($fromLevel - $toLevel); $i++) {
            $result .= ($fromLevel > $toLevel) ? "</cite>" : "<cite>";
        }
        return $result;
    }

    function MakeTextQuotes($text) {
        $result = "";
        $lastLevel = 0;
        $text = str_replace("\r", "", $text);

        $lines = split("\n", $text);
        for ($i = 0; $i < sizeof($lines); $i++) {
            $line = $lines[$i];
            if (preg_match("/^((&gt;|>)+)/", $line, $matches)) {
                $match = $matches[0];
                $line = substr($line, strlen($match));

                $quoteLevel = substr_count(str_replace("&gt;", ">", $match), ">");
            } else {
                $quoteLevel = 0;
            }

            if (trim($line)) {
                $result .= UpdateQuotation($lastLevel, $quoteLevel).$line."<br />\n";
                $lastLevel = $quoteLevel;
            }
        }
        $result .= UpdateQuotation($lastLevel, 0)."<br />";
        return $result;
    }

    /* ---------------------------------------------------------- */

    function TrimBy($text, $by) {
        if (strlen($text) <= $by) {
            return $text;
        }

        return mb_substr($text, 0, $by)."...";
    }

    function Countable($word, $amount, $null = "0") {
      global $counting;

        $variants = $counting[$word];
        $result = $word;
        if ($variants) {
            list($two, $many) = split("\|", $variants);
            $lastDigit = round(substr($amount, -1));
            $last2Digits = round(substr($amount, -2));

            $result = $many;
            if ($lastDigit == 1 && $last2Digits != 11) {$result = $word;}
            if ($lastDigit >= 2 && $lastDigit <= 4 && ($last2Digits < 12 || $last2Digits > 14)) {$result = $two;}
        }
        return ($amount ? $amount : $null)." ".$result;
    }

    function MakeKeywordSearch($text, $pattern) {
        if (!$text || !$pattern) {
            return "";
        }
        $words = split("[   ]", $text);
        $result = "";
        for ($i = 0; $i < sizeof($words); $i++) {
            $word = $words[$i];
            if ($word) {
                $result .= ($result ? " AND " : "").str_replace("#WORD#", SqlQuote($word), $pattern);
            }
        }
        return $result ? "(".$result.")" : $result;
    }

    function Mark($haystack, $needle) {
        if (!$needle) {
            return $haystack;
        }
        $needle = mb_strtolower($needle);

        $p = strpos(mb_strtolower($haystack), $needle);
        if ($p === false) {
            return $haystack;
        }
        $l = strlen($needle);
        $p2 = $p + $l;
        return substr($haystack, 0, $p)."<b>".substr($haystack, $p, $l)."</b>".substr($haystack, $p2);
    }

    function Smartnl2br($text) {
        return preg_replace("/([^\>])(\n|\r)([^\<])/", "$1<br />$2$3", $text);
//      return preg_replace("/([^\>]|[^p]\>|[^\/]p\>|[^\<]\/p\>)(\n|\r)/", "$1<br />$2", $text);
    }

    function MakeSearchCriteria($dateKey, $dateParameter, $keywordsKey, $keywordsTemplate) {
        $condition = "";

        // Dates condition
        $d = $_POST[$dateKey];
        if ($d) {
            $t = ParseDate($d);
            if ($t !== false) {
                $condition = "t1.".$dateParameter." LIKE '".DateFromTime($t, "Y-m-d")."%' ";
            }
        }

        // Search keywords
        $keywords = trim(substr(UTF8toWin1251($_POST[$keywordsKey]), 0, 1024));
        $search = MakeKeywordSearch($keywords, $keywordsTemplate);
        if ($search) {
            $condition .= ($condition ? " AND " : "").$search;
        }
        return $condition;
    }

    function Clickable($name) {
        return "<a href=\"javascript:void(0)\" onclick=\"__(this)\">".$name."</a>";
    }

    function MakeListItem($className = "") {
        $i = round(rand(0,4));
        return "<li".($i ? " class=\"l".$i.($className ? " ".$className : "")."\"" : ($className ? " class=\"".$className."\"" : "")).">";
    }

?>
