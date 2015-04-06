//5.1
/*
	Forum records tags (labels)
*/

var tagPattern = new RegExp("^[a-zA-Zа-я\ёА-Я\Ё0-9\-_\ ]+$", "gim");
var maxTags = 10;

function Tags() {
	this.fields = [];
	this.ServicePath = servicesPath + "tags.service.php";
	this.Template = "tags";
	this.ClassName = "Tags";

	this.IsLoaded = 0;
};

Tags.prototype = new OptionsBase();

Tags.prototype.Bind = function(data, found) {
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
};

Tags.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.FillFrom(obj.data);
		obj.Bind(obj.data, obj.found);
	}
};

Tags.prototype.TemplateLoaded = function(req) {
	this.Tab.AddedTags = new Collection();

	this.RECORD_ID = this.Tab.RECORD_ID;

	this.TemplateBaseLoaded(req);
	this.FindRelatedControls();

	this.AssignSelfTo("AddTag");

	// Validation
	this.Tab.Validators = new ValidatorsCollection();
	this.Tab.Validators.Add(new Validator(this.Inputs["SEARCH_TAG"], new MatchPattern(tagPattern), "Тег содержит запрешённые символы&nbsp;(разрешено a-z а-я 0-9 -_)", Random(10000)));
	this.Tab.Validators.Init(this.Inputs["Errors"]);

	var req = new DelayedRequestor(this, this.Inputs["SEARCH_TAG"]);
	req.Submitter = this.Inputs["AddTag"];
};

Tags.prototype.Request = function(params, callback) {
	if (!params) {
		params = "";
	}
	params += MakeParametersPair("RECORD_ID", this.RECORD_ID);
	this.BaseRequest(params, callback);
};

Tags.prototype.AddNewTag = function(input) {
	if (input && input.obj) {
		var value = input.obj.Inputs["SEARCH_TAG"].value;
		var tag = new tagdto(value, value);
		tag.obj = this;
		this.AT(tag);
	}
};

Tags.prototype.AT = function(tag) {
	if (this.Tab.AddedTags.Count() >= maxTags) {
		this.Inputs["Errors"].innerHTML = "<li> Можно добавить не более " + maxTags + " тегов";
		return false;
	}
	this.Tab.AddedTags.Add(tag);
	this.ShowTags();
	this.Inputs["SEARCH_TAG"].value = "";
	return true;
};

Tags.prototype.DT = function(id) {
	this.Tab.AddedTags.Delete(id);
	this.ShowTags();
};

Tags.prototype.ShowTags = function() {
	this.SetTabElementValue("TagsContainer", this.Tab.AddedTags.Count() > 0 ? "" : "не указаны");
	this.Tab.AddedTags.ToString(this.Inputs["TagsContainer"]);
};


/*
	Tag Data Transfer Object
*/

function tagdto(id, title) {
	this.fields = ["Id", "Title"];
	this.Init(arguments);
};

tagdto.prototype = new DTO();

tagdto.prototype.ToString = function(holder, index) {
	holder.appendChild(d.createTextNode((index ? ",	" : "") + this.Id));
	var a = d.createElement("a");
	a.href = voidLink;
	a.className = "CloseSign Small";
	a.obj = this;
	a.onclick = function(){this.obj.obj.DT(this.obj.Id)};
	a.innerHTML = "x";
	holder.appendChild(a);
};

tagdto.prototype.ToSelect = function(holder) {
	var li = d.createElement("li");
	var a = d.createElement("a");
	a.href = voidLink;
	a.obj = this;
	a.onclick = function(){this.obj.obj.AT(this.obj)};
	a.innerHTML = this.Title;
	li.appendChild(a);
	holder.appendChild(li);
};

tagdto.prototype.Gather = function(index) {
	return (index ? "|" : "") + this.Title;
};
