import {initChat} from './layouts';

window.initChat = () => initChat();

export var layoutConfigs = [
  {
    status: [0, 0, 0, 30],
    users: [0, 40, 200, 0],
    form: [210, 40, 0, 100],
    messages: [210, 160, 0, 0]
  },
  {
    status: [0, 0, 0, 30],
    users: [-200, 40, 0, 0],
    form: [0, 40, -210, 100],
    messages: [0, 160, -210, 0]
  }
];

// function addMessageText(text, erase) {
//   var textForm = $("#Message");
//   textForm.val((erase ? '' : textForm.val()) + text).focus();
// };

// function insertName(text, erase) {
//   addMessageText(text + ", ", erase);
// };

// function __(el) {
//   if (el.hasChildNodes && el.childNodes[0].hasChildNodes && el.childNodes[0].childNodes[0]) {
//     el = el.childNodes[0];
//   }
//   insertName(el.innerText || el.textContent);
// };