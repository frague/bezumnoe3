//1.0
/*

	MyFrame class
	Handles window resize and update object's properties correspondingly

*/
function MyFrame(obj) {
	this.x = 0;
	this.y = 0;
	this.width = 0;
	this.height = 0;

	this.element = 0;

	this.GetPosAndSize = function() {
		if (this.element == window) {
			return this.GetWindowSize();
		}

		this.width = parseInt(this.element.clientWidth);
		this.height = parseInt(this.element.clientHeight);

		var obj = this.element;

		if (obj.offsetParent) {
			this.x = obj.offsetLeft;
			this.y = obj.offsetTop;
			while (obj = obj.offsetParent) {
				this.x += obj.offsetLeft;
				this.y += obj.offsetTop;
			}
		}
	};

	this.GetWindowSize = function() {
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
	};

	this.Replace = function(x, y, w, h) {
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
			this.element.style.width = w + 'px';
		}
		if (h >= 0) {
			this.element.style.height = h + 'px';
		}
		this.GetPosAndSize();
	};

	this.Info = function() {
		var s = 'x=' + this.x + ', ';
		s += 'y='+ this.y + ', ';
		s += 'width='+ this.width + ', ';
		s += 'height='+ this.height;
		return s;
	};

	if (obj) {
		this.element = obj;
		this.GetPosAndSize();
	}
}
