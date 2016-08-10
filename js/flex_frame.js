import $ from 'jquery';
import React from 'react';
import ReactDOM from 'react-dom';
/*

FlexFrame class
Handles window resize and update object's properties correspondingly

*/
export var FlexFrame = React.createClass({
  getInitialState() {
    return {
      dimensions: this.getPositionAndSize()
    };
  },

  getPositionAndSize() {
    if (this.props.trackWindow) {
      return this.getWindowSize();
    };

    return {
      x: 0,
      y: 0,
      width: 0,
      height: 0
    }
  },

  getWindowSize() {
    var result = {
      x: 0,
      y: 0
    };

    if (self.innerWidth) {
      result = _.extend(result, {
        width: self.innerWidth,
        height: self.innerHeight
      });
    } else if (document.documentElement && document.documentElement.clientWidth) {
      result = _.extend(result, {
        width: document.documentElement.clientWidth,
        height: document.documentElement.clientHeight
      });
    } else if (document.body) {
      result = _.extend(result, {
        width: document.body.clientWidth,
        height: document.body.clientHeight
      });
    };

    return result;

    // if (navigator.appVersion.indexOf("Chrome") > 0) {
    //   this.height -= 24;
    // }
  },

  componentWillMount() {
    if (this.props.trackWindow) {
      $(window).resize(() => {
        console.log('Resized');
        this.setState(this.getInitialState());
      });
    }
  },

  // Replace(x, y, w, h) {
  //   if (this.element === window || !this.element.style) {
  //     return;
  //   }

  //   if (x >= 0) {
  //     this.element.style.left = x +  'px';
  //   }
  //   if (y >= 0) {
  //     this.element.style.top = y +  'px';
  //   }
  //   if (w >= 0) {
  //     if (w < this.minWidth) {
  //       w = this.minWidth;
  //     }
  //     this.element.style.width = w + 'px';
  //   }
  //   if (h >= 0) {
  //     if (h < this.minHeight) {
  //       h = this.minHeight;
  //     }
  //     this.element.style.height = h + 'px';
  //   }
  //   this.getPositionAndSize();
  // },

  // info() {
  //   var s = 'x=' + this.x + ', ';
  //   s += 'y='+ this.y + ', ';
  //   s += 'width='+ this.width + ', ';
  //   s += 'height='+ this.height;
  //   return s;
  // },

  makeStyle() {
    var {x, y, width, height} = this.state.dimensions;
    return {x, y, width, height};
  },

  render() {
    var {trackWindow, windowDimensions} = this.props;
    var props = {
      windowDimensions: trackWindow ? this.state.dimensions : windowDimensions
    };
    return (
      <div className='flex-frame' style={this.makeStyle()}>
        {this.props.children}
      </div>
    );
  }
});