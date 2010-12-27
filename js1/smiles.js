function GetSmilesContainer() {
	c = $("Smiles");
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
	return 1;
};


function InitSmiles(arr) {
	c = GetSmilesContainer();
	if (!c) {
		return;
	}
	div = d.createElement("div");
	sc = new Collection();
	sc.BulkAdd(arr);
	sc.ToString(div);
	c.appendChild(div);
};


// Smile class

function Smile(src) {
	this.Token = "*" + src.substr(0, src.indexOf(".")) + "*";
	this.Rendered = new Image();
	this.Rendered.src = "/img/smiles/" + src;
};

Smile.prototype.ToString = function(holder, index) {
	var a = d.createElement("a");
	a.href = voidLink;
	a.Obj = this;
	a.onclick = function() {__(this.Obj.Token);SwitchSmiles()};
	a.appendChild(this.Rendered);
	holder.appendChild(a);
};