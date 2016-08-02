import {settings} from './settings';

/*
  Journal functionality: Blog templates, messages, settings
*/

// Warning! Matrix should correspond to standard accesses
var accessMatrix = [
  {f: [0, 0, 0, 0], g: [0, 0, 0, 0], j: [0, 0, 0, 0]}, 
  {f: [1, 0, 0, 0], g: [1, 0, 0, 0], j: [1, 0, 0, 0]}, 
  {f: [1, 0, 0, 0], g: [1, 0, 0, 0], j: [1, 0, 0, 0]}, 
  {f: [1, 0, 0, 0], g: [1, 0, 0, 0], j: [1, 0, 0, 0]}, 
  {f: [1, 0, 1, 1], g: [1, 0, 1, 0], j: [1, 1, 1, 1]}
];

var MessagesSpoiler, TemplatesSpoiler, SettingsSpoiler;

class Journal extends OptionsBase {
  constructor() {
    super();
    this.fields = [];
    this.ServicePath = settings.servicesPath + "journal.service.php";
    this.Template = "journal";
    this.ClassName = "Journal";
    this.Forum = "";
    
    this.MessagesSpoiler = null;
    this.TemplatesSpoiler = null;
    this.SettingsSpoiler = null;
    this.AccessSpoiler = null;
  }

  RequestCallback(req, obj) {
    if (obj) {
      obj.RequestBaseCallback(req, obj);
      obj.Bind();
    }
  }

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);

    this.FindRelatedControls();

    this.Forum = this.Tab.Forum;

    this.GroupSelfAssign(["linkNewPost", "linkDeleteJournal"]);
    this.SetTabElementValue("FORUM_ID", this.Forum.FORUM_ID);
    this.SetTabElementValue("Title", this.Forum.TITLE);
    this.SetTabElementValue("linkNewPost", "Создать новую запись в  &laquo;" + this.Forum.TITLE + "&raquo;");
    this.SetTabElementValue("linkDeleteJournal", "Удалить &laquo;" + this.Forum.TITLE + "&raquo;");

    if (this.Forum.ACCESS != forumAccess.FULL_ACCESS) {
      this.DisplayTabElement("linkDeleteJournal", 0);
    } else if (this.Forum.ACCESS != forumAccess.READ_ADD_ACCESS && this.Forum.ACCESS != forumAccess.FULL_ACCESS) {
      this.DisplayTabElement("linkNewPost", 0);
    }

    var spoilers = this.Inputs["Spoilers"];
    if (spoilers) {
      // TODO: Check type here
      this.MessagesSpoiler = new Spoiler(1, "Сообщения", 0, 0, function(tab) {new JournalMessages().LoadTemplate(tab, me.Id, me.Login)});
      this.TemplatesSpoiler = new Spoiler(2, "Шаблоны отображения", 0, 0, function(tab) {new JournalTemplates().LoadTemplate(tab, me.Id)});
      this.SettingsSpoiler = new Spoiler(3, "Настройки", 0, 0, function(tab) {new JournalSettings().LoadTemplate(tab, me.Id)});
      this.AccessSpoiler = new Spoiler(4, "Доступ / друзья", 0, 0, function(tab) {new ForumAccess().LoadTemplate(tab, me.Id)});

      let accessRow = accessMatrix[this.Forum.ACCESS][this.Forum.TYPE];
      _.each(
        [this.MessagesSpoiler, this.TemplatesSpoiler, this.SettingsSpoiler, this.AccessSpoiler],
        (spoiler, index) => {
          if (accessRow[i]) {
            spoiler.Forum = this.Forum;
            spoiler.ToString(spoilers);
          }
        }
      );
    }
    this.InitMCE();
  }

  // tinyMCE initialization
  InitMCE() {
    tinymce.init({
      selector: "textarea.Editable",
      schema: "html5",
      language: "ru",
      theme: "modern",
      skin: "lightgray",
      resize: true,
      relative_urls: false,
      image_advtab: true,
      height: 500,
      statusbar: false,
      plugins: [
        "advlist autolink link image lists charmap hr anchor pagebreak",
        "searchreplace visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
        "save contextmenu directionality template paste preview"
      ],
      content_css: "css/content.css",
      menubar: "insert format",
      toolbar: "insertfile undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | code preview"
    }); 
  };
}
