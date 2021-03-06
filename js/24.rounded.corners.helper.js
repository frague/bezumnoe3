//3.1
/*
    Rounded div corners based on CSS3
*/

function RoundCorners(el, color, css) {
    if (!el.tagName) {
        el = $(GetElement(id))[0];
    }
    if (!el) {
        return;
    }

    var div = d.createElement("div");
    div.className = "RoundedCorners AlertsContainer" + (css ? " " + css : "");
    if (color) {
        div.style.backgroundColor = color;
    }
    el.appendChild(div);
    return div;
};
