import {mixin} from 'es2015-mixin';
/*
  Base class for grids with[out] pager.
*/

class Grid extends OptionsBase {
  constructor() {
    super();
    this.GridId = "Grid";
    this.Columns = 3;
    this.CurrentContent = [];
    this.HasEmptyRow = false;
  };

  GatherDTO(dto) {
    var l = this.fields.length;
    if (dto.fields.length < l) {
      l = dto.fields.length;
    }
    for (var i = 0; i < l; i++) {
      this[this.fields[i]] = dto[dto.fields[i]];
    }
    return true;
  }

  FindTableBase() {
    if (this.Tbody) {
      return true;
    }
    this.FindRelatedControls();

    if (!this.Inputs) {
      return false;
    }

    var el = this.Inputs[this.GridId];
    if (el) {
      this.Tbody = el.firstChild;
    }
    return false;
  }

  FindTable() { // Method to override
    return this.FindTableBase();
  }

  AddItem(dto) {
    this.CurrentContent[this.CurrentContent.length] = dto;
  }

  ClearRecords(showIndicator) {
    this.FindTable();

    if (!this.Tbody) {
      return false;
    }

    for (var i = 1, l = this.Tbody.childNodes.length; i < l; i++) {
      var e = this.Tbody.childNodes[l - i];
      this.Tbody.removeChild(e);
    }

    if (showIndicator) {
      var tr = document.createElement("tr");

      var td = document.createElement("td");
      td.colSpan = this.Columns;
      td.innerHTML = loadingIndicator;

      tr.appendChild(td);
      this.Tbody.appendChild(td);
    }
    this.CurrentContent = [];
    this.HasEmptyRow = false;
  }

  Request(params, callback) {
    this.ClearRecords(true);
    this.BaseRequest(params, callback);
    this.HasEmptyRow = false;
  }

  DoBind(content) {
    if (!this.Tbody) {
      return false;
    }

    this.BaseBind();
    this.ClearRecords();
    for (var i = 0, l = content.length; i < l; i++) {
      this.Tbody.appendChild(content[i].ToString(i, this, this.Tbody));
      content[i].Grid = this;
    }
    this.CurrentContent = content;
  }

  Bind(content) {
    return this.DoBind(content);
  }

  Refresh() {
    var content = this.CurrentContent;
    return this.DoBind(content);
  }
}

/* Grid with paging */

class PagedGrid extends Grid {
  constructor() {
    super();
    this.PerPage = 10;
    this.PagerId = "Pager";
  }

  InitPager() {    // Method to override
    this.Pager = new Pager(this.Inputs[this.PagerId], function(){}, this.PerPage);
    this.Tab.Pager = this.Pager;
  }

  FindTable() {
    if (!this.FindTableBase()) {
      this.InitPager();
      this.Pager.Tab = this.Tab;
    }
  }

  Bind(content, total) {
    this.Pager.Total = total;
    this.DoBind(content);
    this.Pager.Print();
  }

  Request(params, callback) {
    this.ClearRecords(true);
    if (!params) {
      params = "";
    }
    params += this.Gather();
    params += MakeParametersPair("from", this.Pager.Offset());
    params += MakeParametersPair("amount", this.Pager.PerPage);
    this.BaseRequest(params, callback);
  }

  SwitchPage() {
    this.Request();
  }
}

// Editable grids operations
let crud = {
  Edit(id) {
    if (!this.CurrentContent) {
      return;
    }
    for (var i = 0,l = this.CurrentContent.length; i < l; i++) {
      this.CurrentContent[i].EditView = (this.CurrentContent[i].Id == id) ? 1 : 0;
    }
    this.Refresh();
  },
  CancelEditing() {
    if (this.CurrentContent && this.CurrentContent.length && this.CurrentContent[this.CurrentContent.length - 1].Id == 0) {
      this.CurrentContent.pop();
    }
    this.Edit("");
  },
  AddRow(dto) {
    if (!this.HasEmptyRow) {
      this.AddItem(dto);
      this.DoBind(this.CurrentContent);
      this.Edit(0);
      this.HasEmptyRow = true;
    }
  }
};

/* Grid with ability to edit records listed */
var EditableGrid = mixin(Grid, crud);

/* Grid with ability to edit records listed + paging*/
var EditablePagedGrid = mixin(PagedGrid, crud);

/* Helper methods */
function MakeGridRow(index) {
  var tr = document.createElement("tr");
  if (index%2) {
    tr.className = " Dark";
  }
  if (this.IsHidden) {
    tr.className += " Hidden";
  }
  return tr;
};

function MakeGridSubHeader(index, cols, text) {
  var trd = MakeGridRow(index);
  trd.className = "Sub";
  var td0 = document.createElement("th");
  td0.colSpan = cols;
  td0.innerHTML = text;
  trd.appendChild(td0);
  return trd;
};