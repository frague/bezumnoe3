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
      ['u' + this.props.userId]: !isSystem,
      'system': isSystem,
      'private': !isSystem && !isSelf && this.props.toUserId > 0,
      'self': !isSystem && isSelf
    };
  },

  addName() {
    return this.props.addName(this.props.userName);
  },

  wrapNames() {
    let text = (this.props.userId > 0
    ? 
      '<>' + this.props.userId + '>' + this.props.userName + '<>: ' 
    : 
      '') + this.props.text;
    return text.split('<>').map((chunk, index) => {
      let [id, name] = chunk.split('>');
      if (!id || !name) {
        return chunk;
      }
      return <a key={index} onClick={this.addName}>{name}</a>;
    });
  },

  render() {
    return <p className={utils.classNames(this.getClassName())}>
      {this.wrapNames()}
    </p>;
  }
});
