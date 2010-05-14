d = document;
t = d.getElementById("bb");
thick = 3;

function line(x, y, x1, y1, color) {
	if (x > x1) {
		x = x + x1;
		x1 = x - x1;
		x = x - x1;
	}
	if (y > y1) {
		y = y + y1;
		y1 = y - y1;
		y = y - y1;
	}

	div = d.createElement("div");
	div.style.left = x + "px";
	div.style.top = y + "px";
	div.style.width = (x1 - x) + "px";
	div.style.height = (y1 - y) + "px";
	if (color) {
		div.style.backgroundColor = color;
	}
	div.inerHTML = "&nbsp;";
	t.appendChild(div);
};

function hl(x, y, w, color) {
	line(x, y, x + w, y + thick, color);
};

function vl(x, y, w, color) {
	line(x, y, x + thick, y + w, color);
};

function b(x, y, text, color) {
	span = d.createElement("span");
	span.innerHTML = text;
	span.style.left = x + "px";
	span.style.top = y + "px";
	if (color) {
		span.style.backgroundColor = color;
	}
	t.appendChild(span);
};

function relation(id1, id2, type) {
	this.From = arr[0];
	this.To = arr[1];
	this.Type = arr[2];
};

function user(arr) {
	this.Id = arr[0];
	this.Login = arr[1];
	this.Generation = arr[2];
};


users = [];
generations = [];
maxGeneration = 0;

for (item in u) {
	user1 = new user(u[item]);
	users[user1.Id] = user1;
	a = generations[user1.Generation];
	if (!a) {
		a = [];
	}
	a[a.length] = user1;
	generations[user1.Generation] = a;
	maxGeneration = maxGeneration > user1.Generation ? maxGeneration : user1.Generation;
}

for (i = 0; i < maxGeneration; i++) {
	gen = generations[i];
	for (y = 0; y < gen.length; y++) {
		b(i * 160, y * 25, gen[y].Login, "orange");
	}
};

/*line(100, 120, 300, 450, "cyan");
hl(120, 50, 300, "red");
vl(160, 50, 200);
b(200, 200, "Damn", "orange");*/
