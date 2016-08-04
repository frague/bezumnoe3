import {initLayout, pages} from './layouts';

window.initLayout = (options) => initLayout(pages.inside, window, options);

export var layoutConfigs = [
  function() {
    this.frames[4].Replace(10, 10, this.winSize.width - 20, 28);
    this.frames[0].Replace(10, this.frames[4].y + this.frames[4].height + 10, 150, this.winSize.height - 60);
    this.frames[1].Replace(10 + this.frames[0].x + this.frames[0].width, this.frames[0].y, this.winSize.width - 20 - this.frames[0].x - this.frames[0].width, -1);

    this.frames[2].Replace(
      this.frames[1].x,
      this.frames[1].y + this.frames[1].height + 10,
      this.frames[1].width + 2,
      this.winSize.height - 80 - this.frames[1].height);

    this.frames[3].Replace(-1, -1, -1, this.frames[2].height - 40);
  },
  function() {
    this.frames[4].Replace(10, 10, this.winSize.width - 20, 26);
    this.frames[0].Replace(this.winSize.width - 160, this.frames[4].y + this.frames[4].height + 10, 150, this.winSize.height - 60);
    this.frames[1].Replace(10, this.frames[0].y, this.winSize.width - 30 - this.frames[0].width, -1);

    this.frames[2].Replace(
      this.frames[1].x,
      this.frames[1].y + this.frames[1].height + 10,
      this.frames[1].width + 2,
      this.winSize.height - 80 - this.frames[1].height);

    this.frames[3].Replace(-1, -1, -1, this.frames[2].height - 40);
  },
  function() {
    this.frames[4].Replace(10, 10, this.winSize.width - 20, 28);
    this.frames[0].Replace(10, this.frames[4].y + this.frames[4].height + 10, 150, this.winSize.height - this.frames[4].height - 30);

    this.frames[2].Replace(
      10 + this.frames[0].x + this.frames[0].width,
      this.frames[0].y,
      this.winSize.width - this.frames[0].width - 30,
      this.winSize.height - 80 - this.frames[1].height);

    this.frames[3].Replace(-1, -1, -1, this.frames[2].height - 40);

    this.frames[1].Replace(this.frames[2].x, this.winSize.height - 110, this.winSize.width - 20 - this.frames[0].x - this.frames[0].width, 100);
  },
  function() {
    this.frames[4].Replace(10, 10, this.winSize.width - 20, 28);

    this.frames[2].Replace(
      10,
      this.frames[4].y + this.frames[4].height + 10,
      this.winSize.width - this.frames[0].width - 28,
      this.winSize.height - 70 - this.frames[1].height);

    this.frames[3].Replace(-1, -1, -1, this.frames[2].height - 40);

    this.frames[0].Replace(this.winSize.width - 160, this.frames[2].y, 150, this.winSize.height - this.frames[4].height - 30);
    this.frames[1].Replace(this.frames[2].x, this.winSize.height - 110, this.winSize.width - this.frames[0].width - 30, 100);
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