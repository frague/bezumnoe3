<?php

	function AddEtagHeader($etag) {

		header("ETag: ".$etag);

		if (trim($_SERVER["HTTP_IF_NONE_MATCH"]) == $etag) {
		    header("HTTP/1.1 304 Not Modified");
		    die;
		}
	}

	function AddLastModified($last_modified) {

		$lm = gmdate("D, d M Y H:i:s", $last_modified)." GMT";

		header("Last-Modified: $lm");

		if (@strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"]) == $last_modified) {
		    header("HTTP/1.1 304 Not Modified");
		    die;
		}
	}

	function DieWith404() {
		header("HTTP/1.0 404 Not Found");
		die;
	}

?>