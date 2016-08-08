import {MenuItem, MenuItemsCollection} from './menu';
import {settings} from './settings';

/* 
  Chat properties initialization 
*/

export function InitMenu(div, chat) {
  var menu = new MenuItemsCollection(true);
  var main = new MenuItem('Команды');

  main.children.Add(new MenuItem('Отойти&nbsp;(Away)', () => chat.setMessageType('away')));
  main.children.Add(new MenuItem('Сменить статус', () => chat.setMessageType('status')));
  main.children.Add(new MenuItem('Сменить никнейм', chat.ChangeName));

  if (chat.me.Rights >= settings.topicRights) {
    var topic = new MenuItem('Сменить тему', () => chat.setMessageType('topic'));
    if (chat.me.Rights >= settings.adminRights) {
      topic.children.Add(new MenuItem('С блокировкой', () => chat.setMessageType('locktopic')));
      topic.children.Add(new MenuItem('Разблокировать', () => chat.setMessageType('unlocktopic')));
    }
    main.children.Add(topic);
  }

  main.children.Add(new MenuItem('<b>Меню</b>', 'ShowOptions'));
  main.children.Add(new MenuItem('Выход из чата', () => chat.setMessageType('quit')));

  menu.Add(main);
  menu.render(div);
  return menu;
};

// function OnLoad() {
//   utils.displayElement(alerts.element, false);
//   co.Init('AlertContainer', 'AlertBlock');
//   if (window.Pong) {
//     Ping();
//   }
//   if (window.OpenReplyForm) {
//     OpenReplyForm();
//   }
// };
