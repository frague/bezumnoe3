/*
    Manage galleries and their contents
*/

class Galleries extends EditableGrid {
	constructor() {
		super();
    this.fields = ["OWNER_ID", "TITLE", "DESCRIPTION"];
    this.ServicePath = servicesPath + "gallery.service.php";
    this.Template = "gallery";
    this.GridId = "GalleriesGrid";
    this.Columns = 2;
	}
}
