import {settings} from './settings';

/*
  Journal functionality: Blog templates, messages, settings
*/

class JournalsManager extends OptionsBase {
  constructor() {
    super();
    this.fields = ["SEARCH", "SHOW_FORUMS", "SHOW_JOURNALS", "SHOW_GALLERIES"];
    this.ServicePath = settings.servicesPath + "journal_manager.service.php";
    this.Template = "journal_manager";
    this.ClassName = "JournalManager";
  }

  Request(params, callback) {
    this.BaseRequest(this.Gather(), callback);
  }

  RequestCallback(req, obj) {
    if (obj) {
      obj.RequestBaseCallback(req, obj);
      obj.Bind();
    }
  }

  Bind() {
    var container = this.Inputs["ForumsContainer"];
    if (container) {
      container.innerHTML = "";
      if (this.data && this.data.length) {
        for (var i = 0, l = this.data.length; i < l; i++) {
          this.data[i].ToString(i, container);
        }
      }
    }

    this.DisplayTabElement("CreateJournal", !this.HasJournal);
    this.SetTabElementValue("linkNewForum", (this.HasJournal ? "" : "Создать журнал"));
  }

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);

    this.FindRelatedControls();
    this.GroupSelfAssign(this.fields);
    this.GroupSelfAssign(["linkRefresh", "linkNewForum"]);
  }
}

/* Forum line DTO */

class jjdto extends DTO {
  constructor(id, title, type, access) {
    super(arguments);
    this.fields = ["FORUM_ID", "TITLE", "TYPE", "ACCESS"];
    this.Init(arguments);
    this.ForumTypes = {"f": "Форум", "g": "Фотогалерея", "j": "Журнал"};
  }

  ToShowView(index, container) {
    var t = this.MakeTitle();
    spoiler = new Spoiler('_j' + (i + 1), t, 0, 0, 
      (tab) => new Journal().LoadTemplate(tab, me.Id, me.Login)
    );
    spoiler.Forum = this;
    spoiler.ToString(container);
    if (!index) {
      spoiler.Switch();
    }
  }

  MakeTitle() {
    return this.ForumTypes[this.TYPE] + "  &laquo;" + this.TITLE + "&raquo;";
  }
}

/* New Forum Creation */
function CreateForum(obj) {
  obj.BaseRequest("go=create&");
}