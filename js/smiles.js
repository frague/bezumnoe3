// Smile class

class Smile {
  constructor(src) {
    this.Id = src;
    this.token = "*" + src.substr(0, src.indexOf(".")) + "*";
    this.image = new Image();
    this.image.src = "/img/smiles/" + src;
  }

  ToString(holder) {
    var link = document.createElement("a");
    link.onclick = () => {
      _s(this.token);
      SwitchSmiles();
    };
    link.appendChild(this.image);
    holder.appendChild(link);
    holder.appendChild(document.createTextNode(" "));
  }
}

function GetSmilesContainer() {
  return $("#Smiles")[0] || {};
};

function SwitchSmiles() {
  var c = GetSmilesContainer();
  c.className = c.className == "On" ? "" : "On";
  AdjustDivs();
};

function InitSmiles(arr) {
  var c = GetSmilesContainer();
  var div = document.createElement("div");
  var sc = new Collection();
  for (var i = 0, l = arr.length; i < l; i++) {
    var s = new Smile(arr[i]);
    sc.Add(s);
  }
  sc.ToString(div);
  c.appendChild(div);
};
