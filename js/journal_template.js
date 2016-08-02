import {utils} from './utils';
import {settings} from './settings';


/*
	Journal templates: Global markup, single message & stylesheets.
*/

class JournalTemplates extends OptionsBase {
	constructor() {
		super();        
		this.fields = new Array("TITLE", "BODY", "MESSAGE", "CSS", "SKIN_TEMPLATE_ID");
		this.ServicePath = settings.servicesPath + "journal.templates.service.php";
		this.Template = "journal_templates";
		this.ClassName = "JournalTemplates";

		this.Forum = new jjdto();
	}

	Request(params, callback) {
		if (!params) {
			params = "";
		}
		params += MakeParametersPair("FORUM_ID", this.Forum.FORUM_ID);
		this.BaseRequest(params, callback);
	}

	RequestCallback(req, obj) {
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
			button = utils.setRadioValue(obj.Inputs["SKIN_TEMPLATE_ID"], bId);
			if (button) {
				button.click();
			}
		}
	}

	TemplateLoaded(req) {
		this.Forum = this.Tab.Forum;
		this.FORUM_ID = this.Tab.FORUM_ID;

		this.TemplateBaseLoaded(req);

		this.AssignTabTo("SKIN_TEMPLATE_ID");
		this.Tab.AddSubmitButton("SaveObject(this)", "", this);
	}
}

/* Actions */

function Maximize(el) {
	el.className = "Maximized";
}


