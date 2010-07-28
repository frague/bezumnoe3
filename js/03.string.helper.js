//1.6
/*
	Contains misc string-related functions.
*/

var chars = "àáâãäå¸æçèéêëìíîïğñòóôõö÷øùúûüışÿÀÁÂÃÄÅ¨ÆÇÈÉÊËÌÍÎÏĞÑÒÓÔÕÖ×ØÙÚÛÜİŞß";
var ascii = new Array();

for (var i = 0; i < chars.length; i++) {
    var ch = chars.charAt(i);
	ascii[ch] = i + 1;
};

function CheckSum(source) {
	source = "" + source;
    var sum = 0;
    if (source != "undefined" && source != "") {
	    source = " " + source;

	    var i;
    	if (source && source.length) {
    		var code;
			for (i = 1; i < source.length; i++) {
				code = ascii[source.charAt(i)];
				if (!code) {
					code = source.charCodeAt(i);
					if (code > 255) {
						code = 1;
					}
				}
				sum += code;
				//DebugLine(source.charAt(i) + " = " + code + " -> " + sum);
			}
		}
	}
	//DebugLine(source + " = " + sum + "<hr>");
	return sum;
};

function MakeParametersPair(name, value) {
	if (value == "undefined") {
		return "";
	}

	var param = encodeURIComponent(name);
	param += "=";
	param += encodeURIComponent(value);
	param += "&";
	return param;
};

function TwoDigits(d) {
	if (d > 9) {
		return d;
	}
	return "0" + d;
};

var tagsRegex = new RegExp("\<[\/]{0,1}[a-z]+[^\>]*\>", "ig");
function StripTags(text) {
	return text.replace(tagsRegex, "");
};

function HtmlQuotes(text) {
	return text.replace(/"/g, "&quot;");
};

function StrongHtmlQuotes(text) {
	text = text.replace(/&/g, "&amp;");
	text = HtmlQuotes(text);
	text = text.replace(/</g, "&lt;");
	text = text.replace(/>/g, "&gt;");
	text = text.replace(/"/g, "&quot;");
	return text;
};

function Quotes(text) {
//	return text.replace(/\"/g, "\\\"").replace(/'/g, "\\'");
	return text.replace(/'/g, "\\'").replace(/"/g, "&amp;quot;");
};

function Slash(text) {
	return text.replace(/(['"<>])/g, "\\$1");
};
