/*
    Timing functions
*/

// TODO: Remove asap!
function Wait(milliseconds) {
  var date = new Date();
  var curDate = null;

  do { 
  	curDate = new Date(); 
  } while (curDate - date < milliseconds);
};
