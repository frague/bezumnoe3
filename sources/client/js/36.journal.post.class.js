//3.5
/*
	Create/edit blog post in separate tab.
*/

var post_service = servicesPath + "journal.post.service.php";

function JournalPost(forum) {
	this.fields = new Array("RECORD_ID", "TITLE", "CONTENT", "DATE", "TYPE", "IS_COMMENTABLE", "FORUM_ID");
	this.defaultValues = new Array("-1", "", "", new Date().ToString(true), "0", "1", "");
	this.ServicePath = post_service;
	this.ClassName = "JournalPost";
	this.Template = "journal_post";

	this.Forum = forum;
	mceInitialized = 0;
};

JournalPost.prototype = new OptionsBase();

JournalPost.prototype.EditorIsShown = function() {
	return (!this.Forum || this.Forum.TYPE == "j");
};

JournalPost.prototype.Gather = function() {
	var editor = tinyMCE.get(this.ContentField.id);
	if (editor) {
		this.CONTENT = editor.save();
	} else {
		this.CONTENT = this.ContentField.value;
	}
	return this.BaseGather();
};

JournalPost.prototype.Bind = function() {
	this.BaseBind();
	this.ContentField.value = this.CONTENT;


	/* Update tab title */
	if (this.TITLE) {
		this.Tab.Title = "&laquo;" + this.TITLE.substr(0, 10) + "...&raquo;";
		this.Tab.Alt = this.TITLE;
		tabs.Print();
	}
};

JournalPost.prototype.request = function(params, callback) {
	var s = new ParamsBuiler(params)
		.add('RECORD_ID', this.RECORD_ID);
	if (this.Forum) {
		s.add('FORUM_ID', this.Forum.FORUM_ID);
	};
	if (this.TagsSpoiler && this.TagsSpoiler.AddedTags) {
		s.add('TAGS', this.TagsSpoiler.AddedTags.Gather());
	}
	this.BaseRequest(s.build(), callback);
};

JournalPost.prototype.requestCallback = function(req) {
	this.requestBaseCallback(req);
	if (this.data && this.data != '') {
		this.FillFrom(this.data);
		this.Bind();
		if (journalMessagesObj) {
			journalMessagesObj.request();
		};
	};

	if (!mceInitialized && this.EditorIsShown && this.EditorIsShown()) {
		InitMCE();
		mceInitialized = 1;
	};

	if (this.Forum) {
		this.SetTabElementValue("TITLE1", this.Forum.MakeTitle());
		this.Tab.SetAdditionalClass(this.Forum.TYPE);
	};
};

JournalPost.prototype.TemplateLoaded = function(req) {
	this.RECORD_ID = 1 * this.Tab.PARAMETER;
	this.TemplateBaseLoaded(req);

	this.SetTabElementValue("LOGIN", this.LOGIN);

	// Create content field
	this.ContentField = createElement("textarea", "CONTENT" + Math.random(10000));
	if (this.EditorIsShown()) {
		this.ContentField.className = "Editable";
	}
	this.ContentField.rows = 30;
	if (this.inputs["ContentHolder"]) {
		this.inputs["ContentHolder"].appendChild(this.ContentField);
	}

	// Radios group rename
	RenameRadioGroup(this.inputs["TYPE"]);

	// DatePicker
	this.inputs["DATE"].value = new Date().ToString(1);
	var a = new DatePicker(this.inputs["DATE"], 1);

	// Tags (labels) spoiler
	var tagsContainer = this.inputs["TagsContainer"];
	if (tagsContainer) {
		this.TagsSpoiler = new Spoiler(1, "Теги&nbsp;(метки)", 0, 0, function(tab) {new Tags().loadTemplate(tab, me.Id, me.Login)});
		this.TagsSpoiler.ToString(tagsContainer);
		this.TagsSpoiler.RECORD_ID = this.RECORD_ID;
	}

	// Submit button
	this.Tab.AddSubmitButton("SaveObject(this)", "", this);
};


/* Helper methods */

function EditJournalPost(obj, post_id) {
	if (obj) {
//		var login = obj.LOGIN ? obj.LOGIN : "";
		var login = "";
		var tab_id = "post" + post_id;
		createUserTab(obj.USER_ID, login, new JournalPost(obj.Forum), "Новая запись", post_id, tab_id);
	}
};
