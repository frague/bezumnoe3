//6.8
/*
	User profile data & helper methods
*/

function Profile() {
	this.fields = new Array("LOGIN", "EMAIL", "NAME", "GENDER", "BIRTHDAY", "CITY", "ICQ", "URL", "PHOTO", "AVATAR", "ABOUT", "REGISTERED", "LAST_VISIT");
	this.ServicePath = servicesPath + "profile.service.php";
	this.Template = "userdata";
	this.ClassName = "Profile";	// Optimize?
};

Profile.prototype = new OptionsBase();

Profile.prototype.Gather = function() {
	var result = this.BaseGather();
	result += this.GatherOne("PASSWORD");
	result += this.GatherOne("PASSWORD_CONFIRM");
	result += this.GatherOne("BANNED");
	return result;
};

Profile.prototype.Bind = function() {
	this.BaseBind();

	/* Bind images */
	DisplayElement(this.Inputs["liDeletePhoto"], this.PHOTO);
	DisplayElement(this.Inputs["liDeleteAvatar"], this.AVATAR);

	this.BindImage(this.Photo1);
	this.BindImage(this.Avatar1);

	/* Ban Status */
	this.SetTabElementValue("BANNED", this.BANNED_BY > 0);
	this.DisplayTabElement("BanDetails", !this.ADMIN && this.BANNED_BY > 0);

	var ban_status = "";
	if (this.ADMIN) {
		ban_status += "Пользователь забанен администратором&nbsp;<b>" + this.ADMIN + "</b>";
		ban_status += "&nbsp;" + (this.BANNED_TILL ? "до " + this.BANNED_TILL : "бессрочно");
		if (this.BAN_REASON) {
			ban_status += "&nbsp;по причине&nbsp;&laquo;" + this.BAN_REASON + "&raquo;";
		}
	}
	this.SetTabElementValue("BanStatus", ban_status);

	// Correct dates
	this.UpdateToPrintableDate("REGISTERED");
	this.UpdateToPrintableDate("LAST_VISIT");
};

Profile.prototype.BindImage = function(img) {
	if (this[img.Field]) {
		this.ReloadImage(img);
	} else {
		img.ImageObject = "";
		this.PrintLoadedImage(img);
	}
};

Profile.prototype.CheckPhoto = function(img) {
	if (!img.HasImage()) {
		img.ImageObject = d.createElement("img");
		img.ImageObject.Profile = this;
		img.ImageObject.Img = img;
		img.ImageObject.onload = function(){ImageLoaded(this)};
	}
};

Profile.prototype.ReloadImage = function(img) {
	if (!this.Tab.Alerts.HasErrors) {
		this.SetTabElementValue(img.Container, LoadingIndicator);
		this.CheckPhoto(img);
		img.ImageObject.src = img.Path + this[img.Field] + "?" + Math.random(100);
		if (this.Inputs[img.UploadForm]) {
			this.Inputs[img.UploadForm].reset();
		}
	}
};

Profile.prototype.PrintLoadedImage = function(img) {
	var p = this.Inputs[img.Container], result = "не загружено";
	if (p) {
		if (img.ImageObject) {
			var dim = "width='" + img.MaxWidth + "'";
			if (img.ImageObject.width < img.MaxWidth) {
				dim = "width='" + img.ImageObject.width + "' height='" + img.ImageObject.height + "'";
			}
			result = "<img class='Photo' src='" + img.ImageObject.src + "' " + dim + ">";
		}
		p.innerHTML = result;
	}
};

Profile.prototype.RequestCallback = function(req, obj) {
	if (obj) {
		obj.RequestBaseCallback(req, obj);
		obj.Bind();
		obj.Initialized = false;
	}
};

function ImageLoaded(e) {
	e.Profile.PrintLoadedImage(e.Img);
};

