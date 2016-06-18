//1.2
/*
	Contains all global script settings, constants and variables
*/

var debug = 0;

var d = document;
var w = top;
var voidLink = "javascript:void(0);";
var voidHref = "href=\"" + voidLink + "\"";

var imagesPath = "/3/img/";

/* Service methods */

function $(id) {
	if (d.all) {
		return d.all[id];
	} else if (d.getElementById) {
		return d.getElementById(id);
	} else if (d.layers) {
		return d.layers[id];
	}
	return false;
};