//1.2
/*
	Helps in JS code debugging. Displays debugging information
	in pop-up window.
*/

var debugWin;

function DebugLine(line) {
	if (!debug) {
		return;
	}
    if (!debugWin || !debugWin.document) {
		debugWin = window.open('','debug');
		debugWin.document.open();
		debugWin.document.writeln('<link rel=stylesheet type=text/css href=/3/css/debug.css>');
    }
	debugWin.document.writeln('<p>' + line);
};

function PropertiesOf(o) {
	var s = "";
	var l = 0;
	for (p in o) {
		s += p + "=" + o[p] + "		";
		if (l++ == 3) {
			l = 0;
			s += "\n";
		}
	}
	return s;
};