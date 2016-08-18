import _ from 'lodash';
import {settings} from './settings';
import {Entity} from './entity';
import {OptionsBase} from './options';
import {EditableDTO} from './dto';

import React from 'react';
import {User} from './user';
import {utils} from './utils';

/*
  Represents room entity on client-side.
*/

export var Room = React.createClass({
  propTypes: {
    moveToRoom: React.PropTypes.func
  },

  render() {
    var {id, title, me, users, moveToRoom} = this.props;
    var roomUsers = _.filter(users, (user) => user.roomId == id);

    return (
      <ul className={utils.classNames({current: me && me.roomId == id})}>
        <a onClick={() => moveToRoom(id)}>
          {title}
          <sup>{roomUsers.length}</sup>
        </a>
        <ul>
          {roomUsers.map((user) => <li key={user.id}><User {...user.data} /></li>)}
        </ul>
      </ul>
    );
  },

  Gather(sel) {

    var opt = document.createElement("option");
    opt.value = this.Id;
    opt.text = this.Title;


    try {
      sel.add(opt, null); // standards compliant; doesn't work in IE
    } catch (ex) {
      sel.add(opt); // IE only
    }
  },

  MakeCSS() {
    var cl = (this.IsInvitationRequired ? "Private" : "Usual");
    cl += (this.isCurrent ? " Current" : "");
    cl += (this.IsLocked ? " Locked " : "");
    return cl;
  },

  CheckSum() {
    return _.sum(_.invoke([
      this.OwnerId, this.Title, this.Topic, this.TopicLock, this.TopicAuthorId,
      this.IsLocked, this.IsInvitationRequired,
    ], this.checkSum));
  }
});

//   /* Room DTO class */
// class RoomLightweight extends OptionsBase {
//   constructor() {
//     super();

//     this.fields = new Array("NEW_ROOM", "IS_PRIVATE", "IS_LOCKED");
//     this.ServicePath = settings.servicesPath + "room.service.php";
//     this.Template = "add_room";
//     this.ClassName = "AddRoom";
//   }

//   requestCallback(responseText) {
//     if (responseText) {
//       this.SetRoomStatus(responseText);
//     } else {
//       this.SetRoomStatus("");
//       this.Clear();
//       this.Bind();
//       this.Tab.Display(false);
//       printRooms();
//     }
//   }

//   request(params, callback) {};

//   Save(callback) {
//     var params = this.Gather();
//     if (this.NEW_ROOM) {
//       this.BaseRequest(params, callback);
//     } else {
//       this.SetRoomStatus("Введите название");
//     }
//   }

//   SetRoomStatus(text) {
//     this.FindRelatedControls();
//     var st = this.inputs["RoomStatus"];
//     if (st) {
//       st.innerHTML = text;
//     }
//   }

//   TemplateLoaded(req) {
//     this.TemplateBaseLoaded(req);

//     utils.displayElement("AdminOnly", me && me.Rights >= adminRights);

//     this.AssignTabTo("linkAdd");
//     BindEnterTo(this.inputs["NEW_ROOM"], this.inputs["linkAdd"]);
//   }
// });

// /* Room lightweight link actions */

// function AddRoom(a) {
//   if (a.Tab) {
//     a.Tab.AddRoom.Save();
//   }
// };

// /* Room Data Transfer Object */

// class rdto extends EditableDTO {
//   constructor(id, title, is_deleted, is_locked) {
//     super(arguments);
//     this.fields = ["Id", "Title", "IsDeleted", "IsLocked", "IsInvitationRequired"];
//     this.Init(arguments);
//   }
// }
