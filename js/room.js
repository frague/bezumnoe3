//3.2
/*
  Represents room entity on client-side.
*/

class Room {
  constructor(id, title, topic, topic_lock, topic_author_id, topic_author_name, is_locked, is_by_invitation, owner_id) {

    this.Id = id;
    this.Title = title;
    this.Topic = topic;
    this.TopicLock = topic_lock;
    this.TopicAuthorId = topic_author_id;
    this.TopicAuthorName = topic_author_name;
    this.IsLocked = is_locked;
    this.IsInvitationRequired = is_by_invitation;
    this.OwnerId = owner_id;
    this.isCurrent = false;
  }

  Enter() {
    this.isCurrent = true;
  }

  ToString() {
    var s = "<li class='roomBox" + (this.isCurrent ? " Current" : "") + "'>";
    var title = this.Title.length < 16 ? this.Title : this.Title.substr(0, 16) + "...";
    if (this.isCurrent) {
      s += "<strong class='" + this.MakeCSS() + "' title='" + this.Title + "'>" + title + "</strong>";
    } else {
      s += "<a " + voidHref + " onclick=\"ChangeRoom('" + this.Id + "')\" class='" + this.MakeCSS() + "' title='" + this.Title + "'>" + title + "</a>";
    }

    var inside = 0;
    var t = '<ul class=\"Users\">';
    var requestors = "";

    for (var id in users.Base) {
      var user = users.Base[id];
      if (user && user.RoomId == this.Id) {
        var str = user.ToString(this);
        if (user.HasAccessTo(this) || me.RoomId != this.Id) {
          t += str;
          inside++;
        } else {
          requestors += str;
        }
      }
    }
    if (requestors) {
      t += "<div class=\"Requestors\">Ожидают допуска:</div>" + requestors;
    }
    t += "</ul></li>";
    s = s + (inside ? ("&nbsp;<span class='Count'>(" + inside + ")</span>") : "") + (inside || requestors ? t : "");
    return s;
  }

  Gather(sel) {

    var opt = d.createElement("option");
    opt.value = this.Id;
    opt.text = this.Title;


    try {
      sel.add(opt, null); // standards compliant; doesn't work in IE
    } catch (ex) {
      sel.add(opt); // IE only
    }
  }

  MakeCSS() {
    var cl = (this.IsInvitationRequired ? "Private" : "Usual");
    cl += (this.isCurrent ? " Current" : "");
    cl += (this.IsLocked ? " Locked " : "");
    return cl;
  }

  CheckSum() {
    var cs = CheckSum(this.OwnerId);
    cs+= CheckSum(this.Title);
    cs+= CheckSum(this.Topic);
    cs+= CheckSum(this.TopicLock);
    cs+= CheckSum(this.TopicAuthorId);
    cs+= CheckSum(this.IsLocked);
    cs+= CheckSum(this.IsInvitationRequired);
    //DebugLine("Room: " + this.Id + " sum: "+cs);
    return cs;
  }
}
  /* Room DTO class */
class RoomLightweight extends OptionsBase {
  constructor() {
    super();

    this.fields = new Array("NEW_ROOM", "IS_PRIVATE", "IS_LOCKED");
    this.ServicePath = servicesPath + "room.service.php";
    this.Template = "add_room";
    this.ClassName = "AddRoom";
  }

  requestCallback(responseText) {
    if (responseText) {
      this.SetRoomStatus(responseText);
    } else {
      this.SetRoomStatus("");
      this.Clear();
      this.Bind();
      this.Tab.Display(false);
      PrintRooms();
    }
  }

  request(params, callback) {};

  Save(callback) {
    var params = this.Gather();
    if (this.NEW_ROOM) {
      this.BaseRequest(params, callback);
    } else {
      this.SetRoomStatus("Введите название");
    }
  }

  SetRoomStatus(text) {
    this.FindRelatedControls();
    var st = this.inputs["RoomStatus"];
    if (st) {
      st.innerHTML = text;
    }
  }

  TemplateLoaded(req) {
    this.TemplateBaseLoaded(req);

    displayElement("AdminOnly", me && me.Rights >= adminRights);

    this.AssignTabTo("linkAdd");
    BindEnterTo(this.inputs["NEW_ROOM"], this.inputs["linkAdd"]);
  }
}

/* Room lightweight link actions */

function AddRoom(a) {
  if (a.Tab) {
    a.Tab.AddRoom.Save();
  }
};

/* Room Data Transfer Object */

function rdto(id, title, is_deleted, is_locked) {
  this.fields = ["Id", "Title", "IsDeleted", "IsLocked", "IsInvitationRequired"];
  this.Init(arguments);
};

rdto.prototype = new EditableDTO();
