//1.0
/*
	Manage galleries and their contents
*/

function Galleries() {
	this.fields = ["OWNER_ID", "TITLE", "DESCRIPTION"];
	this.ServicePath = servicesPath + "gallery.service.php";
	this.Template = "gallery";
	this.GridId = "GalleriesGrid";
	this.Columns = 2;
};

Galleries.prototype = new EditableGrid();

