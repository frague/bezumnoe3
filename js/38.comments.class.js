//1.1
/*
    Comments base class
*/

function Comments() {
    this.fields = new Array("SEARCH", "LOGIN", "TITLE");
    this.GridId = "CommentsGrid";
};

Comments.prototype = new PagedGrid();

