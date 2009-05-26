//1.8
/* 
	Chat properties initialization 
*/

var users = new Collection();
var rooms = new Collection();
var recepients = new Collection();
var co = new Confirm();

// Below values to be updated with 
// values received from server 

var CurrentRoomId = 1;
var me = "";


// Create Pop & Push methods For Array if not supported

function ArrayPop(a) {
	var o = a[a.length - 1];
	a.length--;
	return o;
};

function ArrayPush(a, p) {
	a[a.length] = p;
	return a.length;
};

// OnLoad actions

var menuInitilized = 0;
function InitMenu(div) {
	var menu = new MenuItemsCollection(true);
	var main = new MenuItem(1, "�������");

/*	main.SubItems.Items.Add(new MenuItem(1, "/me ���������", "MI('me')"));
		var w = new MenuItem(2, "������");
		w.SubItems.Items.Add(new MenuItem(1, "�����...", "", 1));
		main.SubItems.Items.Add(w);*/
	main.SubItems.Items.Add(new MenuItem(3, "������&nbsp;(Away)", "MI('away')"));
	main.SubItems.Items.Add(new MenuItem(4, "������� ������", "MI('status')"));

	main.SubItems.Items.Add(new MenuItem(5, "������� �������", "ChangeName()"));
	if (me.Rights >= topicRights) {
		var t = new MenuItem(6, "������� ����", "MI('topic')");
		if (me.Rights >= adminRights) {
			t.SubItems.Items.Add(new MenuItem(1, "� �����������", "MI('locktopic')"));
			t.SubItems.Items.Add(new MenuItem(2, "��������������", "MI('unlocktopic')"));
		}
		main.SubItems.Items.Add(t);
	}
	main.SubItems.Items.Add(new MenuItem(7, "<b>����</b>", "ShowOptions()"));
	main.SubItems.Items.Add(new MenuItem(8, "����� �� ����", "MI('quit')"));

	menu.Items.Add(main);
	menu.Create(div);
	menuInitilized = 1;
};

function OnLoad() {
	DisplayElement(alerts.element, false);
	co.Init("AlertContainer", "AlertBlock");
	if (window.Pong) {
		Ping();
	}
};