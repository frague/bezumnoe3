//6.7
/*
	Journal functionality: Blog templates, messages, settings
*/

// Warning! Matrix should correspond to standard accesses
var accessMatrix = [{f: [0, 0, 0, 0], g: [0, 0, 0, 0], j: [0, 0, 0, 0]}, {f: [1, 0, 0, 0], g: [1, 0, 0, 0], j: [1, 0, 0, 0]}, {f: [1, 0, 0, 0], g: [1, 0, 0, 0], j: [1, 0, 0, 0]}, {f: [1, 0, 0, 0], g: [1, 0, 0, 0], j: [1, 0, 0, 0]}, {f: [1, 0, 1, 1], g: [1, 0, 1, 0], j: [1, 1, 1, 1]}];

var MessagesSpoiler, TemplatesSpoiler, SettingsSpoiler;

function Journal() {
	this.fields = [];
	this.ServicePath = servicesPath + "journal.service.php";
	this.Template = "journal";
	this.ClassName = "Journal";
	this.Forum = "";
};

Journal.prototype = new OptionsBase();

Journal.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind();
	}
};

Journal.prototype.TemplateLoaded = function(req) {
	this.TemplateBaseLoaded(req);

	this.FindRelatedControls();

	this.Forum = this.Tab.Forum;

	this.GroupSelfAssign(["linkNewPost", "linkDeleteJournal"]);
	this.SetTabElementValue("FORUM_ID", this.Forum.FORUM_ID);
	this.SetTabElementValue("Title", this.Forum.TITLE);
	this.SetTabElementValue("linkNewPost", "Создать новую запись в	&laquo;" + this.Forum.TITLE + "&raquo;");
	this.SetTabElementValue("linkDeleteJournal", "Удалить	&laquo;" + this.Forum.TITLE + "&raquo;");

	if (this.Forum.ACCESS != FULL_ACCESS) {
		this.DisplayTabElement("linkDeleteJournal", 0);
	} else if (this.Forum.ACCESS != READ_ADD_ACCESS && this.Forum.ACCESS != FULL_ACCESS) {
		this.DisplayTabElement("linkNewPost", 0);
	}

	var spoilers = this.Inputs["Spoilers"];
	if (spoilers) {
		// TODO: Check type here
		MessagesSpoiler = new Spoiler(1, "Сообщения", 0, 0, function(tab) {new JournalMessages().LoadTemplate(tab, me.Id, me.Login)});
		TemplatesSpoiler = new Spoiler(2, "Шаблоны отображения", 0, 0, function(tab) {new JournalTemplates().LoadTemplate(tab, me.Id)});
		SettingsSpoiler = new Spoiler(3, "Настройки", 0, 0, function(tab) {new JournalSettings().LoadTemplate(tab, me.Id)});
		AccessSpoiler = new Spoiler(4, "Доступ / друзья", 0, 0, function(tab) {new ForumAccess().LoadTemplate(tab, me.Id)});

		s = [MessagesSpoiler, TemplatesSpoiler, SettingsSpoiler, AccessSpoiler];
		accessRow = accessMatrix[this.Forum.ACCESS][this.Forum.TYPE];

		for (i = 0; i < s.length; i++) {
			if (accessRow[i]) {
				s[i].Forum = this.Forum;
				s[i].ToString(spoilers);
			}
		}
	}
	InitMCE();
};

// tinyMCE initialization
function InitMCE() {
	tinymce.init({
		selector: "textarea.Editable",
		schema: "html5",
		language: "ru",
		theme: "modern",
		skin: "lightgray",
		resize: true,
		relative_urls: false,
		image_advtab: true,
		height: 500,
		statusbar: false,
		plugins: [
			"advlist autolink link image lists charmap hr anchor pagebreak",
			"searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
			"save contextmenu directionality template paste preview"
		],
		content_css: "css/content.css",
		menubar: "insert format",
		toolbar: "insertfile undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | code preview"
	}); 
};

/*

		mode : "textareas",
		theme : "advanced",
		language : "ru",
		plugins : "pagebreak,style,advimage,advlink,inlinepopups,preview,contextmenu,paste,directionality,visualchars,nonbreaking,xhtmlxtras,template,media",
		convert_urls : false,
		extended_valid_elements : "a[name|href|target|title|onclick],img[class|style|src|border=1|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
		editor_selector : "Editable",

		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
		theme_advanced_buttons2 : "link,unlink,image,|,bullist,numlist,|,outdent,indent,blockquote,|,insertdate,inserttime|,sub,sup,|,charmap,|,cite,del,ins,hr,nonbreaking,pagebreak",
		theme_advanced_buttons3 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,visualchars,removeformat,|,code,preview",
		theme_advanced_toolbar_location : "bottom",
		theme_advanced_toolbar_align : "left",
		theme_advanced_resizing : true,
        media_strict : false
	});
*/