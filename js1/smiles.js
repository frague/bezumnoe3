function GetSmilesContainer() {
	c = $("#Smiles")[0];
	if (!c) {
		return;
	}
	return c;
};

function SwitchSmiles() {
	c = GetSmilesContainer();
	if (!c) {
		return;
	}
	c.className = c.className == "On" ? "" : "On";
	AdjustDivs();
	return 1;
};


function InitSmiles(arr) {
	c = GetSmilesContainer();
	if (!c) {
		return;
	}
	div = d.createElement("div");
	sc = new Collection();
	for (var i = 0, l = arr.length; i < l; i++) {
		var s = new Smile(arr[i]);
		sc.Add(s);
	}
	sc.ToString(div);
	c.appendChild(div);
};


// Smile class

function Smile(src) {
	this.Id = src;
	this.Token = "*" + src.substr(0, src.indexOf(".")) + "*";
	this.Rendered = new Image();
	this.Rendered.src = "/img/smiles/" + src;
};

Smile.prototype.ToString = function(holder, index) {
	var a = d.createElement("a");
	a.href = voidLink;
	a.Obj = this;
	a.onclick = function() {_s(this.Obj.Token);SwitchSmiles()};
	a.appendChild(this.Rendered);
	holder.appendChild(a);
	holder.appendChild(d.createTextNode(" "));
};