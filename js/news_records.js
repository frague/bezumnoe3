import {utils} from './utils';
import {settings} from './settings';

/*
    Displaying news/guestbook messages grid
*/

class NewsRecords extends EditablePagedGrid {
    constructor() {
      super();
      this.fields = ["NEWS_RECORD_ID", "OWNER_ID", "TITLE", "CONTENT", "IS_HIDDEN", "SEARCH_DATE", "SEARCH"];
      this.ServicePath = settings.servicesPath + "news_records.service.php";
      this.Template = "news_records";
      this.ClassName = "NewsRecords";
      this.GridId = "NewsRecordsGrid";
      this.Columns = 2;
  }

  BaseBind() {}

  RequestCallback(req, obj) {
      if (obj) {
          obj.RequestBaseCallback(req, obj);
          obj.Bind(obj.data, obj.Total);
      }
  }

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);

    this.GroupSelfAssign(["buttonSearch", "ResetFilter", "linkRefresh", "AddNewsRecord", "RefreshNewsRecords"]);
    BindEnterTo(this.Inputs["SEARCH"], this.Inputs["buttonSearch"]);
    new DatePicker(this.Inputs["SEARCH_DATE"]);
  }

  CustomReset() {
    this.SetTabElementValue("SEARCH_DATE", "");
  }
};

/* News Record Data Transfer Object */

class nrdto extends EditableDTO {
  constructor(id, owner_id, title, content, is_hidden) {
    super(arguments);
    this.fields = ["Id", "OwnerId", "Title", "Content", "IsHidden", "Date"];
    this.Init(arguments);
  }

  ToShowView(index, obj) {
    var tr = MakeGridRow(index);

    var td1 = document.createElement("td");
    var date = ParseDate(this.Date).ToPrintableString();
    td1.appendChild(utils.makeDiv(date + ":   " + this.Title, "h2"));
    td1.appendChild(utils.makeDiv(this.Content));
    tr.appendChild(td1);

    tr.appendChild(this.utils.makeButtonsCell());
    return tr;
  }

  ToEditView(index, obj) {
      var tr = MakeGridRow(index);

      var td1 = document.createElement("td");
      td1.appendChild(utils.makeDiv("Дата:", "h4"));

      this.DateInput = document.createElement("input");
      this.DateInput.value = this.Date;
      td1.appendChild(this.DateInput);
      new DatePicker(this.DateInput);

      td1.appendChild(utils.makeDiv("Заголовок:", "h4"));

      this.TitleInput = document.createElement("input");
      this.TitleInput.value = this.Title;
      this.TitleInput.className = "Wide";
      td1.appendChild(this.TitleInput);

      td1.appendChild(utils.makeDiv("Содержание:", "h4"));

      this.ContentInput = document.createElement("textarea");
      this.ContentInput.value = this.Content;
      this.ContentInput.className = "Wide NewsDescription";
      td1.appendChild(this.ContentInput);

      tr.appendChild(td1);
      tr.appendChild(this.utils.makeButtonsCell());
      return tr;
  }
};

/* Helper methods */

function AddNewsRecord(a) {
  if (a.obj) {
    a.obj.AddRow(new nrdto(0, a.obj["USER_ID"], "Новое сообщение", "", 0, ""));
  }
};