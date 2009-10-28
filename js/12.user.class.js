//4.9
/*
	Represents user entity on client-side.
*/

var IsIgnoredDefault = "", IgnoresYouDefault = "";

function User(id, login, room_id, room_is_permitted, ip, away_message, ban_reason, banned_by, nickname, settings, rights, status_title, status_color, is_ignored, ignores_you) {
	// Properties

	this.Id = id;
	this.Login = login;
	this.RoomId = room_id;
	this.RoomIsPermitted = room_is_permitted;

	this.SessionAddress = ip;
	this.AwayMessage = away_message;

	this.BanReason = ban_reason;
	this.BannedBy = banned_by;

	this.Nickname = nickname;

	this.Settings = settings;

	this.Rights = rights;
	this.StatusTitle = status_title;
	this.StatusColor = status_color;

	this.IsIgnored = 1 * is_ignored;
	this.IgnoresYou = 1 * ignores_you;
};

User.prototype.CheckSum = function() {
	var cs = 0;
	cs += CheckSum(this.RoomId);
	cs += CheckSum(this.RoomIsPermitted);

	cs += CheckSum(this.AwayMessage);

	cs += CheckSum(this.BanReason);
	cs += CheckSum(this.BannedBy);

	cs += this.Settings.CheckSum();

	cs += CheckSum(this.Nickname);

	cs += CheckSum(this.Rights);
	cs += CheckSum(this.StatusTitle);
	cs += CheckSum(this.StatusColor);

	cs += CheckSum(this.IsIgnored);
	cs += CheckSum(this.IgnoresYou);
//	DebugLine('User: '+this.Id+' sum: '+cs);

	return cs;
};

User.prototype.IsAdmin = function() {
	return this.Rights >= adminRights;
};

User.prototype.IsSuperAdmin = function() {
	return this.Rights > adminRights;
};

User.prototype.ToString = function(room) {
	var name = (this.Nickname ? this.Nickname : this.Login);

	var has_access = this.HasAccessTo(room);
	var s = this.NameToString(name, has_access);

	s += '<div class="UserInfo" style="display:none" onmouseover="Show(this);" onclick="HideDelayed();" onmouseout="Hide();" id="_' + this.Id + '">';
	s += '<ul><li> <a ' + voidHref + ' class="Close">x</a><a ' + voidHref + ' onclick="Info(' + this.Id + ')">Инфо</a>';

	if (this.Id != me.Id && me.RoomId == this.RoomId) {
		if (!has_access && me.RoomIsPermitted == 1 && this.RoomIsPermitted == 0) {
			s += '<li class="Grant"> <a ' + voidHref + ' onclick="AG(' + this.Id + ',1)">Впустить</a>';
		} else if (room.OwnerId == me.Id && this.Id != room.OwnerId) {
			s += '<li class="Deny"> <a ' + voidHref + ' onclick="AG(' + this.Id + ',0)">Закрыть доступ</a>';
		}
	}

	s += '<li> <a ' + voidHref + ' onclick="_(\'' + name + '\')">Обратиться</a>';
	s += '<li> <a ' + voidHref + ' onclick="AR(' + this.Id + ',\'' + name + '\')">Шёпотом</a>';
	s += '<li> <a ' + voidHref + ' onclick="AR(' + this.Id + ',\'' + name + '\',\'wakeup\')">Вейкап</a>';
	if (!me || this.Id != me.Id) {
		s += '<li> <a ' + voidHref + ' onclick="IG(' + this.Id + ',\'' + this.IsIgnored + '\')">' + (this.IsIgnored ? 'Убрать игнор' : 'В игнор') + '</a>';
	}
	if (me && me.Rights >= this.Rights) {
		if (me.IsAdmin() || me.Rights == keeperRights) {
			s += '<li> <a ' + voidHref + ' onclick="AR(' + this.Id + ',\'' + name + '\',\'kick\')">Выгнать</a>';
		}
		if ((me.IsAdmin() && me.Rights > this.Rights && this.Rights != keeperRights) || me.IsSuperAdmin()) {
			s += '<li> <a ' + voidHref + ' onclick="AR(' + this.Id + ',\'' + name + '\',\'ban\')">Забанить</a>';
			if (this.Login) {
				s += '<li class="Overlined"><span>' + this.Login + '</span>';
				if (this.SessionAddress) {
					s += '<li title=' + this.SessionAddress + ' class="IP">' + this.SessionAddress;
				}
			}
		}
	}
	s += '</ul></div>';

	return s;
};

User.prototype.NameToString = function(name, has_access) {
	var color = this.StatusColor;

	var className = "";
	var cl = 1;
	if (this.BannedBy > 0) {
		className = "Banned";
		cl = 0;
	} else if (this.AwayMessage) {
		className = "Away";
		cl = 0;
	}

	if (!has_access && me.RoomId == this.RoomId) {
		className = "Requestor";
	}
	if (this.IgnoresYou) {
		className += " IgnoresMe";
	}

	var title = name + (this.AwayMessage ? " отсутствует	&laquo;" + this.AwayMessage + "&raquo;" : "");

	var s = '<li><span' + (this.IsIgnored ? ' class="Ignored"' : '') + '><a ' + voidHref + ' onclick="SwitchVisibility(\'_' + this.Id + '\')" ';
	s += ' ' + (cl ? ' style="color:' + color + '"' : '') + ' class="' + className + '" alt="' + title + '" title="' + title + '">' + name + '</a></span><br>';
	return s;
};

User.prototype.DisplayedName = function() {
	return this.Nickname ? this.Nickname : this.Login;
};

User.prototype.HasAccessTo = function(room) {
	if (!room.IsInvitationRequired || room.OwnerId == this.Id) {
		return true;
	}
	if (this.RoomId == room.Id && this.RoomIsPermitted != 0) {
		return true;
	}
	return false;
};

function SetVisibility(id, state) {
	var e = id.style ?  id : $(id);
	if (e) {
		e.style.display = state ? '' : 'none';
	}
};

function SwitchVisibility(id) {
	var e = id.style ?  id : $(id);
	if (e.style.display == 'none') {
		Show(e);
	} else {
		HideDelayed();
	}
};

/* Helper methods */

var shownElement;
var hideTimer;
function Show(id) {
	if (shownElement) {
		if (shownElement != id) {
			HideDelayed();
		} else if (hideTimer) {
			clearTimeout(hideTimer);
			return;
		}
	}
	SetVisibility(id, true);
	shownElement = id;
};

function Hide() {
	hideTimer = setTimeout("HideDelayed()", 1000);
};

function HideDelayed() {
	SetVisibility(shownElement, false);
	shownElement = '';
};