//2.6
/*
	Journal templates: Global markup, single message & stylesheets.
*/

function JournalTemplates() {
	this.fields = new Array("BODY", "MESSAGE", "CSS");
	this.ServicePath = servicesPath + "journal.templates.service.php";
	this.Template = "journal_templates";
};

JournalTemplates.prototype = new OptionsBase();

JournalTemplates.prototype.Bind = function() {
	if (this["SKIN_TEMPLATE_ID"]) {
		var select = this.Inputs["SKIN_TEMPLATE_ID"];
		select.value = this["SKIN_TEMPLATE_ID"];
		PreviewSkin(select);
	}
	this.BaseBind();
};

JournalTemplates.prototype.Gather = function() {
	var params = this.BaseGather();
	params += MakeParametersPair("SKIN_TEMPLATE_ID", this.Inputs["SKIN_TEMPLATE_ID"].value);
	return params;
};

JournalTemplates.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.skinTemplateId = "";
		obj.RequestBaseCallback(req, obj);
		obj["SKIN_TEMPLATE_ID"] = obj.skinTemplateId;
		if (obj.data) {
			obj.FillFrom(obj.data);
			obj.Bind();
		}
	}
};

/* Helper methods */

function LoadAndBindJournalTemplatesToTab(tab, user_id) {
	LoadAndBindObjectToTab(tab, user_id, new JournalTemplates(), "JournalTemplates", JournalTemplatesOnLoad);
};

function JournalTemplatesOnLoad(req, tab) {
	if (tab) {
		ObjectOnLoad(req, tab, "JournalTemplates");

		var jt = tab.JournalTemplates;
		jt.AssignTabTo("SKIN_TEMPLATE_ID");

		/* Submit button */
		tab.AddSubmitButton("SaveJournalTemplate(this)");
	}
};

/* Actions */

function Maximize(el) {
	el.className = "Maximized";
};

function ShowSkinPreview(jt, background) {
	var url = "url(".length;
	var bg = background.substr(url, background.length - url -1);

	jt.DisplayTabElement("previewCell", bg);
	if (bg) {
		jt.Inputs["skinPreview"].innerHTML = "<img src='" + skinsPreviewPath + bg + "' class='Photo'>";
	}
};

function PreviewSkin(select) {
	var jt = select.Tab.JournalTemplates;
	if (jt) {
		jt.FindRelatedControls();
		if (jt.Inputs["templates"]) {
			jt.DisplayTabElement("templates", !select.value);
			ShowSkinPreview(jt, select.options[select.selectedIndex].style.backgroundImage);
		}
	}
};

function SaveJournalTemplate(a) {
	if (a.obj) {
		a.obj.JournalTemplates.Save();
	}
};

/* Confirms */

function DeleteRecord(a, id) {
	co.Show(function() {DeleteRecordConfirmed(a.obj, id)}, "Удалить запись?", "Запись в блоге и все комментарии к ней будут удалены.<br>Продолжить?");
};

