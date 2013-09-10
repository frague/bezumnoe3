function Feedback() {
	document.location = "mai" + "lto:" + "info" + "@" + "bezumnoe." + "ru";
};

var infoPopUp;
function Info(id) {
	infoPopUp = open("/user/" + id + ".html", "info", "width=550,height=600,toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=1");
}

/* Letterizing */
letterize = ['.Register h4', '.Forum h4', '.Gallery h4', '.Blogs h4', 'h1'];

$(document).ready(function() {
	$(letterize.join(',')).lettering();

	$("#auth_form").dialog({
		title: 'Авторизация в чате',
		autoOpen: false,
		height: 230,
		width: 420,
		modal: true,
		buttons: {
			"Авторизоваться": function() {
				$("form#auth").submit();
				$(this).dialog("close");
			},
			"Отмена": function() {
				$(this).dialog("close");
			}
		}
	});
	$(".submitter").keypress(function (e) {
		if (e.which == 13) {
			$("form#auth").submit();
		}
	});
	if (window.startup) {
		startup();
	}
});
