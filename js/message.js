import React from 'react';
import {Tab} from './tabs';
import {dateHelper} from './date_helper';
import _ from 'lodash';
import {utils} from './utils';

export var Message = React.createClass({
  getClassName() {
    let isSystem = !this.props.userId;
    let isSelf = this.props.userId === this.props.toUserId;
    return {
      ['u' + this.props.userId]: true,
      'system': isSystem,
      'private': !isSystem && !isSelf && this.props.toUserId > 0,
      'self': !isSystem && isSelf
    };
  },

  render() {
    console.log(this.props);
    return <p className={utils.classNames(this.getClassName())}>
      <a>{this.props.userName}</a>: {this.props.text}
    </p>;
  }
});
