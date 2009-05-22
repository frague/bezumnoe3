//2.7
/*
	Create/edit blog post in separate tab.
*/

var post_service = servicesPath + "journal.post.service.php";

function JournalPost() {
	this.fields = new Array("RECORD_ID", "TITLE", "CONTENT", "DATE", "TYPE", "IS_COMMENTABLE");
	this.defaultValues = new Array("-1", "", "", new Date().ToString(true), "0", "1");
	this.ServicePath = post_service;
	this.Template = "journal_post";
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
	this.BaseRequest(params, callback);
};

JournalPost.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		if (obj.data) {
			obj.FillFrom(obj.data);
			obj.Bind();
			if (journalMessagesObj) {
				journalMessagesObj.Request();
			}
		}
		if (!mceInitialized) {
			InitMCE();
			mceInitialized = 1;
		}
	}
};


/* Helper methods */

function LoadAndBindJournalPostToTab(tab, user_id, login) {
	LoadAndBindObjectToTab(tab, user_id, new JournalPost(), "JournalPost", JournalPostOnLoad, login);
};

function JournalPostOnLoad(req, tab) {
	if (tab) {
		tab.JournalPost.RECORD_ID = 1 * tab.PARAMETER;

		ObjectOnLoad(req, tab, "JournalPost");

		var tj = tab.JournalPost;
		tj.SetTabElementValue("LOGIN", tj.LOGIN);

		// Create content field
		tj.ContentField = CreateElement("textarea", "CONTENT" + Math.random(10000));
		tj.ContentField.className = "Editable";
		tj.ContentField.rows = 30;
		if (tj.Inputs["ContentHolder"]) {
			tj.Inputs["ContentHolder"].appendChild(tj.ContentField);
		}

		// Radios group rename
		RenameRadioGroup(tj.Inputs["TYPE"]);

		// DatePicker
		tj.Inputs["DATE"].value = new Date().ToString(1);
		var a = new DatePicker(tj.Inputs["DATE"], 1);

		/* Submit button */
		tab.AddSubmitButton("SaveJournalPost(this)");
	}
};


function SaveJournalPost(a) {
	if (a.obj) {
		var jp = a.obj.JournalPost;		
		jp.Save();
	}
};

function EditJournalPost(obj, post_id) {
	if (obj) {
		var login = obj.LOGIN ? obj.LOGIN : "";
		var tab_id = "post" + post_id;
		CreateUserTab(obj.USER_ID, login, LoadAndBindJournalPostToTab, "Пост в журнал", post_id, tab_id);
	}
};