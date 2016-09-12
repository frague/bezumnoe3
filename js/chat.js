import $ from 'jquery';
import {layoutConfigs} from './chat_layout';

import {ParamsBuilder, utils} from './utils';
import {settings} from './settings';
import {Collection} from './collection';
import {Confirm} from './confirm';
import {getCookie} from './cookie_helper';
import {WakeupsCollection} from './wakeups_collection';
import {User} from './user';
import {InitMenu} from './init';
import {Spoiler} from './spoiler';
import {Message} from './message';
import {Recepient} from './recepient';
import {pages} from './layouts';

import React from 'react';
import models from './models';
import {FlexFrame} from './flex_frame';
import {Room} from './room';
import {Tab, Tabs} from './tabs';

export var Chat = React.createClass({
  tabs: null,

  getInitialState() {
    this.history = [];
    this.historyPointer = 0;

    this.users = new Collection();
    this.rooms = new Collection();
    this.messages = new Collection();
    this.wakeups = new WakeupsCollection();
 
    this.newRoomId = null;
    this.messageType = null;

    return {
    // var Topic = $("#TopicContainer")[0];
    // var Status = $("#Status")[0];
    
    // this.options = options;

    // this.menu = null;

    // this.PongImg = $("#pong")[0];
    // this.pongImage = new Image();
    // this.pongImage.src = settings.imagesPath + 'pong.gif';

    // this.lastMessageId = null;
    // this.newRoomId = 0;
    // this.pingTimer = null;

    // this.historyContainer = $("#History")[0];
    // this.historySize = 100;
    // this.historyPointer = -1;

  
    // this.tiomeoutTimer;
    // this.busy = false;
    // this.requestSent = false;

    // this.newRoomTab;
    // this.messageType = '';

    // this.eng = "qwertyuiop[]asdfghjkl;'zxcvbnm,.QWERTYUIOP{}ASDFGHJKL\\:ZXCVBNM<>/&?@`~";
    // this.rus = "йцукенгшщзхъфывапролджэячсмитьбюЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ.?,\"ёЁ";

    // this.confirmation = new Confirm();
    // this.confirmation.Init('AlertContainer', 'AlertBlock');

    // // $('form[name=messageForm]').on('submit', () => {
    // //   this.send();
    // //   return false;
    // // });
    // this.HistoryGo(0);
    // this.ping();
      me: null,
      users: [],
      rooms: [],
      messages: [],
      wakeups: [],
      currentTab: null,

      layoutIndex: 1,
      displayedName: '%username%'
    };
  },

  componentDidMount() {
    this.tabs.add({
      name: 'Чат',
      render: () => 
        _.map(this.state.messages, (message) => <p key={message.id}>{message.text}</p>),
      isPrivate: false
    });

    let aLines = this.tabs.add({
      name: 'A-lines',
      render: () => 
        _.map(_.range(100), (n) => <p key={n}>AAAAAAAAAAAAAAAAAAAAAAAAAAAAAA</p>),
      switchToIt: false
    });
    aLines.addRecepient('a6452767', 'Vasya');
    aLines.addRecepient('a1124430', 'Petya');
    aLines.addRecepient('a9807098', 'Fedya');

    this.ping();
  },

  AddContainerLine(container, line) {
    container.innerHTML = line + "\n" + container.innerHTML;
  },

  // AM(text, message_id, author_id, author_name, to_user_id, to_user) {
  //   message_id = 1 * message_id;
  //   var tab = tabs.main;
  //   var renewTabs = false;
  //   if (to_user_id > 0) {
  //     tab = tabs.tabsCollection.Get(to_user_id);
  //     if (!tab) {
  //       tab = new Tab(to_user_id, to_user, 0, 1);
  //       tabs.Add(tab);
  //       renewTabs = true;
  //     }
  //   }

  //   if (tab && (!message_id || (message_id && tab.lastMessageId < message_id))) {
  //     text = Format(text, author_id, author_name);
  //     this.AddContainerLine(tab.RelatedDiv, text);
  //     if (message_id) {
  //       this.lastMessageId = message_id;
  //       tab.lastMessageId = message_id;
  //     }
  //     if (tab.Id != tabs.current.Id) {
  //       tab.UnreadMessages++;
  //       renewTabs = true;
  //     }
  //   }
  //   if (renewTabs) tabs.Print();
  // };

  // ClearMessages() {
  //   if (this.tabs.main) {
  //     this.tabs.main.RelatedDiv.innerHTML = '';
  //   }
  // },

  // ChangeRoom(id) {
  //   if (this.rooms && rooms.Get) {
  //     var room = this.rooms.Get(id);
  //     if (room && room.Enter) {
  //       room.Enter();
  //       this.MoveToRoom(id);
  //       this.printRooms();
  //     }
  //   }
  // },

  // printRooms() {
  //   var roomsContainer = $('#UsersContainer');
  //   roomsContainer.html('');
  //   this.rooms.render(roomsContainer[0], this.users, this.me);

  //   var container = $("#NewRoom")[0];
  //   if (!this.newRoomTab && this.me && this.me.Rights >= 11) { // Allowed to create rooms
  //     this.MakeNewRoomSpoiler(container);
  //   }
  //   utils.displayElement(container, this.rooms.Count() < 10);
  //   this.UpdateTopic();
  // },

  moveToRoom(id) {
    // MainTab.Clear();
    this.newRoomId = id;
    this.forcePing();
  },

  // MakeNewRoomSpoiler(container) {
  //   this.newRoomTab = new Spoiler(
  //     100, 'Создать комнату', 0, '', (tab) => new RoomLightweight().loadTemplate(tab, this.me.Id)
  //   );
  //   this.newRoomTab.ToString(container);
  // },

  // UpdateTopic() {
  //   if (this.CurrentRoom) {
  //     if (this.me && this.me.HasAccessTo(CurrentRoom)) {
  //       this.SetTopic(CurrentRoom.Topic, CurrentRoom.TopicAuthorId, CurrentRoom.TopicAuthorName, CurrentRoom.TopicLock);
  //     } else {
  //       this.SetTopic("Доступ в комнату ограничен. Ожидайте допуска.");
  //     }
  //   }
  // },

  // SetTopic(text, author_id, author_name, lock) {
  //   var topicMessage = text;
  //   if (MainTab) {
  //     var s = "";
  //     if (text) {
  //       s = "<div>" + (1 * lock ? "<em>x</em>" : "") + "&laquo;<strong>" + MakeSmiles(topicMessage) + "</strong>&raquo;";
  //       if (author_name) {
  //         s += ", ";
  //         if (author_id) {
  //           s += Format("#info#", author_id, author_name);
  //         } else {
  //           s += author_name;
  //         }
  //       }
  //       s += "</div>";
  //     }
  //     MainTab.TopicDiv.innerHTML = s;
  //     MainTab.TopicDiv.className = "TabTopic" + ((!author_id && !author_name) ? " Alert" : "");
  //   }

  //   document.title = "(" + this.users.Count() + ") " + (author_name ? "\"" : "") + StripTags(text) + (author_name ? "\", " + author_name : "");
  // },

  // addRecepient(id, name, type) {
  //   if (id === this.me.Id) {
  //     return;
  //   }
  //   var mainTab = this.tabs.main;
  //   if (mainTab) {
  //     if (type != this.messageType) {
  //       this.messageType = type;
  //       mainTab.recepients.Clear();
  //     }
  //     if (id && name) {
  //       mainTab.recepients.Add(new Recepient(id, name, false, this.deleteRecepient));
  //       if (this.tabs.current.Id !== this.tabs.main.Id) {
  //         mainTab.switchTo();
  //       }
  //     }
  //     this.renderRecepients();
  //   }
  // },

  // deleteRecepient(id) {
  //   if (this.tabs.main) {
  //     this.tabs.main.recepients.Delete(id);
  //     this.renderRecepients();
  //   }
  // },

  setMessageType(type, id = null, name = null) {
    // this.addRecepient(id, name, type);
  },

  Translit() {},

  HistoryGo() {},

  SwitchSmiles() {},

  // IG(id, state) {
  //   var s = new ParamsBuilder().add('user_id', id).add('state', state);
  //   $.post(settings.servicesPath + 'ignore.service.php', s.build())
  //     .then(() => this.Ignored);
  // },

  // Ignored(data) {
  //   var id = data.responseText;
  //   if (id) {
  //     var u = this.users.Get(Math.abs(id));
  //     if (u) {
  //       u.IsIgnored = (id > 0 ? 1 : "");
  //     }
  //   }
  //   this.printRooms();
  // },

  // // Grant room access
  // AG(id, state) {
  //   var s = ParamsBuilder().add('user_id', id).add('state', state);
  //   $.get(settings.servicesPath + 'room_user.service.php', s.build());
  // },

  // PingTimeout() {
  //   this.busy = false;
  // },

  ping(doCheck) {
    if (!this.busy) {
      var s = new ParamsBuilder();
      if (doCheck) s.add('SESSION_CHECK', this.options.sessionCheck);

      // _.each(
      //   this.state.rooms.Base,
      //   (room, id) => s.add('r' + id, room.CheckSum())
      // );

      // _.each(
      //   this.state.users.Base,
      //   (user, id) => s.add('u' + id, user.CheckSum())
      // );

      /* Messages */
      s.add('last_id', this.state.messages.LastId);

      /* Move to room */
      if (this.state.newRoomId) {
        s.add('room_id', this.state.newRoomId);
        this.state.newRoomId = null;
      };

      $.post(settings.servicesPath + 'pong.service.php', s.build())
        .then((response) => this.pong(response))
        .fail((jqXHR) => {
          if (jqXHR.status !== 408) {
            this.quit();
          }
        });

      this.state.requestSent = true;
      this.tiomeoutTimer = setTimeout(() => this.PingTimeout, 20000);
      this.state.busy = true;
    }
    this.pingTimer = setTimeout(() => this.ping(), 10000);
  },

  pong({myId, users, rooms, messages}) {
    this.state.busy = false;
    this.state.requestSent = false;
    clearTimeout(this.tiomeoutTimer);
    // this.PongImg.src = this.pongImage.src;

    // this.wakeups.Clear();

    _.each(
      users, 
      (userData) => {
        var user = new models.UserModel(...userData);
        if (user.id == myId) {
          this.state.me = user;
        }
        this.users.add(user);
      }
    );

    _.each(
      rooms,
      (roomData) => this.rooms.add(new models.RoomModel(...roomData))
    );

    _.each(
      messages,
      (message) => this.messages.add(new models.MessageModel(...message))
    );

    this.setState({
      users: this.users.base,
      rooms: this.rooms.base,
      messages: this.messages.base
    });

    // this.wakeups.render();

    if (!_.isEmpty(this.state.me)) {
      if (this.state.me.settings.Frameset != window,configIndex) {
        this.state.layoutIndex = this.state.me.settings.Frameset;
      }

      if (!this.menu) {
        // this.menu = InitMenu($("#MenuContainer")[0], this);
      }
      this.state.currentName = this.state.me.Nickname ? this.state.me.Nickname : this.state.me.Login;
    }
  },

  forcePing(doCheck) {
    if (!this.requestSent) {
      this.busy = false;
      clearTimeout(this.pingTimer);
      this.ping(doCheck);
    }
  },

  send() {
    let activeTab = this.tabs.getActiveTab();
    var recepients = activeTab.recepients.Gather();
    var textField = $('#Message');

    if (!recepients && !textField.val()) {
      return;
    }
    var s = new ParamsBuilder()
      .add('message', textField.val())
      .add('type', this.messageType)
      .add('recepients', recepients);

    $.post(settings.servicesPath + 'message.service.php', s.build())
      .then(() => this.received);

    if (!activeTab.props.isPrivate) {
      activeTab.recepients.Clear();
      this.messageType = null;
    }
    this.MessagesHistory = _.take(
      _.concat([textField.val()], this.MessagesHistory), 
      this.historySize
    );
    this.HistoryGo(-1);
    textField.val('');
  },
  
  // received(req) {
  //   console.log('Attempting to eval', req.responseText);
  //   // try {
  //   //   if (req.responseText) {
  //   //     eval(req.responseText);
  //   //   }
  //   // } catch (e) {
  //   // }
  //   this.forcePing();
  // },

  // HistoryGo(i) {
  //   if (i >= -1 && i < this.MessagesHistory.length) {
  //     this.historyPointer = i;
  //     this.historyContainer.innerHTML = 
  //       "История сообщений (" + (this.historyPointer + 1) + "/" + this.MessagesHistory.length + ")";
  //     var value = i >= 0 ? this.MessagesHistory[historyPointer] : "";
  //     $('#Message').val(value);
  //   }
  // },

  // Translit() {
  //   var textField = $('#Message');
  //   var val = textField.val();
  //   var out = '';

  //   for (var i = 0, l = val.length; i < l; i++) {
  //     s = val.charAt(i);
  //     var engIndex = this.eng.indexOf(s);
  //     if (engIndex >= 0) {
  //       s = this.rus.charAt(engIndex);
  //     } else {
  //       rusIndex = this.rus.indexOf(s);
  //       if (rusIndex >= 0) {
  //         s = this.eng.charAt(rusIndex);
  //       }
  //     }
  //     out += s;
  //   }
  //   textField.val(out);
  // },

  stopPings() {
    clearTimeout(this.pingTimer);
    clearTimeout(this.tiomeoutTimer);
  },

  quit() {
    this.stopPings();
    // this.confirmation.AlertType = true;
    // this.confirmation.Show("./", "Сессия завершена", "Ваша сессия в чате завершена. Повторите авторизацию для повторного входа в чат.", "", true);
  },

  // Kicked(reason) {
  //   this.StopPings();
  //   this.confirmation.AlertType = true;
  //   this.confirmation.Show("./", "Вас выгнали из чата", "Формулировка:<ul class='Kicked'>" + reason + "</ul>");
  // },

  // Banned(reason, admin, admin_id, till) {
  //   this.StopPings();
  //   this.confirmation.AlertType = true;
  //   var s = Format("Администратор #info# забанил вас " + (till ? "до " + till : ""), admin_id, admin);
  //   s += (reason ? " по причине <h4>&laquo;" + reason + "&raquo;</h4>" : "");
  //   s += "Пожалуйста, ознакомьтесь с <a href=/rules.php>правилами</a> чата.<br>До свидания.";
  //   this.confirmation.Show("/rules.php", "Пользователь забанен", s);
  // },

  // Forbidden(reason) {
  //   this.StopPings();
  //   this.confirmation.AlertType = true;
  //   this.confirmation.Show(".", "Доступ в чат закрыт", "Администратор закрыл для вас доступ в чат" + ($reason ? "<br>с формулировкой &laquo;" + reason + "&raquo;" : ""));
  // },

  // ChangeName() {
  //   this.confirmation.Show(SaveNicknameChanges, "Смена имени в чате", "Выберите имя:", new ChangeNickname(), true);
  // },

  // MessageForbiddenAlert() {
  //   this.confirmation.AlertType = true;
  //   this.confirmation.Show("", "Публикация сообщения невозможна!", "Публикация сообщений невозможна в приватных комнатах, если у вас нет туда допуска.");
  // },

  // ShowOptions() {
  //   var tab = this.tabs.tabsCollection.Get('menu');

  //   if (!tab) {
  //     var menuTab = new Tab('menu', 'Меню');
  //     this.tabs.Add(menuTab);
  //     menuTab.switchTo();
  //     this.tabs.Print();

  //     $(menuTab.RelatedDiv).load(
  //       '/options/menu.php',
  //       () => {
  //         initLayout(pages.menu, $('#MessagesContainer')[0]);
  //         $(menuTab.RelatedDiv).trigger('load');
  //       }
  //     );
  //   } else {
  //     tab.switchTo();
  //   }
  // },

  setActiveTab(tab) {
    this.setState({activeTab: tab});
  },

  renderRecepients() {
    let activeTab = this.state.activeTab;
    if (!activeTab) {
      return null;
    }

    let recepients = activeTab.getRecepients();
    console.log(recepients.base);

    let prefix = {
      'wakeup': 'Wakeup для ',
      'kick': 'Выгнать ',
      'ban': 'Забанить ',
      'me': 'О себе в третьем лице',
      'status': 'Установить статус',
      'topic': 'Установить тему',
      'locktopic': 'Установить и заблокировать тему',
      'unlocktopic': 'Установить и разблокировать тему',
      'away': 'Отойти',
      'quit': 'Выйти из чата'
    }[this.messageType];

    if (!prefix) {
      if (!recepients.count()) {
        this.messageType = null;
        return null;
      } else {
        prefix = 'Для';
      }
    }
    return <ul className='recepients'>
      <li key='prefix'>{prefix}</li>
      {recepients.render()}
    </ul>;
  },

  render() {
    var layout = layoutConfigs[this.state.layoutIndex];
    var {me} = this.state;
    return (
      <div>
        <FlexFrame key='users' dimensions={layout.users}>
          <div id='wakeups' />
          <ul id='users-container'>
            {_.map(
              this.state.rooms, 
              (room) => <Room 
                  key={room.id} 
                  users={this.state.users} 
                  me={this.state.me}
                  moveToRoom={this.moveToRoom}
                  {...room.data}
                />
            )}
          </ul>
          <div id='NewRoom' />
        </FlexFrame>
        <FlexFrame key='form' className='messages-form' dimensions={layout.form}>
          <h6>{me ? me.login : '%username%'}</h6>
          {this.renderRecepients()}
          <table>
            <tbody>
              <tr>
                <td>
                  <a onClick={() => this.setMessageType('me')}>me</a>
                </td>
                <td>
                  <input id='message' autoComplete='off' />
                </td>
                <td>
                  <button onClick={() => this.send()}>Отправить</button>
                </td>
              </tr>
            </tbody>
          </table>
          <div className='ServiceLinks'>
            <a onClick={this.SwitchSmiles()}>:)</a>
            <a onClick={this.Translit()}>qwe&harr;йцу</a>
            <a onClick={this.HistoryGo()}>&times;</a>
            <a onClick={this.HistoryGo(1)}>&laquo;</a>
            <span id='History'>
              История сообщений ({this.historyPointer}/{this.history.length})
            </span>
            <a onClick={this.HistoryGo(-1)}>&raquo;</a>
          </div>
        </FlexFrame>
        <FlexFrame key='messages' dimensions={layout.messages}>
          <Tabs 
            ref={(component) => this.tabs = component}
            setActiveTab={this.setActiveTab}
          />
        </FlexFrame>
        <FlexFrame key='status' dimensions={layout.status} />
      </div>
    );
  }
});
