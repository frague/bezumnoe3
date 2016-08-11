import $ from 'jquery';
import React from 'react';
import ReactDOM from 'react-dom';
/*

FlexFrame class
Handles window resize and update object's properties correspondingly

*/
export var FlexFrame = React.createClass({
  propTypes: {
    topLeft: React.PropTypes.arrayOf(React.PropTypes.number).isRequired,
    bottomRight: React.PropTypes.arrayOf(React.PropTypes.number).isRequired
  },
  getWindowSize() {
    if (self.innerWidth) {
      return {
        width: self.innerWidth,
        height: self.innerHeight
      };
    } else if (document.documentElement && document.documentElement.clientWidth) {
      return {
        width: document.documentElement.clientWidth,
        height: document.documentElement.clientHeight
      };
    } else if (document.body) {
      return {
        width: document.body.clientWidth,
        height: document.body.clientHeight
      };
    };

    throw(new Error('Unable to get viewport dimensions'));

    // if (navigator.appVersion.indexOf("Chrome") > 0) {
    //   this.height -= 24;
    // }
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
    var {width, height} = this.getWindowSize();
    var result = {};
    var {topLeft, bottomRight} = this.props;
    result.top = (topLeft[1] < 0 ? (height - topLeft[1]) : topLeft[1]) + 'px';
    result.left = (topLeft[0] < 0 ? (width - topLeft[0]) : topLeft[0]) + 'px';
    if (bottomRight[0] <= 0) {
      result.right = Math.abs(bottomRight[0]) + 'px';
    } else {
      result.width = bottomRight[0] + 'px';
    };
    if (bottomRight[1] <= 0) {
      result.bottom = Math.abs(bottomRight[1]) + 'px';
    } else {
      result.height = bottomRight[1] + 'px';
    };
    return result;
  },

  render() {
    return (
      <div className='flex-frame' style={this.makeStyle()}>
        {this.props.children}
      </div>
    );
  }
});