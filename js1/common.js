function Feedback() {
	document.location = "mai" + "lto:" + "info" + "@" + "bezumnoe." + "ru";
};

var infoPopUp;
function Info(id) {
	infoPopUp = open("/user/" + id + ".html", "info", "width=550,height=600,toolbar=0,location=0,directories=0,status=1,menubar=0,resizable=1");
}

/* Letterizing */
letterize = ['.Register h4', '.Forum h4', '.Gallery h4', '.Blogs h4', 'h1'];

Modernizr.load([
{
	load: ['/js1/jquery/jquery-1.6.4.min.js', '/js1/jquery/jquery-ui-1.8.16.custom.min.js']
},
{
    test: Modernizr.fontface,
    yep: ['/js1/jquery/jquery.lettering-0.6.1.min.js', '/css/lettering.css'],
    callback: function (url, result, key) {
    	this.result = result;
    },
    complete: function () {
    	if (this.result && window.jQuery) {
    		
			$(document).ready(function() {
				$(letterize.join(',')).lettering();

				$( "#auth_form" ).dialog({
					title: 'Авторизация в чате',
					autoOpen: false,
					height: 230,
					width: 420,
					modal: true,
					buttons: {
						"Авторизоваться": function() {
							$( "#auth" ).submit();
							$( this ).dialog( "close" );
						},
						"Отмена": function() {
							$( this ).dialog( "close" );
						}
					}
				});
				startup();
			});
    	}
  	}
}
]);