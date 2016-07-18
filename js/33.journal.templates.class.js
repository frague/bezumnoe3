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

JournalTemplates.prototype.Request = function(params, callback) {
    if (!params) {
        params = "";
    }
    params += MakeParametersPair("FORUM_ID", this.Forum.FORUM_ID);
    this.BaseRequest(params, callback);
};

JournalTemplates.prototype.RequestCallback = function(req, obj) {
    if (obj) {
        obj.skinTemplateId = "";
        obj.ownMarkupAllowed = "";
        obj.defaultTemplateId = "";
        obj.RequestBaseCallback(req, obj);

        if (obj.data) {
            obj.FillFrom(obj.data);
            obj.Bind();
        }
        if (!obj.ownMarkupAllowed) {
            obj.DisplayTabElement("label__1", false);
            bId = obj.skinTemplateId > 0 ? obj.skinTemplateId : obj.defaultTemplateId;
        } else {
            bId = obj.skinTemplateId;
        }
        button = SetRadioValue(obj.Inputs["SKIN_TEMPLATE_ID"], bId);
        if (button) {
            button.click();
        }
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


