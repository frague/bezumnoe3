//3.0
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

JournalPost.prototype.Gather = function() {	// Method to override
	var editor = tinyMCE.get(this.ContentField.id);
	if (editor) {
		this.CONTENT = editor.save();
	}
	return this.BaseGather();
};

JournalPost.prototype.Bind = function() {	// Method to override
	this.BaseBind();
	this.ContentField.value = this.CONTENT;


	/* Update tab title */
	if (this.TITLE) {
		this.Tab.Title = "&laquo;" + this.TITLE.substr(0, 10) + "...&raquo;";
		this.Tab.Alt = this.TITLE;
		tabs.Print();
	}
};

JournalPost.prototype.Request = function(params, callback) {
	if (!params) {
		params = "";
	}
	params += MakeParametersPair("RECORD_ID", this.RECORD_ID);
	params += MakeParametersPair("FORUM_ID", this.Forum.FORUM_ID);
	this.BaseRequest(params, callback);
};

JournalPost.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		if (obj.data && obj.data != "") {	// "" comparison makes sense
			obj.FillFrom(obj.data);
			obj.Bind();
			if (journalMessagesObj) {
				journalMessagesObj.Request();
			}
		}

		if (!mceInitialized && (!obj.Forum || obj.Forum.TYPE == "j")) {
			InitMCE();
			mceInitialized = 1;
		}
	}
	if (obj.Forum) {
		obj.SetTabElementValue("TITLE1", obj.Forum.MakeTitle());
		obj.Tab.SetAdditionalClass(obj.Forum.TYPE);
	}
};

JournalPost.prototype.TemplateLoaded = function(req) {
	this.RECORD_ID = 1 * this.Tab.PARAMETER;

	this.TemplateBaseLoaded(req);

	this.SetTabElementValue("LOGIN", this.LOGIN);

	// Create content field
	this.ContentField = CreateElement("textarea", "CONTENT" + Math.random(10000));
	this.ContentField.className = "Editable";
	this.ContentField.rows = 30;
	if (this.Inputs["ContentHolder"]) {
		this.Inputs["ContentHolder"].appendChild(this.ContentField);
	}

	// Radios group rename
	RenameRadioGroup(this.Inputs["TYPE"]);

	// DatePicker
	this.Inputs["DATE"].value = new Date().ToString(1);
	var a = new DatePicker(this.Inputs["DATE"], 1);

	/* Submit button */
	this.Tab.AddSubmitButton("SaveJournalPost(this)", "", this);
};


/* Helper methods */

function SaveJournalPost(a) {
	if (a.obj) {
		a.obj.Save();
	}
};

function EditJournalPost(obj, post_id) {
	if (obj) {
//		var login = obj.LOGIN ? obj.LOGIN : "";
		var login = "";
		var tab_id = "post" + post_id;
		CreateUserTab(obj.USER_ID, login, new JournalPost(obj.Forum), "Пост в журнал", post_id, tab_id);
	}
};