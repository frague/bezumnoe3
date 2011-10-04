function $(id) {
	if (d.getElementById) {
		return d.getElementById(id);
	} else if (d.all) {
		return d.all[id];
	} else if (d.layers) {
		return d.layers[id];
	}
	return false;
};
