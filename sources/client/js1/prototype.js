function Chat(tabs) {
    var Topic = $("#TopicContainer")[0];
    var Status = $("#Status")[0];
    var PongImg = $("#pong")[0];
    var pongImage = new Image();
    pongImage.src = imagesPath + 'pong.gif';


    var topicMessage = '';
    var lastMessageId = -1;
    var showRooms = 0;
    var newRoomId = 0;
    var pingTimer;

    /* Pong: Messages, Rooms, Users, Topic */

    this.pong = function(responseText) {
        busy = 0;
        requestSent = 0;
        clearTimeout(tiomeoutTimer);
        PongImg.src = pongImage.src;

        wakeups.Clear();

        try {
            eval(responseText);
            if (showRooms) {
                showRooms = 0;
                PrintRooms();
            }
        } catch (e) {
            DebugLine(e.description);
            DebugLine(responseText);
        }

        PrintWakeups();

        if (me && me.Settings.Frameset != configIndex) {
            configIndex = me.Settings.Frameset;
            _.result(window, 'onResize');
        }

        var currentName = $('#CurrentName');
        if (currentName) {
            var oldName = currentName.text();
            if ((me.Nickname && oldName != me.Nickname) || (!me.Nickname && currentName.innerHTML != me.Nickname)) {
                currentName.text(me.Nickname ? me.Nickname : me.Login);
            }
        }
    };

    var tiomeoutTimer;
    function PingTimeout() {
        busy = 0;
    };

    var busy = 0;
    var requestSent = 0;
    this.ping = function(doCheck) {
        CompareSessions();
        if (!busy) {
            var s = new ParamsBuilder();
            if (doCheck) s.add('SESSION_CHECK', SessionCheck);

            /* Rooms */
            _.each(
                rooms.Base,
                function(room, id) {
                    s.add('r' + id, room.CheckSum());
                }
            );

            /* Users */
            _.each(
                users.Base,
                function(user, id) {
                    s.add('u' + id, user.CheckSum());
                }
            );

            /* Messages */
            s.add('last_id', lastMessageId);

            /* Move to room */
            if (newRoomId) {
                s.add('room_id', newRoomId);
                newRoomId = 0;
            };

            $.post(servicesPath + 'pong.service.php', s.build())
                .done(this.pong.bind(this));

            requestSent = 1;
            tiomeoutTimer = setTimeout(PingTimeout, 20000);
            busy = 1;
        }
        pingTimer = setTimeout(this.ping.bind(this), 10000);
    };

    this.forcePing = function(doCheck) {
        if (!requestSent) {
            busy = 0;
            clearTimeout(pingTimer);
            this.ping(doCheck);
        }
    };

    function CompareSessions() {
        cookieSession = getCookie(SessionKey);
        if (cookieSession != Session) {
            Quit();
        }
    };

    this.send = function() {
        var recepients = tabs.current.recepients.Gather(),
            textField = $('#Message');
        if (!recepients && !textField.val()) {
            return;
        }
        var s = new ParamsBuilder();
        s.add('message', textField.val());
        s.add('type', messageType);
        s.add('recepients', recepients);

        $.post(servicesPath + 'message.service.php', s.build())
            .done(this.received.bind(this));

        if (!tabs.current.IsPrivate) {
            tabs.current.recepients.Clear();
            messageType = '';
            ShowRecepients();
        }
        ArrayInsertFirst(MessagesHistory, textField.val(), historySize);
        HistoryGo(-1);

        textField.val('');
        return false;
    };
    $('form[name=messageForm]').on('submit', this.send.bind(this));

    this.received = function(req) {
        try {
            if (req.responseText) {
                eval(req.responseText);
            }
        } catch (e) {
        }
        this.forcePing();
    };


    /* Alerts & Confirmations */

    function StopPings() {
        clearTimeout(pingTimer);
        clearTimeout(tiomeoutTimer);
    };

    function Quit() {
        StopPings();
        co.AlertType = true;
        co.Show("./", "Сессия завершена", "Ваша сессия в чате завершена. Повторите авторизацию для повторного входа в чат.", "", true);
    };

    function Kicked(reason) {
        StopPings();
        co.AlertType = true;
        co.Show("./", "Вас выгнали из чата", "Формулировка:<ul class='Kicked'>" + reason + "</ul>");
    };

    function Banned(reason, admin, admin_id, till) {
        StopPings();
        co.AlertType = true;
        var s = Format("Администратор #info# забанил вас " + (till ? "до " + till : ""), admin_id, admin);
        s += (reason ? " по причине <h4>&laquo;" + reason + "&raquo;</h4>" : "");
        s += "Пожалуйста, ознакомьтесь с <a href=/rules.php>правилами</a> чата.<br>До свидания.";
        co.Show("/rules.php", "Пользователь забанен", s);
    };

    function Forbidden(reason) {
        StopPings();
        co.AlertType = true;
        co.Show(".", "Доступ в чат закрыт", "Администратор закрыл для вас доступ в чат" + ($reason ? "<br>с формулировкой &laquo;" + reason + "&raquo;" : ""));
    };

    function ChangeName() {
        co.Show(SaveNicknameChanges, "Смена имени в чате", "Выберите имя:", new ChangeNickname(), true);
    };

    function MessageForbiddenAlert() {
        co.AlertType = true;
        co.Show("", "Публикация сообщения невозможна!", "Публикация сообщений невозможна в приватных комнатах, если у вас нет туда допуска.");
    };

    this.initMenu = function(div) {
        var menu = new MenuItemsCollection(true),
            mainItem = new MenuItem(1, 'Команды');

        mainItem.SubItems.Items.Add(new MenuItem(3, 'Отойти (Away)', _.partial(MI, 'away')));
        mainItem.SubItems.Items.Add(new MenuItem(4, 'Сменить статус', _.partial(MI, 'status')));

        mainItem.SubItems.Items.Add(new MenuItem(5, 'Сменить никнейм', ChangeName));
        if (me && me.Rights >= topicRights) {
            var t = new MenuItem(6, 'Сменить тему', _.partial(MI, 'topic'));
            if (me.Rights >= adminRights) {
                t.SubItems.Items.Add(new MenuItem(1, 'С блокировкой', _.partial(MI, 'locktopic')));
                t.SubItems.Items.Add(new MenuItem(2, 'Разблокировать', _.partial(MI, 'unlocktopic')));
            }
            mainItem.SubItems.Items.Add(t);
        }
        mainItem.SubItems.Items.Add(new MenuItem(7, '<b>Меню</b>', ShowOptions));
        mainItem.SubItems.Items.Add(new MenuItem(8, 'Выход из чата', _.partial(MI, 'quit')));

        menu.Items.Add(mainItem);
        menu.Create(div);
    };

    this.ping();
}