// Loading template
Profile.prototype.TemplateLoaded = function(req) {
    this.TemplateBaseLoaded(req);

	/* Init images (photo & avatar) */
	this.Tab.InitUploadFrame("AvatarUploadFrame");

	/* Assign Tab to links */
	this.Photo1 = new Img("PHOTO", "Photo", "uploadForm", this.Tab.UploadFrame, userPhotosPath, 300);
	this.Avatar1 = new Img("AVATAR", "Avatar", "avatarUploadForm", this.Tab.AvatarUploadFrame, avatarsPath, 120);

	this.GroupTabAssign(["linkDeletePhoto", "linkDeleteAvatar", "BANNED", "PASSWORD"]);
	this.GroupSelfAssign(["linkRefresh", "linkLockIP"]);

	/* Viewing my profile hide Status & Ban sections */
	if (this.USER_ID == me.Id) {
		this.DisplayTabElement("NotForMe", false);
	}

	/* Date pickers */
	new DatePicker(this.Inputs["BIRTHDAY"]);
	new DatePicker(this.Inputs["BANNED_TILL"], 1);

	/* OpenIDs associated with this user */
	var oid = new Spoiler(1, "OpenID", 0, 0, function(tab) {new OpenIds().LoadTemplate(tab, this.USER_ID)});
	oid.USER_ID = this.USER_ID;
	oid.ToString(this.Inputs["OpenIds"]);

	/* Admin comments spoiler */
	if (me.IsAdmin()) {
		var acs = new Spoiler(2, "Комментарии администраторов	&	логи", 0, 0, function(tab) {new AdminComments().LoadTemplate(tab, this.USER_ID)});
		acs.USER_ID = this.USER_ID;
		acs.ToString(this.Inputs["AdminComments"]);
   	}

	/* Submit button */
	this.Tab.AddSubmitButton("SaveProfile(this)", "", this);
};

/* Save profile */

function UploadImage(profile, img) {
	var form = profile.Inputs[img.UploadForm];
	if (form) {
		var p = form["PHOTO1"];
		if (p && p.value) {
			form["tab_id"].value = profile.Tab.Id;
			form["USER_ID"].value = profile.USER_ID;
			form.target = img.Frame.name;
			form.submit();
		}
	}
};

function SaveProfile(a) {
	if (a.obj) {
		a.obj.Tab.Alerts.Clear();

		/* Saving Photo & Avatar */
		UploadImage(a.obj, a.obj.Photo1);
		UploadImage(a.obj, a.obj.Avatar1);

		/* Saving profile */
		a.obj.Save(ProfileSaved);
	}
};

function ProfileSaved(responseText, obj) {
	if (obj && responseText) {
		var tabObject = obj.Tab;
		obj.RequestCallback(responseText, obj);

		// Refresh admin comments
		obj.FindRelatedControls(true);
		DoClick(obj.Inputs["RefreshAdminComments"]);
	}
};

/* Links actions */

function DeletePhotoConfirmed(a, image) {
	if (a.Tab) {
		a.Tab.Alerts.Clear();
		a.Tab.Profile.Request(MakeParametersPair("go", "delete_" + image));
	}
};

function ShowBanDetails(cb) {
	if (cb.Tab) {
		cb.Tab.Profile.DisplayTabElement("BanDetails", !cb.Tab.Profile.ADMIN && cb.checked);
	}
};

function RestoreInput(el, relatedBlockId) {
	var tab = el.Tab;
	if (!tab) {
		return;
	}
	if (el.value != el.previousValue) {
		tab.Profile.DisplayTabElement(relatedBlockId, el.value);
	}
	if (!el.value) {
		el.value = empty_pass;
	}
};

/* Profile Image helper class */

function Img(field, container, form, frame, path, max_width) {
	this.Field = field;
	this.Container = container;
	this.UploadForm = form;
	this.Frame = frame;
	this.Path = path;
	this.MaxWidth = max_width;
};

Img.prototype.HasImage = function() {
	return this.ImageObject;
};


/* Confirms */

function DeletePhoto(a) {
	co.Show(function() {DeletePhotoConfirmed(a,"photo")}, "Удалить фотографию?", "Фотография пользователя будет удалена из профиля.<br>Вы уверены?");
};

function DeleteAvatar(a) {
	co.Show(function() {DeletePhotoConfirmed(a,"avatar")}, "Удалить аватар?", "Автар будет удален из профиля.<br>Вы уверены?");
};
