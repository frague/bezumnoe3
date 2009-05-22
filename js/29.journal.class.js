//1.6
/*
	Journal functionality: Blog templates, messages, settings
*/

var MessagesSpoiler, TemplatesSpoiler, SettingsSpoiler;

function Journal() {
	this.Template = "journal";
};

Journal.prototype = new OptionsBase();

Journal.prototype.Request = function() {
};


function LoadAndBindJournalToTab(tab, user_id) {
	LoadAndBindObjectToTab(tab, user_id, new Journal(), "Journal", JournalOnLoad);
};

function JournalOnLoad(req, tab) {
	if (tab) {
		ObjectOnLoad(req, tab, "Journal");

		var tj = tab.Journal;
		tj.FindRelatedControls();
		tj.AssignTabTo("linkNewPost");

		var spoilers = tab.Journal.Inputs["Spoilers"];
		if (spoilers) {
			MessagesSpoiler = new Spoiler(1, "Сообщения", 0, 0, function(tab) {LoadAndBindJournalMessagesToTab(tab,me.Id,me.Login)});
			TemplatesSpoiler = new Spoiler(2, "Шаблоны отображения", 0, 0, function(tab) {LoadAndBindJournalTemplatesToTab(tab,me.Id)});
			SettingsSpoiler = new Spoiler(3, "Настройки", 0, 0, function(tab) {LoadAndBindJournalSettingsToTab(tab,me.Id)});
			
			MessagesSpoiler.ToString(spoilers);
			TemplatesSpoiler.ToString(spoilers);
			SettingsSpoiler.ToString(spoilers);
		}
		InitMCE();
	}
};

function InitMCE() {
	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		theme_advanced_resizing : true,
		language : "ru",
		plugins : "safari,pagebreak,style,advimage,advlink,inlinepopups,insertdatetime,preview,searchreplace,contextmenu,paste,directionality,visualchars,nonbreaking,xhtmlxtras,template",
		convert_urls : false,
		extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
		editor_selector : "Editable",

		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
		theme_advanced_buttons2 : "link,unlink,image,|,bullist,numlist,|,outdent,indent,blockquote,|,insertdate,inserttime|,sub,sup,|,charmap,|,cite,del,ins,hr,nonbreaking,pagebreak",
		theme_advanced_buttons3 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,visualchars,removeformat,|,code,preview",
		theme_advanced_toolbar_location : "bottom",
		theme_advanced_toolbar_align : "left",
		theme_advanced_resizing : true
	});
};