/* Messages */

function addLine(container, line) {
    container.innerHTML = line + "\n" + container.innerHTML;
};

function AM(text, message_id, author_id, author_name, to_user_id, to_user) {
    message_id = 1 * message_id;
    var tab = tabs.main;
    var renewTabs = false;
    if (to_user_id > 0) {
        tab = tabs.tabsCollection.Get(to_user_id);
        if (!tab) {
            tab = new Tab(to_user_id, to_user, 0, 1);
            tabs.Add(tab);
            renewTabs = true;
        }
    }

    if (tab && (!message_id || (message_id && tab.lastMessageId < message_id))) {
        text = Format(text, author_id, author_name);
        addLine(tab.RelatedDiv, text);
        if (message_id) {
            lastMessageId = message_id;
            tab.lastMessageId = message_id;
        }
        if (tab.Id != tabs.current.Id) {
            tab.UnreadMessages++;
            renewTabs = true;
        }
    }
    if (renewTabs) tabs.Print();
};

function ClearMessages() {
    if (MainTab) {
        MainTab.RelatedDiv.innerHTML = "";
    }
};

/* Rooms */

var newRoomTab;
function PrintRooms() {
    $('#UsersContainer').html(rooms.ToString());

    var container = $("#NewRoom")[0];
    if (!newRoomTab && me && me.Rights >= 11) { // Allowed to create rooms
        MakeNewRoomSpoiler(container);
    }
    displayElement(container, rooms.Count() < 10);
    UpdateTopic();
};

function MoveToRoom(id) {
    MainTab.Clear();
    newRoomId = id;
    this.forcePing();
};

function MakeNewRoomSpoiler(container) {
    newRoomTab = new Spoiler(100, "Создать комнату", 0, "", function(tab){new RoomLightweight().loadTemplate(tab, me.Id)});
    newRoomTab.ToString(container);
};

/* Topic */

