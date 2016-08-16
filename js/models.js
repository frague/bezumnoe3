import _ from 'lodash';

class Model {
  get properties() {
    return [];
  }
  get data() {
    return _.pick(this, this.properties);
  }
  postprocess() {}
  constructor() {
    if (this.properties.length !== arguments.length) {
      return console.error('Unable to initialize instance: expected ' + this.properties.length + 
        ' arguments but ' + arguments.length + ' received!');
    }
    _.each(this.properties, (property, index) => {this[property] = arguments[index]});
    this.postprocess();
  }
};

class RoomModel extends Model {
  get properties() {
    return [
      'id', 'title', 'topic', 'topicLock', 'topicAuthorId', 'topicAuthorName',
      'isLocked', 'isByInvitation', 'ownerId'
    ];
  }
};

class MessageModel extends Model {
  get properties() {
    return [
      'id', 'roomId', 'userId', 'userName', 'toUserId', 'toUserName',
      'text', 'moment'
    ]
  }
};

class FontModel extends Model {
  get properties() {
    return [
      'color', 'size', 'face', 'isBold', 'isItalic', 'isUnderlined'
    ]
  }
};

class SettingsModel extends Model {
  get properties() {
    return [
      'status', 'ignoreColors', 'ignoreSizes', 'ignoreFonts', 'ignoreStyles',
      'receiveWakeups', 'frameset', 'font'
    ]
  }
  postprocess() {
    this.font = new FontModel(...this.font);
  }
};

class UserModel extends Model {
  get properties() {
    return [
      'id', 'login', 'roomId', 'isRoomPermitted', 'sessionAddress',
      'awayMessage', 'banReason', 'bannedBy', 'nickname', 'settings', 
      'statusRights', 'statusTitle', 'statusColor',
      'isIgnored', 'ignoresYou'
    ];
  }
  postprocess() {
    this.settings = new SettingsModel(...this.settings);
  }
};

export default {
  UserModel, RoomModel, MessageModel, FontModel, SettingsModel
};