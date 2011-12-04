function startup() {
	Modernizr.load([
	{
		load: '/js1/jquery/jquery.prettyPhoto.js',
		complete: function() {
			$("a[rel^='pp']").prettyPhoto();
		}
	}
	]);
};
