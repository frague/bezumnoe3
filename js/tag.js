import {utils} from './utils';
import {settings} from './settings';

/*
  Forum records tags (labels)
*/

var tagPattern = new RegExp("^[a-zA-Zа-я\ёА-Я\Ё0-9\-_\ ]+$", "gim");
var maxTags = 10;

class Tags extends OptionsBase {
  constructor() {
    super();
    this.fields = [];
    this.ServicePath = settings.servicesPath + "tags.service.php";
    this.Template = "tags";
    this.ClassName = "Tags";

    this.IsLoaded = 0;
  }

  Bind(data, found) {
    if (data && data.length > 0 && !this.IsLoaded) {
      var s = "";
      this.SetTabElementValue("TagsContainer", "");
      this.Tab.AddedTags.Clear();
      var holder = this.Inputs["TagsContainer"];

      for (var i = 0,l = data.length; i < l; i++) {
        data[i].obj = this;
        this.Tab.AddedTags.Add(data[i]);
        s += data[i].ToString(holder, i);
      }
      this.IsLoaded = 1;
    }

    if (found) {
      var s = "";
      var holder = this.Inputs["FoundTags"];
      holder.innerHTML = "";
      for (var i = 0,l = found.length; i < l; i++) {
        found[i].obj = this;
        found[i].ToSelect(holder);
      }
    }
  }

  RequestCallback(req, obj) {
    if (obj) {
      obj.RequestBaseCallback(req, obj);
      obj.FillFrom(obj.data);
      obj.Bind(obj.data, obj.found);
    }
  }

  TemplateLoaded(req) {
    this.Tab.AddedTags = new Collection();

    this.RECORD_ID = this.Tab.RECORD_ID;

    this.TemplateBaseLoaded(req);
    this.FindRelatedControls();

    this.AssignSelfTo("AddTag");

    // Validation
    this.Tab.Validators = new ValidatorsCollection();
    this.Tab.Validators.Add(new Validator(this.Inputs["SEARCH_TAG"], new MatchPattern(tagPattern), "Тег содержит запрешённые символы&nbsp;(разрешено a-z а-я 0-9 -_)", utils.random(10000)));
    this.Tab.Validators.Init(this.Inputs["Errors"]);

    var req = new DelayedRequestor(this, this.Inputs["SEARCH_TAG"]);
    req.Submitter = this.Inputs["AddTag"];
  }

  Request(params, callback) {
    if (!params) {
      params = "";
    }
    params += MakeParametersPair("RECORD_ID", this.RECORD_ID);
    this.BaseRequest(params, callback);
  }

  AddNewTag(input) {
    if (input && input.obj) {
      var value = input.obj.Inputs["SEARCH_TAG"].value;
      var tag = new tagdto(value, value);
      tag.obj = this;
      this.AT(tag);
    }
  }

  AT(tag) {
    if (this.Tab.AddedTags.Count() >= maxTags) {
      this.Inputs["Errors"].innerHTML = "<li> Можно добавить не более " + maxTags + " тегов";
      return false;
    }
    this.Tab.AddedTags.Add(tag);
    this.ShowTags();
    this.Inputs["SEARCH_TAG"].value = "";
    return true;
  }

  DT(id) {
    this.Tab.AddedTags.Delete(id);
    this.ShowTags();
  }

  ShowTags() {
    this.SetTabElementValue("TagsContainer", this.Tab.AddedTags.Count() > 0 ? "" : "не указаны");
    this.Tab.AddedTags.ToString(this.Inputs["TagsContainer"]);
  }
}

/*
  Tag Data Transfer Object
*/

class tagdto extends DTO {
  constructor(id, title) {
    super(arguments);
    this.fields = ["Id", "Title"];
    this.Init(arguments);
  };

  ToString(holder, index) {
    holder.appendChild(d.createTextNode((index ? ", " : "") + this.Id));
    var a = document.createElement("a");
    a.href = settings.voidLink;
    a.className = "CloseSign Small";
    a.obj = this;
    a.onclick = function(){this.obj.obj.DT(this.obj.Id)};
    a.innerHTML = "x";
    holder.appendChild(a);
  }

  ToSelect(holder) {
    var li = document.createElement("li");
    var a = document.createElement("a");
    a.obj = this;
    a.onclick = function(){this.obj.obj.AT(this.obj)};
    a.innerHTML = this.Title;
    li.appendChild(a);
    holder.appendChild(li);
  }

  Gather(index) {
    return (index ? "|" : "") + this.Title;
  }
}