var UsersDiv = $("#UsersContainer")[0];
var Topic = $("#TopicContainer")[0];
var Messages = $("#MessagesContainer")[0];
var Status = $("#Status")[0];
var Recepients = $("#RecepientsContainer")[0];
var PongImg = $("#pong")[0];
var pongImage = new Image(); pongImage.src = imagesPath + 'pong.gif';

var textField = $("#Message")[0];
var historyContainer = $("#History")[0];

var topicMessage = '';
var lastMessageId = -1;
var showRooms = 0;
var newRoomId = 0;
var pingTimer;

var MessagesHistory = [];
var historySize = 100;
var historyPointer = -1;

/* Messages */

function AddContainerLine(container, line) {
	container.innerHTML = line + "\n" + container.innerHTML;
};

function AM(text, message_id, author_id, author_name, to_user_id, to_user) {
	message_id = 1 * message_id;
	var tab = MainTab;
	var renew_tabs = 0;
	if (to_user_id > 0) {
		tab = tabs.tabsCollection.Get(to_user_id);
		if (!tab) {
			tab = new Tab(to_user_id, to_user, 0, 1);
			tabs.Add(tab);
			renew_tabs = 1;
		}
	}

	if (tab && (!message_id || (message_id && tab.lastMessageId < message_id))) {
		text = Format(text, author_id, author_name);
		AddContainerLine(tab.RelatedDiv, text);
		if (message_id) {
			lastMessageId = message_id;
			tab.lastMessageId = message_id;
		}
		if (tab.Id != CurrentTab.Id) {
			tab.UnreadMessages++;
			renew_tabs = 1;
		}
	}
	if (renew_tabs) {
		tabs.Print();
	}
};

function ClearMessages() {
	if (MainTab) {
		MainTab.RelatedDiv.innerHTML = "";
	}
};

/* Rooms */

var newRoomTab;
function PrintRooms() {
	UsersDiv.innerHTML = rooms.ToString();

	var container = $("#NewRoom")[0];
	if (!newRoomTab && me && me.Rights >= 11) {	// Allowed to create rooms
		MakeNewRoomSpoiler(container);
	}
	DisplayElement(container, rooms.Count() < 10);
	UpdateTopic();
};

function MoveToRoom(id) {
	MainTab.Clear();
	newRoomId = id;
	ForcePing();
};

function MakeNewRoomSpoiler(container) {
	newRoomTab = new Spoiler(100, "Создать комнату", 0, "", function(tab){new RoomLightweight().LoadTemplate(tab, me.Id)});
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
	document.title = (author_name ? "\"" : "") + StripTags(text) + (author_name ? "\", " + author_name : "");
};


/* Recepients */

var messageType = "";
function ShowRecepients() {
	if (CurrentTab) {
		Recepients.innerHTML = CurrentTab.recepients.ToString();
		if (Recepients.innerHTML) {
			var prefix = "Для ";
			if (CurrentTab.Id == MainTab.Id) {
				switch (messageType) {
					case "wakeup":
						prefix = "Wakeup для ";
						break;
					case "kick":
						prefix = "Выгнать ";
						break;
					case "ban":
						prefix = "Забанить ";
						break;
					case "me":
						prefix = "О себе в третьем лице";
						break;
					case "status":
						prefix = "Установить статус";
						break;
					case "topic":
						prefix = "Установить тему";
						break;
					case "locktopic":
						prefix = "Установить и заблокировать тему";
						break;
					case "unlocktopic":
						prefix = "Установить и разблокировать тему";
						break;
					case "away":
						prefix = "Отойти";
						break;
					case "quit":
						prefix = "Выйти из чата";
						break;
				}
			}
			Recepients.innerHTML = prefix + Recepients.innerHTML;
		} else {
			messageType = "";
		}
	}
	textField.focus();
};

// Add recepient
function AR(id, name, type) {
	if (id == me.Id) {
		return;
	}
	if (MainTab) {
		if (type != messageType) {
			messageType = type;
			MainTab.recepients.Clear();
		}
		if (id && name) {
			MainTab.recepients.Add(new Recepient(id, name));
			if (CurrentTab.Id != MainTab.Id) {
				SwitchToTab(MainTab.Id);
			}
		}
		ShowRecepients();
	}
};

