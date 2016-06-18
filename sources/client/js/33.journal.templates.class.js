//3.4
/*
    Journal templates: Global markup, single message & stylesheets.
*/

function JournalTemplates() {
    this.fields = new Array("TITLE", "BODY", "MESSAGE", "CSS", "SKIN_TEMPLATE_ID");
    this.ServicePath = servicesPath + "journal.templates.service.php";
    this.Template = "journal_templates";
    this.ClassName = "JournalTemplates";

    this.Forum = new jjdto();
};

JournalTemplates.prototype = new OptionsBase();

JournalTemplates.prototype.request = function(params, callback) {
    var s = new ParamsBuilder(params);
    s.add('FORUM_ID', this.Forum.FORUM_ID);
    this.BaseRequest(s.build, callback);
};

JournalTemplates.prototype.requestCallback = function(req) {
    this.skinTemplateId = '';
    this.ownMarkupAllowed = '';
    this.defaultTemplateId = '';
    this.requestBaseCallback(req);

    if (this.data) {
        this.FillFrom(this.data);
        this.Bind();
    }
    if (!this.ownMarkupAllowed) {
        this.DisplayTabElement("label__1", false);
        bId = this.skinTemplateId > 0 ? this.skinTemplateId : this.defaultTemplateId;
    } else {
        bId = this.skinTemplateId;
    }
    button = SetRadioValue(this.inputs["SKIN_TEMPLATE_ID"], bId);
    if (button) {
        button.click();
    }
};

JournalTemplates.prototype.TemplateLoaded = function(req) {
    this.Forum = this.Tab.Forum;
    this.FORUM_ID = this.Tab.FORUM_ID;

    this.TemplateBaseLoaded(req);

    this.AssignTabTo("SKIN_TEMPLATE_ID");
    this.Tab.AddSubmitButton("SaveObject(this)", "", this);
};

/* Actions */

function Maximize(el) {
    el.className = "Maximized";
};


