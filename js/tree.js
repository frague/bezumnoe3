var d = document;
var t = d.getElementById("bb");

var thick = 3;
var gap = 5;
var genWidth = 400;
var half = 5;

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

  var div = d.createElement("p");
  try {
    div.style.left = x + "px";
    div.style.top = y + "px";
    div.style.width = (x1 - x) + "px";
    div.style.height = (y1 - y) + "px";
  } catch (e) {
    // IE doesn't like "px"
  }
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

function b(x, y, u, color, is_current) {
  var span = d.createElement("label");
  color = is_current ? "orangered" : color;
    var a = d.createElement("a");
    a.User = u;
    a.innerHTML = u.Login;
    a.onclick = function() {DrawTree(this)};
    a.href = "javascript:void(0)";
  span.appendChild(a);
  span.style.left = x + "px";
  span.style.top = y + "px";
  if (color) {
    span.style.backgroundColor = color;
  }
  t.appendChild(span);
};

function g(gen, x) {
  var h4 = d.createElement("h4");
  h4.innerHTML = gen;
  h4.style.left = (x + 10) + "px";
  h4.style.top = "100px";
  t.appendChild(h4);
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
  var a = arr[key];
  if (!a) {
    a = [];
  }
  a[a.length] = value;
  arr[key] = a;
};


var users = [];
var generations = [];
var maxGeneration = 0;
if (!window.u) {
  var u = [];
  var r = [];
};

for (var i in u) {
  var user1 = new user(u[i]);
  users[user1.Id] = user1;
  AppendSubArray(generations, user1.Generation, user1);
  maxGeneration = maxGeneration > user1.Generation ? maxGeneration : user1.Generation;
}

var relations = [];
var userRelations = [];

for (var i in r) {
  var rel1 = new relation(r[i]);
  relations[relations.length] = rel1;
  AppendSubArray(userRelations, rel1.From, rel1);
}

function MarkRelation(id1, id2) {
  exists[id1 + "_" + id2] = 1;
  exists[id2 + "_" + id1] = 1;
};

