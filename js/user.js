import _ from 'lodash';
import {utils} from './utils';
import {settings} from './settings';
import {Settings} from './user_settings';
import {Entity} from './entity';

/*
  Represents user entity on client-side.
*/

export class User extends Entity {
  constructor(id, login, room_id, room_is_permitted, ip, away_message, ban_reason, banned_by, 
    nickname, userSettings, rights, status_title, status_color, is_ignored, ignores_you) {
    super();

    this.Id = id;
    this.Login = login;
    this.RoomId = room_id;
    this.RoomIsPermitted = room_is_permitted;

    this.SessionAddress = ip;
    this.AwayMessage = away_message;

    this.BanReason = ban_reason;
    this.BannedBy = banned_by;

    this.Nickname = nickname;

    this.Settings = new Settings(...userSettings);

    this.Rights = rights;
    this.StatusTitle = status_title;
    this.StatusColor = status_color;

    this.IsIgnored = !!is_ignored;
    this.IgnoresYou = !!ignores_you;
  }

  CheckSum() {
    var cs = _.sum(_.invoke([
      this.RoomId, this.RoomIsPermitted, this.AwayMessage, this.BanReason,
      this.BannedBy, this.Nickname, this.Rights, this.StatusTitle, this.StatusColor
    ], this.checkSum));
    cs += this.Settings.CheckSum();
    cs += "" + this.IsIgnored + "" + this.IgnoresYou;
    return cs;
  }

  isAdmin() {
    return this.Rights >= settings.adminRights;
  }

  isSuperAdmin() {
    return this.Rights > settings.adminRights;
  }

  ToString(room, me) {
    var name = this.Nickname || this.Login,
      qname = utils.quotes(name),
      has_access = this.HasAccessTo(room),
      s = this.NameToString(name, has_access);

    s += '<div class="UserInfo" style="display:none" onmouseover="Show(this);" onclick="HideDelayed();" onmouseout="Hide();" id="_' + this.Id + '">';
    s += '<ul><li> <a ' + settings.voidHref + ' class="Close">x</a><a ' + settings.voidHref + ' onclick="Info(' + this.Id + ')">Инфо</a>';

    if (this.Id != me.Id && me.RoomId == this.RoomId) {
      if (!has_access && me.RoomIsPermitted == 1 && this.RoomIsPermitted == 0) {
        s += '<li class="Grant"> <a ' + settings.voidHref + ' onclick="AG(' + this.Id + ',1)">Впустить</a>';
      } else if (room.OwnerId == me.Id && this.Id != room.OwnerId) {
        s += '<li class="Deny"> <a ' + settings.voidHref + ' onclick="AG(' + this.Id + ',0)">Закрыть доступ</a>';
      }
    }

    s += '<li> <a ' + settings.voidHref + ' onclick="_(\'' + qname + '\')">Обратиться</a>';
    s += '<li> <a ' + settings.voidHref + ' onclick="AR(' + this.Id + ',\'' + qname + '\')">Шёпотом</a>';
    s += '<li> <a ' + settings.voidHref + ' onclick="AR(' + this.Id + ',\'' + qname + '\',\'wakeup\')">Вейкап</a>';
    if (!me || this.Id != me.Id) {
      s += '<li> <a ' + settings.voidHref + ' onclick="IG(' + this.Id + ',\'' + this.IsIgnored + '\')">' + (this.IsIgnored ? 'Убрать игнор' : 'В игнор') + '</a>';
    }
    if (me && me.Rights >= this.Rights) {
      if (me.isAdmin() || me.Rights == settingskeeperRights) {
        s += '<li> <a ' + settings.voidHref + ' onclick="AR(' + this.Id + ',\'' + qname + '\',\'kick\')">Выгнать</a>';
      }
      if ((me.isAdmin() && me.Rights > this.Rights && this.Rights != settings.keeperRights) || me.isSuperAdmin()) {
        s += '<li> <a ' + settings.voidHref + ' onclick="AR(' + this.Id + ',\'' + qname + '\',\'ban\')">Забанить</a>';
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
  }

  NameToString(name, has_access) {
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

    var title = utils.htmlQuotes(name) + (this.AwayMessage ? " отсутствует  &laquo;" + this.AwayMessage + "&raquo;" : "");

    var s = '<li><span' + (this.IsIgnored ? ' class="Ignored"' : '') + '><a ' + settings.voidHref + ' onclick="switchVisibility(\'_' + this.Id + '\')" ';
    s += ' ' + (cl ? ' style="color:' + color + '"' : '') + ' class="' + className + '" alt="' + title + '" title="' + title + '">' + name + '</a></span><br>';
    return s;
  }

  DisplayedName() {
    return this.Nickname ? this.Nickname : this.Login;
  }

  HasAccessTo(room) {
    if (!room.IsInvitationRequired || room.OwnerId == this.Id) {
      return true;
    }
    if (this.RoomId == room.Id && this.RoomIsPermitted != 0) {
      return true;
    }
    return false;
  }
};
/* Helper methods */

// var shownElement;
// var hideTimer;
// function Show(id) {
//   if (shownElement) {
//     if (shownElement != id) {
//       HideDelayed();
//     } else if (hideTimer) {
//       clearTimeout(hideTimer);
//       return;
//     }
//   }
//   utils.displayElement(id, true);
//   shownElement = id;
// };

// function Hide() {
//   hideTimer = setTimeout("HideDelayed()", 1000);
// };

// function HideDelayed() {
//   utils.displayElement(shownElement, false);
//   shownElement = '';
// };
