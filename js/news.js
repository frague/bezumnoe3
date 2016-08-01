/*
  Displaying/managing news sections
*/

class News extends EditableGrid {
  constructor() {
    super();
    this.fields = ["OWNER_ID", "TITLE", "DESCRIPTION"];
    this.ServicePath = servicesPath + "news.service.php";
    this.ClassName = "News";
    this.Template = "news";
    this.GridId = "NewsGrid";
    this.Columns = 2;
  }

  BaseBind() {}

  RequestCallback(req, obj) {
    if (obj) {
      obj.RequestBaseCallback(req, obj);
      obj.Bind(obj.data);
    }
  }

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);
    this.GroupSelfAssign(["AddNews", "RefreshNews"]);

    this.Tab.NewsItems = this.Inputs["NewsItems"];
  }
}

/* News Data Transfer Object */

class ndto extends EditableDTO {
  constructor(id, title, description) {
    super(arguments);
    this.fields = ["Id", "Title", "Description"];
    this.Init(arguments);
  }

  ToShowView(index, obj) {
    var tr = MakeGridRow(index);

    var td1 = document.createElement("td");
      var h2 = document.createElement("h2");
      var a = MakeDiv(this.Title, "a");
      a.onclick = function() {ShowNewsRecords(this)};
      a.obj = this;
      h2.appendChild(a);
      td1.appendChild(h2);
    td1.appendChild(MakeDiv(this.Description));
    tr.appendChild(td1);

    tr.appendChild(this.MakeButtonsCell());
    return tr;
  }

  ToEditView(index, obj) {
    var tr = MakeGridRow(index);

    var td1 = document.createElement("td");
      td1.appendChild(MakeDiv("Название:"));

      this.TitleInput = document.createElement("input");
      this.TitleInput.value = this.Title;
      this.TitleInput.className = "Wide";
      td1.appendChild(this.TitleInput);

      td1.appendChild(MakeDiv("Описание:"));

      this.DescriptionInput = document.createElement("textarea");
      this.DescriptionInput.innerHTML = this.Description;
      this.DescriptionInput.className = "Wide NewsDecription";
      td1.appendChild(this.DescriptionInput);
    tr.appendChild(td1);

    tr.appendChild(this.MakeButtonsCell());
    return tr;
  };
}

/* Helper methods */

function AddNews(a) {
  if (a.obj) {
    a.obj.AddRow(new ndto(0, "Новый раздел", ""));
  }
};

function ShowNewsRecords(a) {
  var tab = a.obj.Grid.Tab;
  if (tab.NewsItems) {
    tab.NewsItems.innerHTML = "";
    var s = new Spoiler(0, a.obj.Title, 0, 1);
    s.ToString(tab.NewsItems);

    new NewsRecords().LoadTemplate(s, a.obj.Id);
  }
};