<?php 

		$lm = gmdate("D, d M Y H:i:s", time())." GMT";
		$etag = '"'.md5($lm).'"';

		header("Last-Modified: $lm");
		header("ETag: \"$etag\"");
		header('Cache-Control: private');
		header('Pragma: ');
		header('Expires: ');

?>

blah!