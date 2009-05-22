//6.3
/*
	User profile data & helper methods
*/

function Profile() {
	this.fields = new Array("LOGIN", "EMAIL", "NAME", "GENDER", "BIRTHDAY", "CITY", "ICQ", "URL", "PHOTO", "AVATAR", "ABOUT", "REGISTERED", "LAST_VISIT");
	this.ServicePath = servicesPath + "profile.service.php";
	this.Template = "userdata";
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

	this.SetTabElementValue("BANNED", this.BANNED_BY > 0);
	this.DisplayTabElement("BanDetails", !this.ADMIN && this.BANNED_BY > 0);

	/* Ban status */
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


/* Load & bind user profile to Tab */

function LoadAndBindProfileToTab(tab, user_id) {
	LoadAndBindObjectToTab(tab, user_id, new Profile(), "Profile", ProfileOnLoad);
}

function ProfileOnLoad(req, tab) {
	if (tab) {
		ObjectOnLoad(req, tab, "Profile");

		var tp = tab.Profile;

		/* Init images (photo & avatar) */
		tab.InitUploadFrame("AvatarUploadFrame");

		/* Assign Tab to links */
		tp.Photo1 = new Img("PHOTO", "Photo", "uploadForm", tab.UploadFrame, userPhotosPath, 300);
		tp.Avatar1 = new Img("AVATAR", "Avatar", "avatarUploadForm", tab.AvatarUploadFrame, avatarsPath, 120);

		tp.GroupTabAssign(["linkDeletePhoto", "linkDeleteAvatar", "BANNED", "PASSWORD"]);
		tp.GroupSelfAssign(["linkRefresh", "linkLockIP"]);

		/* Viewing my profile hide Status & Ban sections */
		if (tp.USER_ID == me.Id) {
			tp.DisplayTabElement("NotForMe", false);
		}

		/* Date pickers */
		new DatePicker(tp.Inputs["BIRTHDAY"]);
		new DatePicker(tp.Inputs["BANNED_TILL"], 1);

		/* Admin comments spoiler */
		if (me.IsAdmin()) {
			var acs = new Spoiler(1, "Комментарии администраторов	&	логи", 0, 0, function(tab) {LoadAndBindAdminCommentsToTab(tab,tp.USER_ID)});
			acs.ToString(tp.Inputs["AdminComments"]);
	   	}

		/* Submit button */
		tab.AddSubmitButton("SaveProfile(this)");
	}
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
		a.obj.Alerts.Clear();

		/* Saving Photo & Avatar */
		var p = a.obj.Profile;
		UploadImage(p, p.Photo1);
		UploadImage(p, p.Avatar1);

		/* Saving profile */
		a.obj.Profile.Save(ProfileSaved);
	}
};

function ProfileSaved(req, obj) {
	if (obj && req.responseText) {
		var tabObject = obj.Tab;
		obj.RequestCallback(req, obj);

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
