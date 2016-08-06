import {MenuItem, MenuItemsCollection} from './menu';
import {settings} from './settings';
//2.0
/* 
  Chat properties initialization 
*/


// Below values to be updated with 
// values received from server 

export function InitMenu(div, chat) {
  var menu = new MenuItemsCollection(true);
  var main = new MenuItem('Команды');

  main.children.Add(new MenuItem('Отойти&nbsp;(Away)', () => chat.MI('away')));
  main.children.Add(new MenuItem('Сменить статус', () => chat.MI('status')));
  main.children.Add(new MenuItem('Сменить никнейм', chat.ChangeName));

  if (chat.me.Rights >= settings.topicRights) {
    var topic = new MenuItem('Сменить тему', () => chat.MI('topic'));
    if (chat.me.Rights >= settings.adminRights) {
      topic.children.Add(new MenuItem('С блокировкой', () => chat.MI('locktopic')));
      topic.children.Add(new MenuItem('Разблокировать', () => chat.MI('unlocktopic')));
    }
    main.children.Add(topic);
  }

  main.children.Add(new MenuItem('<b>Меню</b>', 'ShowOptions'));
  main.children.Add(new MenuItem('Выход из чата', () => chat.MI('quit')));

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
