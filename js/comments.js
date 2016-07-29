/*
    Comments base class
*/

class Comments extends PagedGrid {
  constructor() {
    super();
    this.fields = ["SEARCH", "LOGIN", "TITLE"];
    this.GridId = "CommentsGrid";
  }
};
