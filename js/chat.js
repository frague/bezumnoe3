import $ from 'jquery';
import {layoutConfigs} from './chat_layout';

import {ParamsBuilder, utils} from './utils';
import {settings} from './settings';
import {Collection} from './collection';
import {Confirm} from './confirm';
import {getCookie} from './cookie_helper';
import {WakeupsCollection} from './wakeups_collection';
import {User} from './user';
import {Room} from './room';
import {InitMenu} from './init';
import {Spoiler} from './spoiler';
import {Message} from './message';
import {Recepient} from './recepient';
import {pages} from './layouts';
import {Tab} from './tabs';

import {FlexFrame} from './flex_frame';
import React from 'react';

export var Chat = React.createClass({
  getInitialState() {
    return {
    // var Topic = $("#TopicContainer")[0];
    // var Status = $("#Status")[0];
    
    // this.tabs = tabs;
    // this.options = options;

    // this.me = {};
    // this.menu = null;

    // this.PongImg = $("#pong")[0];
    // this.pongImage = new Image();
    // this.pongImage.src = settings.imagesPath + 'pong.gif';

    // this.lastMessageId = null;
    // this.newRoomId = 0;
    // this.pingTimer = null;

    // this.MessagesHistory = [];
    // this.historyContainer = $("#History")[0];
    // this.historySize = 100;
    // this.historyPointer = -1;

    // this.users = new Collection();
    // this.rooms = new Collection();
    // this.wakeups = new WakeupsCollection();
  
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
      layoutIndex: 1,
      displayedName: '%username%'
    };
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

  // MoveToRoom(id) {
  //   MainTab.Clear();
  //   this.newRoomId = id;
  //   this.forcePing();
  // },

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

  // showRecepients() {
  //   if (this.tabs.current) {
  //     var prefix = {
  //       'wakeup': 'Wakeup для ',
  //       'kick': 'Выгнать ',
  //       'ban': 'Забанить ',
  //       'me': 'О себе в третьем лице',
  //       'status': 'Установить статус',
  //       'topic': 'Установить тему',
  //       'locktopic': 'Установить и заблокировать тему',
  //       'unlocktopic': 'Установить и разблокировать тему',
  //       'away': 'Отойти',
  //       'quit': 'Выйти из чата'
  //     }[this.messageType];

  //     var recepientsContainer = $('#RecepientsContainer')[0];
  //     if (!prefix) {
  //       if (!this.tabs.current.recepients.Count()) {
  //         this.messageType = null;
  //         recepientsContainer.innerHTML = '';
  //         return;
  //       }
  //       prefix = 'Для ';
  //     }
  //     recepientsContainer.innerHTML = prefix;
  //     this.tabs.current.recepients.render(recepientsContainer);
  //   }
  //   $('#Message').focus();
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
  //     this.showRecepients();
  //   }
  // },

  // deleteRecepient(id) {
  //   if (this.tabs.main) {
  //     this.tabs.main.recepients.Delete(id);
  //     this.showRecepients();
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

  // ping(doCheck) {
  //   this.CompareSessions();
  //   if (!this.busy) {
  //     var s = new ParamsBuilder();
  //     if (doCheck) s.add('SESSION_CHECK', this.options.sessionCheck);

  //     /* Rooms */
  //     _.each(
  //       this.rooms.Base,
  //       (room, id) => s.add('r' + id, room.CheckSum())
  //     );

  //     /* Users */
  //     _.each(
  //       this.users.Base,
  //       (user, id) => s.add('u' + id, user.CheckSum())
  //     );

  //     /* Messages */
  //     s.add('last_id', this.lastMessageId);

  //     /* Move to room */
  //     if (this.newRoomId) {
  //       s.add('room_id', this.newRoomId);
  //       this.newRoomId = null;
  //     };

  //     $.post(settings.servicesPath + 'pong.service.php', s.build())
  //       .then((response) => this.pong(response));

  //     this.requestSent = true;
  //     this.tiomeoutTimer = setTimeout(() => this.PingTimeout, 20000);
  //     this.busy = true;
  //   }
  //   this.pingTimer = setTimeout(() => this.ping(), 10000);
  // },

  // pong({users, rooms, messages}) {
  //   this.busy = false;
  //   this.requestSent = false;
  //   clearTimeout(this.tiomeoutTimer);
  //   this.PongImg.src = this.pongImage.src;

  //   this.wakeups.Clear();

  //   _.each(
  //     users, 
  //     (userData) => {
  //       var user = new User(...userData);
  //       if (user.Id == this.options.myId) {
  //         this.me = user;
  //       }
  //       this.users.Add(user);
  //     }
  //   );

  //   _.each(
  //     rooms,
  //     (roomData) => {
  //       var room = new Room(...roomData);
  //       if (room.Id == this.options.currentRoomId) {
  //         room.Enter();
  //       }
  //       this.rooms.Add(room);
  //     }
  //   );

  //   var renewTabs = _.reduce(
  //     messages,
  //     (result, message) => {
  //       var msg = new Message(...message);
  //       this.lastMessageId = msg.Id;
  //       return msg.render(this.tabs) || result;
  //     }, 
  //     false
  //   );

  //   // if (renewTabs) this.tabs.Print();

  //   this.printRooms();

  //   this.wakeups.render();

  //   if (!_.isEmpty(this.me)) {
  //     if (this.me.Settings.Frameset != window,configIndex) {
  //       window.configIndex = this.me.Settings.Frameset;
  //       _.result(window, 'onResize');
  //     }

  //     if (!this.menu) {
  //       this.menu = InitMenu($("#MenuContainer")[0], this);
  //     }

  //     var currentName = $("#CurrentName")[0];
  //     if (currentName) {
  //       var oldName = currentName.innerHTML;
  //       if (
  //         (this.me.Nickname && oldName != this.me.Nickname) || 
  //         (!this.me.Nickname && currentName.innerHTML != this.me.Nickname)
  //       ) {
  //         currentName.innerHTML = this.me.Nickname ? this.me.Nickname : this.me.Login;
  //       }
  //     }
  //   }
  // },

  // forcePing(doCheck) {
  //   if (!this.requestSent) {
  //     this.busy = false;
  //     clearTimeout(this.pingTimer);
  //     this.ping(doCheck);
  //   }
  // },

  // CompareSessions() {
  //   if (getCookie(this.options.sessionKey) != this.options.session) {
  //     this.Quit();
  //   }
  // },

  // send() {
  //   var recepients = this.tabs.current.recepients.Gather();
  //   var textField = $('#Message');

  //   if (!recepients && !textField.val()) {
  //     return;
  //   }
  //   var s = new ParamsBuilder()
  //     .add('message', textField.val())
  //     .add('type', this.messageType)
  //     .add('recepients', recepients);

  //   $.post(settings.servicesPath + 'message.service.php', s.build())
  //     .then(() => this.received);

  //   if (!this.tabs.current.IsPrivate) {
  //     this.tabs.current.recepients.Clear();
  //     this.messageType = '';
  //     this.showRecepients();
  //   }
  //   this.MessagesHistory = _.take(
  //     _.concat([textField.val()], this.MessagesHistory), 
  //     this.historySize
  //   );
  //   this.HistoryGo(-1);
  //   textField.val('');
  // },
  
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

  // StopPings() {
  //   clearTimeout(this.pingTimer);
  //   clearTimeout(this.tiomeoutTimer);
  // },

  // Quit() {
  //   this.StopPings();
  //   this.confirmation.AlertType = true;
  //   this.confirmation.Show("./", "Сессия завершена", "Ваша сессия в чате завершена. Повторите авторизацию для повторного входа в чат.", "", true);
  // },

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

  render() {
    var layout = layoutConfigs[this.state.layoutIndex];
    return (
      <div>
        <FlexFrame key='users' dimensions={layout.users}>
          <div id='Wakeups' />
          <ul id='UsersContainer' />
          <div id='NewRoom' />
        </FlexFrame>,
        <FlexFrame key='form' dimensions={layout.form}>
          <form name='messageForm'>
            <table>
              <tbody>
                <tr>
                  <td></td>
                  <td id='CurrentName' colSpan={2}>{this.state.displayedName}</td>
                </tr>
                <tr>
                  <td></td>
                  <td colSpan={2}>
                    <ul id='RecepientsContainer' />
                  </td>
                </tr>
                <tr>
                  <td>
                    <a onClick={this.setMessageType('me')}>me</a>
                  </td>
                  <td width='100%'>
                    <div id='Smiles'>
                      <input id='Message' autoComplete='off' />
                    </div>
                  </td><td>
                    <input type='image' alt='Отправить сообщение' src='/img/send_button.gif' />
                  </td>
                </tr>
                <tr>
                  <td></td>
                  <td className='ServiceLinks'>
                    <a onClick={this.SwitchSmiles()}>:)</a>
                    <a onClick={this.Translit()}>qwe&harr;йцу</a>
                    <a onClick={this.HistoryGo()}>&times;</a>
                    <a onClick={this.HistoryGo(1)}>&laquo;</a>
                    <span id='History'>История сообщений (0/0)</span>
                    <a onClick={this.HistoryGo(-1)}>&raquo;</a>
                    </td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </form>
        </FlexFrame>,
        <FlexFrame key='messages' dimensions={layout.messages}>
          <FlexFrame key='messagesContainer' dimensions={[0, 0, 0, 0]} />
        </FlexFrame>,
        <FlexFrame key='status' dimensions={layout.status} />
      </div>
    );
  }
});
