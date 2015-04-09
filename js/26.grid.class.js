//6.3
/*
    Base class for grids with[out] pager.
*/


/* Grid class */

function Grid() {
    this.GridId = "Grid";
    this.Columns = 3;
    this.CurrentContent = [];
    this.HasEmptyRow = false;
};

Grid.prototype = new OptionsBase();

Grid.prototype.GatherDTO = function(dto) {
    var l = this.fields.length;
    if (dto.fields.length < l) {
        l = dto.fields.length;
    }
    for (var i = 0; i < l; i++) {
        this[this.fields[i]] = dto[dto.fields[i]];
    }
    return true;
};

Grid.prototype.FindTableBase = function() {
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
};

Grid.prototype.FindTable = function() { // Method to override
    return this.FindTableBase();
};

Grid.prototype.AddItem = function(dto) {
    this.CurrentContent[this.CurrentContent.length] = dto;
};

Grid.prototype.ClearRecords = function(show_indicator) {
    this.FindTable();

    if (!this.Tbody) {
        return false;
    }

    for (var i = 1, l = this.Tbody.childNodes.length; i < l; i++) {
        var e = this.Tbody.childNodes[l - i];
        this.Tbody.removeChild(e);
    }

    if (show_indicator) {
        var tr = d.createElement("tr");

        var td = d.createElement("td");
        td.colSpan = this.Columns;
        td.innerHTML = LoadingIndicator;

        tr.appendChild(td);
        this.Tbody.appendChild(td);
    }
    this.CurrentContent = [];
    this.HasEmptyRow = false;
};

Grid.prototype.Request = function(params, callback) {
    this.ClearRecords(true);
    this.BaseRequest(params, callback);
    this.HasEmptyRow = false;
};

Grid.prototype.DoBind = function(content) {
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
};

Grid.prototype.Bind = function(content) {
    return this.DoBind(content);
};

Grid.prototype.Refresh = function() {
    var content = this.CurrentContent;
    return this.DoBind(content);
};


/* --------------- Paged Grid class --------------- */

function PagedGrid() {
    this.PerPage = 10;
    this.PagerId = "Pager";
};

PagedGrid.prototype = new Grid();

PagedGrid.prototype.InitPager = function() {    // Method to override
    this.Pager = new Pager(this.Inputs[this.PagerId], function(){}, this.PerPage);
    this.Tab.Pager = this.Pager;
};

PagedGrid.prototype.FindTable = function() {
    if (!this.FindTableBase()) {
        this.InitPager();
        this.Pager.Tab = this.Tab;
    }
};

PagedGrid.prototype.Bind = function(content, total) {
    this.Pager.Total = total;
    this.DoBind(content);
    this.Pager.Print();
};

PagedGrid.prototype.Request = function(params, callback) {
    this.ClearRecords(true);
    if (!params) {
        params = "";
    }
    params += this.Gather();
    params += MakeParametersPair("from", this.Pager.Offset());
    params += MakeParametersPair("amount", this.Pager.PerPage);
    this.BaseRequest(params, callback);
};

PagedGrid.prototype.SwitchPage = function() {
    this.Request();
};




/* --------------- Editable Grid class --------------- */


function EditableGrid() {
};

EditableGrid.prototype = new Grid();

EditableGrid.prototype.Edit = __edit;
EditableGrid.prototype.CancelEditing = __cancelEditing;
EditableGrid.prototype.AddRow = __addRow;

/* --------------- Editable Paged Grid class --------------- */

function EditablePagedGrid() {
};

EditablePagedGrid.prototype = new PagedGrid();

EditablePagedGrid.prototype.Edit = __edit;
EditablePagedGrid.prototype.CancelEditing = __cancelEditing;
EditablePagedGrid.prototype.AddRow = __addRow;


/* Editable grid helper methods */

function __edit(id) {
    if (!this.CurrentContent) {
        return;
    }
    for (var i = 0,l = this.CurrentContent.length; i < l; i++) {
        this.CurrentContent[i].EditView = (this.CurrentContent[i].Id == id) ? 1 : 0;
    }
    this.Refresh();
};

function __cancelEditing() {
    if (this.CurrentContent && this.CurrentContent.length && this.CurrentContent[this.CurrentContent.length - 1].Id == 0) {
        this.CurrentContent.pop();
    }
    this.Edit("");
};

function __addRow(dto) {
    if (!this.HasEmptyRow) {
        this.AddItem(dto);
        this.DoBind(this.CurrentContent);
        this.Edit(0);
        this.HasEmptyRow = true;
    }
};


/* Helper methods */

function MakeGridRow(index) {
    var tr = d.createElement("tr");
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
    var td0 = d.createElement("th");
    td0.colSpan = cols;
    td0.innerHTML = text;
    trd.appendChild(td0);
    return trd;
};
