import React from 'react';
/*
    Recepient class.

*/
export var Recepient = React.createClass({
  // constructor(id, title, is_locked, deleteHandler) {
  //   this.Id = id;
  //   this.title = title;
  //   this.isLocked = !!is_locked;
  //   this.deleteHandler = deleteHandler;
  // }

  render() {
    return <li key={this.props.name}>
      {this.props.name}
      {this.props.deleteHandler && !this.props.isLocked && 
        <a onClick={() => this.props.deleteHandler(this.props.id)}>&times;</a>
      }
    </li>
    // var li = document.createElement('li');
    // li.appendChild(document.createTextNode(this.title));
    // if (!this.isLocked) {
    //   var closeCross = document.createElement('a');
    //   closeCross.innerHTML = '&times;';
    //   closeCross.onclick = () => chat.deleteRecepient(this.Id);
    //   li.appendChild(closeCross);
    // }
    // container.appendChild(li);
  },

  Gather(index) {
    return (index ? "," : "") + this.Id;
  }
})