function UpdateTopic() {
    if (CurrentRoom) {
        if (me && me.HasAccessTo(CurrentRoom)) {
            SetTopic(CurrentRoom.Topic, CurrentRoom.TopicAuthorId, CurrentRoom.TopicAuthorName, CurrentRoom.TopicLock);
        } else {
            SetTopic("Доступ в комнату ограничен. Ожидайте допуска.");
        }
    }
};

function SetTopic(text, author_id, author_name, lock) {
    topicMessage = text;
    if (MainTab) {
        var s = "";
        if (text) {
            s = "<div>" + (1 * lock ? "<em>x</em>" : "") + "&laquo;<strong>" + MakeSmiles(topicMessage) + "</strong>&raquo;";
            if (author_name) {
                s += ", ";
                if (author_id) {
                    s += Format("#info#", author_id, author_name);
                } else {
                    s += author_name;
                }
            }
            s += "</div>";
        }
        MainTab.TopicDiv.innerHTML = s;
        MainTab.TopicDiv.className = "TabTopic" + ((!author_id && !author_name) ? " Alert" : "");
    }

    document.title = "(" + users.Count() + ") " + (author_name ? "\"" : "") + StripTags(text) + (author_name ? "\", " + author_name : "");
};


/* Recepients */

var messageType = '';
function ShowRecepients() {
    if (tabs.current) {
        var rec = tabs.current.recepients.ToString(),
            recepients = $('#RecepientsContainer');

        recepients.html(rec);
        if (rec) {
            var prefix;
            if (tabs.current.Id == tabs.main.Id) {
                try {
                    prefix = {
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
                        }[messageType];
                } catch (e) {
                    prefix = 'Для ';
                }
            }
            recepients.html(prefix + rec);
        } else {
            messageType = '';
        }
    }
    $('#Message').focus();
};

// Add recepient
function AR(id, name, type) {
    if (id == me.Id) {
        return;
    };
    if (tabs.main) {
        if (type != messageType) {
            messageType = type;
            tabs.main.recepients.Clear();
        }
        if (id && name) {
            tabs.main.recepients.Add(new Recepient(id, name));
            if (tabs.current.Id != tabs.main.Id) {
                tabs.main.switchTo();
            }
        }
        ShowRecepients();
    }
};

// Delete recepient
function DR(id) {
    if (tabs.main) {
        tabs.main.recepients.Delete(id);
        ShowRecepients();
    }
};

// Menu item
function MI(type) {
    AR(-1, ' ', type);
};

function IG(id, state) {
    var s = new ParamsBuilder();
    s.add('user_id', id);
    s.add('state', state);
    $.post(servicesPath + 'ignore.service.php', s.build())
        .done(Ignored);
};

function Ignored(data) {
    var id = data.responseText;
    if (id) {
        var u = users.Get(Math.abs(id));
        if (u) {
            u.IsIgnored = (id > 0 ? 1 : "");
        }
    }
    PrintRooms();
};

// Grant room access
function AG(id, state) {
    var s = ParamsBuilder();
    s.add('user_id', id);
    s.add('state', state);
    $.post(servicesPath + 'room_user.service.php', s.build());
};

var MessagesHistory = [];
var historySize = 100;
var historyPointer = -1;
function HistoryGo(i) {
    if (i >= -1 && i < MessagesHistory.length) {
        historyPointer = i;
        $('#History').text('История сообщений (' + (historyPointer + 1) + '/' + MessagesHistory.length + ')');
        $('#Message').val(i >= 0 ? MessagesHistory[historyPointer] : '');
    }
};
HistoryGo(0);

var eng = "qwertyuiop[]asdfghjkl;'zxcvbnm,.QWERTYUIOP{}ASDFGHJKL\\:ZXCVBNM<>/&?@`~";
var rus = "йцукенгшщзхъфывапролджэячсмитьбюЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ.?,\"ёЁ";

function Translit() {
    var textField = $('#Message'),
        val = textField.val(),
        out = '';

    for (i = 0, l = val.length; i < l; i++) {
        s = val.charAt(i);
        engIndex = eng.indexOf(s);
        if (engIndex >= 0) {
            s = rus.charAt(engIndex);
        } else {
            rusIndex = rus.indexOf(s);
            if (rusIndex >= 0) {
                s = eng.charAt(rusIndex);
            }
        }
        out += s;
    }
    textField.val(out);
};

