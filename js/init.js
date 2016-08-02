//2.0
/* 
  Chat properties initialization 
*/


// Below values to be updated with 
// values received from server 

function InitMenu(div) {
  var menu = new MenuItemsCollection(true);
  var main = new MenuItem('Команды');

  main.children.Add(new MenuItem('Отойти&nbsp;(Away)', 'MI(\'away\')'));
  main.children.Add(new MenuItem('Сменить статус', 'MI(\'status\')'));
  main.children.Add(new MenuItem('Сменить никнейм', window.chat.ChangeName));

  if (me.Rights >= topicRights) {
    var topic = new MenuItem('Сменить тему', 'MI(\'topic\')');
    if (me.Rights >= adminRights) {
      topic.children.Add(new MenuItem('С блокировкой', 'MI(\'locktopic\')'));
      topic.children.Add(new MenuItem('Разблокировать', 'MI(\'unlocktopic\')'));
    }
    main.children.Add(topic);
  }

  main.children.Add(new MenuItem('<b>Меню</b>', ShowOptions));
  main.children.Add(new MenuItem('Выход из чата', 'MI(\'quit\')'));

  menu.Add(main);
  menu.Create(div);
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
