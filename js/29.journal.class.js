//5.0
/*
	Journal functionality: Blog templates, messages, settings
*/

var MessagesSpoiler, TemplatesSpoiler, SettingsSpoiler;

function Journal() {
	this.fields = [];
	this.ServicePath = servicesPath + "journal.service.php";
	this.Template = "journal";
	this.ClassName = "Journal";
};

Journal.prototype = new OptionsBase();

Journal.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind();
		obj.React();
	}
};

var items = [];

Journal.prototype.Bind = function() {
	var select = this.Inputs["FORUM_ID"];
	var showForums = this.Inputs["SHOW_FORUMS"].checked;
	var showJournals = this.Inputs["SHOW_JOURNALS"].checked;
	var showGalleries = this.Inputs["SHOW_GALLERIES"].checked;

	var type = '';

	if (select) {
		select.innerHTML = "";
		for (i = 0, l = this.data.length; i < l; i++) {
			var item = this.data[i];
			switch (item.TYPE) {
				case "f":
					if (!showForums) {
						continue;
					}
					break;
				case "g":
					if (!showGalleries) {
						continue;
					}
					break;
				case "j":
					if (!showJournals) {
						continue;
					}
					break;
			}
			item.ToString(i, this, select);
			items[item.FORUM_ID] = item;
		}
	}

	// Disallow empty value
	if (!select.value) {
		this.Inputs["SHOW_JOURNALS"].click();
		return;
	}
	// React on forum change on rebind
	if (this.FORUM_ID != select.value) {
		this.FORUM_ID = select.value;
		this.Tab.SetAdditionalClass(items[this.FORUM_ID].TYPE);
		this.React();
	}
};

Journal.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);

	this.FindRelatedControls();
	this.AssignTabTo("linkNewPost");
	this.GroupSelfAssign(["FORUM_ID", "SHOW_FORUMS", "SHOW_JOURNALS", "SHOW_GALLERIES"]);

	var spoilers = this.Inputs["Spoilers"];
	if (spoilers) {
		MessagesSpoiler = new Spoiler(1, "Сообщения", 0, 0, function(tab) {new JournalMessages().LoadTemplate(tab, me.Id, me.Login)});
		TemplatesSpoiler = new Spoiler(2, "Шаблоны отображения", 0, 0, function(tab) {new JournalTemplates().LoadTemplate(tab, me.Id)});
		SettingsSpoiler = new Spoiler(3, "Настройки", 0, 0, function(tab) {new JournalSettings().LoadTemplate(tab, me.Id)});
		AccessSpoiler = new Spoiler(4, "Доступ / дружественные журналы", 0, 0, function(tab) {new ForumAccess().LoadTemplate(tab, me.Id)});
			
		MessagesSpoiler.ToString(spoilers);
		TemplatesSpoiler.ToString(spoilers);
		SettingsSpoiler.ToString(spoilers);
		AccessSpoiler.ToString(spoilers);
	}
	InitMCE();
};

// Forum change handler
Journal.prototype.React = function() {
	this.FORUM_ID = this.Inputs["FORUM_ID"].value;
	if (this.FORUM_ID) {
		this.Tab.SetAdditionalClass(items[this.FORUM_ID].TYPE);
		var spoilers = [MessagesSpoiler, TemplatesSpoiler, SettingsSpoiler, AccessSpoiler];
		for (var i = 0, l = spoilers.length; i < l; i++) {
			var s = spoilers[i];
			s.FORUM_ID = this.FORUM_ID;
			s.React();
		}
	}
};

// tinyMCE initialization
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

/* Forum line DTO */

function fldto(forum_id, access, title, type, login) {
	this.fields = ["FORUM_ID", "ACCESS", "TITLE", "TYPE", "LOGIN"];
	this.Init(arguments);
};

fldto.prototype = new DTO();

fldto.prototype.ToString = function(index, obj, select) {
    var prefix = "[Форум] ";
	switch (this.TYPE) {
		case "g":
			prefix = "[Галерея] ";
			break;
		case "j":
			prefix = "[Журнал] ";
			break;
	}
	var selected = (this.FORUM_ID == obj.FORUM_ID);
	AddSelectOption(select, prefix + " \"" + this.TITLE + "\" " + " (" + this.LOGIN + ")", this.FORUM_ID, selected);
	return selected;
};
