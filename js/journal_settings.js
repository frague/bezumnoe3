import {settings} from './settings';

/*
  Journal settings of user menu.
*/

class JournalSettings extends OptionsBase {
  constructor() {
    super();
    this.fields = ["ALIAS", "REQUESTED_ALIAS", "TITLE", "DESCRIPTION", "IS_PROTECTED", "IS_HIDDEN", "OWN_MARKUP_ALLOWED"];
    this.ServicePath = settings.servicesPath + "journal.settings.service.php";
    this.Template = "journal_settings";
    this.ClassName = "JournalSettings";

    this.Forum = new jjdto();
  }

  RequestCallback(req, obj) {
    if (obj) {
      obj.RequestBaseCallback(req, obj);
      obj.FillFrom(obj.data);
      obj.Bind(obj.data);
    }
  }

  TemplateLoaded(req) {
    this.Forum = this.Tab.Forum;
    if (this.Forum && this.Forum.FORUM_ID) {
      this.FORUM_ID = this.Forum.FORUM_ID;
    }

    this.TemplateBaseLoaded(req);

    if (!me.isAdmin()) {
      this.SetTabElementValue("IsHidden", "");
    };

    this.AssignTabTo("linkRefresh");
    this.FindRelatedControls();
    this.Tab.AddSubmitButton("SaveObject(this)", "", this);
  }

  Request(params, callback) {
    if (!params) {
      params = "";
    }
    params += MakeParametersPair("FORUM_ID", this.FORUM_ID);
    this.BaseRequest(params, callback);
  }
}