function MarkRelations(arr) {
  for (var i = 0, l = arr.length; i < l - 1; i++) {
    for (var k = i + 1; k < l; k++) {
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



function DrawTree(u) {
  var queue = [];
  if (u == undefined) {
    u = "";
  } else {
    u = u.User;
    queue[u.Id] = 1;
  }
  t.innerHTML = "";

  var exists = [];

  var lefts = [];
  var rights = [];
  var parentsLinks = [];
  var recommendedColors = [];

  var generationsX = [];


  var index = 0;
  var colors = ["red", "green", "blue", "orangered", "brown", "darkblue", "darkorange", "purple", "olive", "lightseagreen"];

  var actually = 0;
  for (var i = 0; i < maxGeneration; i++) {
    var gen = generations[i];
    var people = gen.length;

    generationsX[i] = i ? generationsX[i - 1] + (120 + (u ? 30 : people * 6)) * (actually ? 1 : 0)  : 50;
    actually = 0;

    for (var y = 0; y < people; y++) {
      var user1 = gen[y];
      var passed = true;
      if (u) {
        if (queue[user1.Id] != 1) {
          passed = false;
          var rels = userRelations[user1.Id];
          if (rels) {
            for (var k = 0, l = rels.length; k < l; k++) {
              var rel = rels[k];
              if (rel.From == u.Id || rel.To == u.Id) {
                var id = rel.From == u.Id ? rel.To : rel.From;
                queue[id] = 1;
                passed = true;
              }
            }
          }
        }
      } else {
        actually = y;
      }

      if (passed) {
        user1.x = generationsX[i];
        user1.y = actually * 20 + 150;
        b(user1.x, user1.y, user1, "orange", (u && user1.Id == u.Id));
        gen[y] = user1;
        actually++;
      }
    }
    if (actually) {
      g(i, generationsX[i]);
    }
  };

  for (var kk in generations) {
    for (var i in generations[kk]) {
      var user1 = generations[kk][i];

      var left = 10 + (lefts[user1.Generation] ? gap * lefts[user1.Generation] : 0);
      var right = 100 + (rights[user1.Generation] ? gap * rights[user1.Generation] : 0);
      var color = recommendedColors[user1.Login] ? 
        recommendedColors[user1.Login] : colors[(index++) % colors.length];
      var newColor = colors[(index++) % colors.length];

      var flagLeft = false;
      var flagRight = false;
      var parents = [];
      var parentId = 0;
      var hasBrothers = false;

      var linked = [user1.Id];

      var rightTop = user1.y;
      var rightBottom = user1.y;

      var leftTop = user1.y;
      var leftBottom = user1.y;

      for (var k in userRelations[user1.Id]) {
        var rel1 = userRelations[user1.Id][k];

        var user1 = users[rel1.From];   // ?
        var user2 = users[rel1.To];

        if (u && (queue[rel1.From] != 1 || queue[rel1.To] != 1)) {
          continue;
        }

        if (rel1.Type == "b" || rel1.Type == "s") {
          hasBrothers = true;
        }

        if (!RelationExists(rel1)) {
          if (user1 && user2) {
            if (rel1.Type == "b" || rel1.Type == "s") {
              hl(user1.x - left, user1.y + half, left, color);
              hl(user2.x - left, user2.y + half, left, color);
              vl(user1.x - left, user1.y + half, user2.y - user1.y, color);

              flagLeft = true;
              linked[linked.length] = user2.Id;

              leftTop = CheckLess(leftTop, user1.y + half, user2.y + half);
              leftBottom = CheckMore(leftBottom, user1.y + half, user2.y + half);
            } else if (rel1.Type == "h" || rel1.Type == "w") {
              hl(user1.x, user1.y + half, right + thick, newColor);
              hl(user2.x, user2.y + half, right + thick, newColor);
              vl(user1.x + right, user1.y + half, user2.y - user1.y, newColor);

              linked[linked.length] = user2.Id;
              flagRight = true;

              rightTop = CheckLess(rightTop, user1.y + half, user2.y + half);
              rightBottom = CheckMore(rightBottom, user1.y + half, user2.y + half);

              parents[parents.length] = user1.Id;
              parents[parents.length] = user2.Id;
            } else if (rel1.Type == "m" || rel1.Type == "f") {
              if (!recommendedColors[user2.Login]) {
                recommendedColors[user2.Login] = newColor;
              }
            } else if (rel1.Type == "c") {
              parentId = user2.Id;
            }
          }
        }
      }
      MarkRelations(linked);

      if (parents.length > 0) {
        var mid = rightTop + Math.round((rightBottom - rightTop) / 2);
        var dot1 = new dot(user1.x + right, mid, newColor);

        for (var i = 0, l = parents.length; i < l; i++) {
          parentsLinks[parents[i]] = dot1;
        }
      }
      if (flagRight) {
        AddGenLevel(rights, user1.Generation);
      }
      if (parentId && parentsLinks[parentId]) {
        var parentsDot = parentsLinks[parentId];
        var prevGen = user1.Generation - 1;
        if (hasBrothers) {
          mid = leftTop + Math.round((leftBottom - leftTop) / 2) + index%8;
          left = 10 + (lefts[user1.Generation] ? gap * lefts[user1.Generation] : 0);
        } else {
          mid = user1.y + half;
          left = -thick;
        }

        var lefter = generationsX[prevGen] + 100 + (rights[prevGen] ? gap * rights[prevGen] : 0);

        hl(parentsDot.x, parentsDot.y, lefter - parentsDot.x + thick, color);
        hl(lefter, mid, user1.x - left - lefter, color);
        vl(lefter, parentsDot.y, mid - parentsDot.y, color);

        AddGenLevel(rights, prevGen);

        parentsLinks[parentId] = 0;
      }
      if (flagLeft) {
        AddGenLevel(lefts, user1.Generation);
      }
    }
  }
}
