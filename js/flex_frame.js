// FlexFrame.
// Frame-like content container, sized and positioned relatively to 
// the viewport.

import $ from 'jquery';
import React from 'react';

export var FlexFrame = React.createClass({
  propTypes: {
    // Dimensions format: [x, y, w, h]
    // For negative values result calculated relatively to the opposite side
    dimensions: React.PropTypes.arrayOf(React.PropTypes.number).isRequired
  },

  getInitialState() {
    return this.getWindowSize();
  },

  getDefaultProps() {
    return {
      className: ''
    }
  },

  componentDidMount() {
    $(window).resize(() => this.setState(this.getWindowSize()));
  },

  componentWillUnmount() {
    $(window).off('resize');
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
  },

  makeStyle() {
    var {width, height} = this.state;
    var {dimensions} = this.props;
    var result = {};
    result.top = (dimensions[1] < 0 ? (height + dimensions[1]) : dimensions[1]) + 'px';
    result.left = (dimensions[0] < 0 ? (width + dimensions[0]) : dimensions[0]) + 'px';
    if (dimensions[2] <= 0) {
      result.right = Math.abs(dimensions[2]) + 'px';
    } else {
      result.width = dimensions[2] + 'px';
    };
    if (dimensions[3] <= 0) {
      result.bottom = Math.abs(dimensions[3]) + 'px';
    } else {
      result.height = dimensions[3] + 'px';
    };
    return result;
  },

  render() {
    return (
      <div className={'flex-frame ' + this.props.className} style={this.makeStyle()}>
        {this.props.children}
      </div>
    );
  }
});