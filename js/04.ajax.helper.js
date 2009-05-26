//1.5

function sendRequest(url, callback, postData, obj) {
	var req = createXMLHTTPObject();
	if (!req) {
		return;
	}
	var method = postData ? "POST" : "GET";
	req.open(method,url,true);
	req.setRequestHeader('User-Agent','XMLHTTP/1.0');
	if (postData) {
		req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	};
	req.onreadystatechange = function () {
		if (req.readyState != 4) {
			return;
		}
		try {
			if (req.status != 200 && req.status != 304) {
				return;
			}
			if (callback) {
				// Callback passed as parameter
				callback(req, obj);
			} else if (obj && obj.TemplateLoaded) {
				// Template render callback
				obj.TemplateLoaded(req);
			}
		} catch (e) {
			DebugLine("Exception: " + e.Description);
		}
	};
	if (req.readyState == 4) {
		return;
	}
	req.send(postData);
	return req;
};

var XMLHttpFactories = [
	function () {return new XMLHttpRequest()},
	function () {return new ActiveXObject("Msxml2.XMLHTTP")},
	function () {return new ActiveXObject("Msxml3.XMLHTTP")},
	function () {return new ActiveXObject("Microsoft.XMLHTTP")}
];

function createXMLHTTPObject() {
	var xmlhttp = false;
	for (var i = 0; i < XMLHttpFactories.length; i++) {
		try {
			xmlhttp = XMLHttpFactories[i]();
		}
		catch (e) {
			continue;
		}
		break;
	}
	return xmlhttp;
};

function handleRequest(req) {
	try {
		eval(req.responseText);
		return;
	} catch(e) {
		return;
	}
	eval(req.responseText);
};