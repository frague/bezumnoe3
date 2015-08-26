//6.0
/*
    Contains all global script settings, constants, variables and common methods
*/

var debug = 0;

var d = document;
var w = window;
var voidLink = "javascript:void(0);";
var voidHref = "href=\"" + voidLink + "\"";

var imagesPath = "/img/";
var servicesPath = "/services/";
var userPhotosPath = "/img/photos/";
var avatarsPath = "/img/avatars/";
var skinsPreviewPath = "/img/journals/";
var openIdPath = "/img/openid/";

var adminRights = 75;
var keeperRights = 20;
var topicRights = 10;

var loadingIndicator = "<div class='LoadingIndicator'></div>";

var severityCss = ["Warning", "Error"];

var replaceTagsExpr = new RegExp("\<[\/a-z][^\>]*\>", "gim");

var CurrentTab;
