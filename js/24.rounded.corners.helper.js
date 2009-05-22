//2.2
/* 
	Rounded div corners based on Alessandro Fulciniti's functionality
*/

function RoundCorners(el, bk, h){
	if (!el.tagName) {
		el = $(id);
	}
	if (!el) {
    	return;
	}

	// Create 3-cell table row
	var t = d.createElement("table");
	var tr = d.createElement("tr");
	var mainCell;
	for (var i = 0; i < 3; i++) {
		var td = d.createElement("td");
		if (i == 1) {
			td.className = "AlertsContainer";
			mainCell = td;
		} else {
			td.style.width = "50%";
		}
		tr.appendChild(td);
	}

	var c = new Array(4);
	for (var i = 0; i < 4; i++) {
	    c[i] = d.createElement("b");
    	c[i].style.display = "block";
	    c[i].style.height = h + "px";
    	c[i].style.fontSize = "1px";
    	c[i].style.background = "url(" + imagesPath + bk + ") no-repeat " + (i%2 ? "right" : "left") + " " + (-i * h) + "px";
    }
	
	c[0].appendChild(c[1]);
	c[2].appendChild(c[3]);
	mainCell.style.padding = "0";
	mainCell.insertBefore(c[0], mainCell.firstChild);
	var div = d.createElement("div");
	mainCell.appendChild(div);
	mainCell.appendChild(c[2]);

	if (d.all) {	// IE hack
		var tb = d.createElement("tbody");
		tb.appendChild(tr);
		t.appendChild(tb);
	} else {
		t.appendChild(tr);
	}
	el.appendChild(t);
	return div;
};