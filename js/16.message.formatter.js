//1.6
/*
	Replaces #pvt#, #info# and #add# chunks with
	proper links
*/

function MakePrivateLink(id, name) {
	s = "<a " + voidHref + " onclick=\"AR(" + id + ",'" + StrongHtmlQuotes(Slash(name)) + "')\">#</a>";
//	alert(s);
	return s;
};

function MakeLink(name) {
	return "<a " + voidHref + " onclick=\"__(this)\">" + StrongHtmlQuotes(name) + "</a>";
};

function MakeInfoLink(id, name) {
	return "<a " + voidHref + " onclick=\"Info('" + id + "')\">" + StrongHtmlQuotes(name) + "</a>";
};

function GetUserStyle(id) {
	if (users) {
		var user = users.Get(id);
		if (user && user.Settings.Font && user.Settings.Font.ToCSS) {
			return "style='" + user.Settings.Font.ToCSS(me) + "'";
		}
	}
	return "";
}

function Format(text, person_id, person_name) {
	text = text.replace("#style#", GetUserStyle(person_id));
	text = text.replace("#info#", MakeInfoLink(person_id, person_name));
	text = text.replace("#pvt#", MakePrivateLink(person_id, person_name));
	text = text.replace("#add#", MakeLink(person_name));
	return text;
};
