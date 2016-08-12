import $ from 'jquery';
import _ from 'lodash';
import {Confirm} from './confirm';
import {FlexFrame} from './flex_frame';
import {Tabs, Tab} from './tabs';
import {Chat} from './chat';
import {layoutConfigs} from './chat_layout';

import React from 'react';
import ReactDOM from 'react-dom';

var frames;
var co = new Confirm();
var MainTab;
window.configIndex = 0;
export var pages = {
  inside: {
    containers: [
      ['#Users', 0, 100],
      ['#MessageForm', 500],
      ['#Messages', 500, 100],
      '#MessagesContainer',
      ['#Status', 660],
      '#AlertContainer'
    ],
    onResize() {
      this.frames[5].Replace(-1, -1, this.winSize.width, this.winSize.height);
      layoutConfigs[window.configIndex].call(this);
      $('body').removeClass().addClass('Layout' + window.configIndex);
    },
    onLoad(options) {
      this.tabs = new Tabs($("#Messages")[0], $("#MessagesContainer")[0]);
      var chatTab = new Tab(1, "Чат", true);
      this.tabs.Add(chatTab);
      chatTab.switchTo();
      this.tabs.main = chatTab;
      window.chat = new Chat(this.tabs, options);
    }
  },
  info: {
    containers: ['#InfoContainer', '#InfoContent'],
    onResize: function() {
      this.frames[0].Replace(10, 10, this.winSize.width - 20, this.winSize.height - 20);
      this.frames[1].Replace(-1, -1, -1, this.frames[0].height - 40);
    },
    onLoad: function() {
      this.tabs = new Tabs($('#InfoContainer')[0], $('#InfoContent')[0]);
      this.tabs.Add(new Tab(1, 'Инфо', 1), $('#Info')[0]);
      this.tabs.Print();
    }
  },
  menu: {
    containers: [
      ['#OptionsContainer'],
      '#OptionsContent',
      '#AlertContainer'
    ],
    onResize: function() {
      this.frames[0].Replace(10, 10, this.winSize.width - 20, this.winSize.height - 16);
      this.frames[1].Replace(-1, -1, -1, this.frames[0].height - 30);
      this.frames[2].Replace(-1, -1, this.winSize.width, this.winSize.height);
    },
    onLoad: function() {
      var me = window.me;
      if (me) {
        this.UploadFrame = $('#uploadFrame')[0];

        this.tabs = new Tabs($('#OptionsContainer')[0], $('#OptionsContent')[0]);
        var profileTab = new Tab(1, 'Личные данные', true);
        this.tabs.Add(profileTab);

        this.tabs.Add(new Tab(
          2, 'Настройки', true, '',
          function (tab) {
            new Settings().loadTemplate(tab, me.Id);
          }
        ));

        this.tabs.Add(new Tab(
          3, 'Журнал', true, '',
          function (tab) {
            new JournalsManager().loadTemplate(tab, me.Id);
          }
        ));

        this.tabs.Add(new Tab(
          5, 'Сообщения', true, '',
          function (tab) {
            new Wakeups().loadTemplate(tab, me.Id);
          }
        ));

        if (me.Rights >= this.adminRights) {
          this.tabs.Add(new Tab(
            6, 'Пользователи', true, '',
            function (tab) {
              new Userman().loadTemplate(tab,me.Id);
            }
          ));

          this.MainTab = new Tab(
            7, 'Администрирование', 1, '',
            function (tab) {
              new AdminOptions().loadTemplate(tab, me.Id);
            });
          this.tabs.Add(MainTab);
        } else {
          profileTab.switchTo();
        }
        this.tabs.Print();
        new Profile().loadTemplate(profileTab, me.Id);
      }
    }
  },
  wakeup: {
    containers: [
      ['#WakeupContainer', 400],
      ['#WakeupReply', 400]
    ],
    onResize: function() {
      this.frames[0].Replace(10, 40, this.winSize.width - 20, this.winSize.height - 50 - offset);
      this.frames[1].Replace(10, this.winSize.height - replyFormHeight, this.winSize.width - 20, replyFormHeight - 10);
    }
  }
};

export function initLayout(layout, container, options = {}) {
  var frames = _.reduce(
    layout.containers, 
    (result, params) => {
      let [id, width, height] = _.flatten([params, null, null]);
      result.push(<FlexFrame id={id} width={width} height={height} key={id} />);
      return result;
    },
    [<FlexFrame id='windows' key='windows' />]
  );

  return ReactDOM.render(
    <FlexFrame id='windows' key='window'>{layout.containers}</FlexFrame>,
    document.getElementById('inside')
  );
};

export function initChat() {
  return ReactDOM.render(
    (<Chat />),
    document.getElementById('content')
  );
}