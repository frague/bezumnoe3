//2.4
/*
	Help with binding events to controls.
*/

function EnterHandler(e, el) {
	if(window.event) {		// IE
		keynum = e.keyCode;
	} else if (e.which) {	// Netscape/Firefox/Opera
		keynum = e.which;
	}

	if (keynum == 13 && el.Submitter) {
		DoClick(el.Submitter);
	}
};

function BindEnterTo(el, click_to) {
	if (el) {
		el.Submitter = click_to;
		el.onkeypress = function(e) {EnterHandler(e,this)};
	}
};

function DoClick(el) {
	if (el) {
		if (el.click) {
			el.click();
		} else if (el.onclick) {
			el.onclick();
		}
	}
};