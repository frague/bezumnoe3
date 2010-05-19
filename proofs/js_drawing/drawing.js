d = document;
t = d.getElementById("bb");
thick = 3;
gap = 5;
genWidth = 400;

function dot(x, y, color) {
	this.x = x;
	this.y = y;
	this.color = color;
}

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

function b(x, y, u, color) {
	span = d.createElement("span");
	span.innerHTML = u.Login + ", " + u.Id;
//	span.innerHTML = u.Login;
	span.style.left = x + "px";
	span.style.top = y + "px";
	if (color) {
		span.style.backgroundColor = color;
	}
	t.appendChild(span);
};

function relation(arr) {
	this.From = arr[0];
	this.To = arr[1];
	this.Type = arr[2];
};

function user(arr) {
	this.Id = arr[0];
	this.Login = arr[1];
	this.Generation = arr[2];
};

function AppendSubArray(arr, key, value) {
	a = arr[key];
	if (!a) {
		a = [];
	}
	a[a.length] = value;
	arr[key] = a;
};


users = [];
generations = [];
maxGeneration = 0;

for (i in u) {
	user1 = new user(u[i]);
	users[user1.Id] = user1;
	AppendSubArray(generations, user1.Generation, user1);
	maxGeneration = maxGeneration > user1.Generation ? maxGeneration : user1.Generation;
}

for (i = 0; i < maxGeneration; i++) {
	gen = generations[i];
	for (y = 0; y < gen.length; y++) {
		gen[y].x = i * genWidth;
		gen[y].y = y * 20;
		b(gen[y].x, gen[y].y, gen[y], "orange");
	}
};

relations = [];
userRelations = [];

for (i in r) {
	rel1 = new relation(r[i]);
	relations[relations.length] = rel1;
	AppendSubArray(userRelations, rel1.From, rel1);
}

exists = [];

function MarkRelation(id1, id2) {
	exists[id1 + "_" + id2] = 1;
	exists[id2 + "_" + id1] = 1;
};

function MarkRelations(arr) {
	for (i = 0, l = arr.length; i < l - 1; i++) {
		for (k = i + 1; k < l; k++) {
			MarkRelation(arr[i], arr[k]);
		}
	}
};

function RelationExists(rel) {
	return (exists[rel.From + "_" + rel.To] == 1);
};

function AddGenLevel(arr, generation) {
	arr[generation] = arr[generation] ? arr[generation] + 1 : 1;
};

function CheckLess(old, v1, v2) {
	if (v1 < v2) {
		return v1 < old ? v1 : old;
	} else {
		return v2 < old ? v2 : old;
	}
};

function CheckMore(old, v1, v2) {
	if (v1 < v2) {
		return v2 > old ? v2 : old;
	} else {
		return v1 > old ? v1 : old;
	}
};

lefts = [];
rights = [];
parentsLinks = [];
recommendedColors = [];

index = 0;
colors = ["red", "green", "blue", "orange", "brown", "darkblue", "darkorange", "purple"];

for (kk in generations) {
	for (i in generations[kk]) {
		user1 = generations[kk][i];
/*		if (user1.Id != 6106 && user1.Id != 3801) {
			continue;
		}
*/
		left = 10 + (lefts[user1.Generation] ? gap * lefts[user1.Generation] : 0);
		right = 100 + (rights[user1.Generation] ? gap * rights[user1.Generation] : 0);
		color = recommendedColors[user1.Id] ? recommendedColors[user1.Id] : colors[(index++) % 8];

		flagLeft = false;
		flagRight = false;
		parents = [];
		parentId = 0;
		hasBrothers = false;

		linked = [user1.Id];

		rightTop = user1.y;
		rightBottom = user1.y;

		leftTop = user1.y;
		leftBottom = user1.y;

		for (k in userRelations[user1.Id]) {
			rel1 = userRelations[user1.Id][k];

			user1 = users[rel1.From];	// ?
			user2 = users[rel1.To];
			if (!RelationExists(rel1)) {
				if (user1 && user2) {
					if (rel1.Type == "b" || rel1.Type == "s") {
						hl(user1.x - left, user1.y + 5, left, color);
						hl(user2.x - left, user2.y + 5, left, color);
						vl(user1.x - left, user1.y + 5, user2.y - user1.y, color);

						flagLeft = true;
						linked[linked.length] = user2.Id;

						leftTop = CheckLess(leftTop, user1.y + 5, user2.y + 5);
						leftBottom = CheckMore(leftBottom, user1.y + 5, user2.y + 5);

						hasBrothers = true;
					} else if (rel1.Type == "h" || rel1.Type == "w") {
						hl(user1.x, user1.y + 5, right + thick, color);
						hl(user2.x, user2.y + 5, right + thick, color);
						vl(user1.x + right, user1.y + 5, user2.y - user1.y, color);
					
						linked[linked.length] = user2.Id;
						flagRight = true;

						rightTop = CheckLess(rightTop, user1.y + 5, user2.y + 5);
						rightBottom = CheckMore(rightBottom, user1.y + 5, user2.y + 5);

						parents[parents.length] = user1.Id;
						parents[parents.length] = user2.Id;
					} else if (rel1.Type == "m" || rel1.Type == "f") {
						recommendedColors[user2.Id] = color;
					} else if (rel1.Type == "c") {
						parentId = user2.Id;
					}

				}
			}
		}
		MarkRelations(linked);

		if (parents.length > 0) {
			mid = rightTop + Math.round((rightBottom - rightTop) / 2);
			dot1 = new dot(user1.x + right, mid, color);

			for (i = 0, l = parents.length; i < l; i++) {
				parentsLinks[parents[i]] = dot1;
			}
		}
		if (flagRight) {
			AddGenLevel(rights, user1.Generation);
		}
		if (parentId && parentsLinks[parentId]) {
			parentsDot = parentsLinks[parentId];
			prevGen = user1.Generation - 1;
			if (hasBrothers) {
				mid = leftTop + Math.round((leftBottom - leftTop) / 2);
				left = 10 + (lefts[user1.Generation] ? gap * lefts[user1.Generation] : 0);
			} else {
				mid = user1.y;
				left = user1.x;
			}

			lefter = (genWidth * prevGen) + 100 + (rights[prevGen] ? gap * rights[prevGen] : 0);

			hl(parentsDot.x, parentsDot.y, lefter - parentsDot.x + thick, color);
//			hl(parentsDot.x + gap, mid, user1.x - left - parentsDot.x, color);
//			vl(lefter, parentsDot.y, mid - parentsDot.y, color);
			AddGenLevel(rights, prevGen);
		}
		if (flagLeft) {
			AddGenLevel(lefts, user1.Generation);
		}
	}
}