// Delete recepient
function DR(id) {
	if (MainTab) {
		MainTab.recepients.Delete(id);
		ShowRecepients();
	}
};

// Menu item
function MI(type, id, name) {
	if (!id) {
		id = -1;
		name = " ";
	}
	AR(id, name, type);
};

function IG(id, state) {
    var s = MakeParametersPair("user_id", id);
    s += MakeParametersPair("state", state);
	sendRequest(servicesPath + "ignore.service.php", Ignored, s);
};

function Ignored(req) {
	var id = req.responseText;
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
    var s = MakeParametersPair("user_id", id);
    s += MakeParametersPair("state", state);
	sendRequest(servicesPath + "room_user.service.php", "", s);
};

/* Pong: Messages, Rooms, Users, Topic */

function ForcePing(do_check) {
	if (!requestSent) {
		busy = 0;
		clearTimeout(pingTimer);
		Ping(do_check);
	}
};

function Pong(responseText) {
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
		AdjustDivs();
	}

	if (!menuInitilized && me.Login) {
		InitMenu($("#MenuContainer")[0]);
	}

	var currentName = $("#CurrentName")[0];
	if (currentName) {
		var oldName = currentName.innerHTML;
		if ((me.Nickname && oldName != me.Nickname) || (!me.Nickname && currentName.innerHTML != me.Nickname)) {
			currentName.innerHTML = me.Nickname ? me.Nickname : me.Login;
		}
	}
};

var tiomeoutTimer;
function PingTimeout() {
	busy = 0;
//	AddContainerLine(Status, "Ping time out. Re-requesting.")
};

var busy = 0;
var requestSent = 0;
function Ping(do_check) {
	CompareSessions();
	if (!busy) {
		var s = '';
		if (do_check) {
			s += MakeParametersPair("SESSION_CHECK", SessionCheck);
		}

		/* Rooms */
		for (var id in rooms.Base) {
			s += MakeParametersPair("r" + id, rooms.Base[id].CheckSum());
		}
		/* Users */
		for (var uid in users.Base) {
			s += MakeParametersPair("u" + uid, users.Base[uid].CheckSum());
		}

        /* Messages */
        s += MakeParametersPair("last_id", lastMessageId);

        /* Move to room */
        if (newRoomId) {
        	s += MakeParametersPair("room_id", newRoomId);
        	newRoomId = 0;
        }

		sendRequest(servicesPath + "pong.service.php", Pong, s);
		requestSent = 1;
		tiomeoutTimer = setTimeout("PingTimeout()", 20000);
		busy = 1;
	}
	pingTimer = setTimeout("Ping()", 10000);
};

function CompareSessions() {
	cookieSession = getCookie(SessionKey);
	if (cookieSession != Session) {
		Quit();
	}
};

function Send(button) {
	var recepients = CurrentTab.recepients.Gather();
	if (!recepients && !textField.value) {
		return;
	}
	var s = MakeParametersPair("message", textField.value);
	s += MakeParametersPair("type", messageType);
	s += MakeParametersPair("recepients", recepients);

	sendRequest(servicesPath + "message.service.php", Received, s);
	if (!CurrentTab.IsPrivate) {
		CurrentTab.recepients.Clear();
		messageType = "";
		ShowRecepients();
	}
	ArrayInsertFirst(MessagesHistory, textField.value, historySize);
	HistoryGo(-1);

	textField.value = "";
};

function HistoryGo(i) {
	if (i >= -1 && i < MessagesHistory.length) {
		historyPointer = i;
		historyContainer.innerHTML = "История сообщений (" + (historyPointer + 1) + "/" + MessagesHistory.length + ")";
		var value = i >= 0 ? MessagesHistory[historyPointer] : "";
		textField.value = value;
	}
};

function Received(req) {
	try {
		if (req.responseText) {
			eval(req.responseText);
		}
	} catch (e) {
	}
	ForcePing();
};

var eng = "qwertyuiop[]asdfghjkl;'zxcvbnm,.QWERTYUIOP{}ASDFGHJKL\\:ZXCVBNM<>/&?@`~";
var rus = "йцукенгшщзхъфывапролджэячсмитьбюЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ.?,\"ёЁ";

function Translit() {
  	var val = textField.value, out = "";

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
	textField.value = out;
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