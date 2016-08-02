/*

FlexFrame class
Handles window resize and update object's properties correspondingly

*/
export class FlexFrame {
  constructor(obj = {}, minWidth, minHeight) {
    this.x = 0;
    this.y = 0;
    this.width = 0;
    this.height = 0;

    this.element = obj;
    this.minWidth = minWidth ? parseInt(minWidth) : 0;
    this.minHeight = minHeight ? parseInt(minHeight) : 0;
    this.GetPosAndSize();
  };

  GetPosAndSize() {
    if (this.element === window) {
      return this.GetWindowSize();
    }

    this.width = parseInt(this.element.clientWidth);
    this.height = parseInt(this.element.clientHeight);

    var obj = this.element;

    if (this.element.offsetParent) {
      this.x = obj.offsetLeft;
      this.y = obj.offsetTop;
      while (obj = obj.offsetParent) {
        this.x += obj.offsetLeft;
        this.y += obj.offsetTop;
      }
    }
  }

  GetWindowSize() {
    this.x = 0;
    this.y = 0;

    if (self.innerWidth) {
      this.width = self.innerWidth;
      this.height = self.innerHeight;
    } else if (document.documentElement && document.documentElement.clientWidth) {
      this.width = document.documentElement.clientWidth;
      this.height = document.documentElement.clientHeight;
    } else if (document.body) {
      this.width = document.body.clientWidth;
      this.height = document.body.clientHeight;
    }

    if (navigator.appVersion.indexOf("Chrome") > 0) {
      this.height -= 24;
    }
  };

  Replace(x, y, w, h) {
    if (this.element == window || !this.element.style) {
      return;
    }

    if (x >= 0) {
      this.element.style.left = x +  'px';
    }
    if (y >= 0) {
      this.element.style.top = y +  'px';
    }
    if (w >= 0) {
      if (w < this.minWidth) {
        w = this.minWidth;
      }
      this.element.style.width = w + 'px';
    }
    if (h >= 0) {
      if (h < this.minHeight) {
        h = this.minHeight;
      }
      this.element.style.height = h + 'px';
    }
    this.GetPosAndSize();
  };

  Info() {
    var s = 'x=' + this.x + ', ';
    s += 'y='+ this.y + ', ';
    s += 'width='+ this.width + ', ';
    s += 'height='+ this.height;
    return s;
  }
}