<?php

    function AddVersion($ver) {
     global $v;
    	$v .= str_replace(".", "", $ver);
    	return;
    }
    
    /*-----------------------------------------------------------------*/

    session_cache_limiter("private_no_expire");

	$isAdmin = false;
	$isSuperAdmin = false;
    $version = $_GET["ver"];
    if ($version) {
    	
		require_once "../server_references.php";

		$user = GetAuthorizedUser($dbMain, true);

		if ($user->IsAdmin()) {
			$isAdmin = true;
		}
		if ($user->IsSuperAdmin()) {
			$isSuperAdmin = true;
		}

    }

    /*-----------------------------------------------------------------*/

	$debug = 0;

    $v = "";
    $lastModifiedTime = 0;

    if (!$root) {
    	$local_root = "./";
    } else {
    	$local_root = $root."js/";
    }
	
	$files = scandir($local_root);	// Sort files

	for ($i = 0; $i < sizeof($files); $i++) {
		$file = $files[$i];
		if (eregi("^00\.super", $file) && !$isSuperAdmin) {
			continue;
		}
		if (eregi("^00\.admin", $file) && !$isAdmin) {
			continue;
		}
    	if (eregi("^[0-9]+\..+\.js$", $file)) {
			if ($version) {
				// Passing javascripts content
		        $s = file_get_contents($local_root.$file);

		        $lastModified = filemtime($local_root.$file);
		        if ($lastModified > $lastModifiedTime) {
		        	$lastModifiedTime = $lastModified;
		        }

		        if (!$debug) {
			        /* Math operations */
					$s = preg_replace("/ *([!&*+-=]) */", "$1", $s);
	
					/* js treatment */
					$s = ereg_replace("[\t ]+", " ", $s);

					// Comments
					$s = ereg_replace("/\*([^*]|\*[^/])*\*/", "", $s);
					$s = ereg_replace("//[^\n\r]*", "", $s);
	
					// New lines & empty spaces
					$s = ereg_replace("[\n\r]", "", $s);
					$s = ereg_replace("\t", " ", $s);

					$s = ereg_replace(" +\(", "(", $s);
					$s = ereg_replace("\{ +", "{", $s);
					$s = ereg_replace("\} +", "}", $s);
					$s = ereg_replace("; +", ";", $s);
					$s = ereg_replace("\) +\{", "){", $s);

					// Operators
					$s = str_replace("else {", "else{", $s);
					$s = str_replace(" ||", "||", $s);
					$s = str_replace(" &&", "&&", $s);
					$s = str_replace(" ? ", "?", $s);
				} else {
					$v .= "\n\n/* ".$file." */\n\n";
				}

	        	$v .= $s;
/*    	    } else {
    	    	// Preparing filename from versions
				/*$fh = fopen($local_root.$file, 'r');
				$ver = preg_replace("/^\/\/(\d+(\.\d+))/e", "AddVersion(\"$1\")", fread($fh, 10));
				fclose($fh);*/
    	    }
    	}
	}

    if ($v) {
    	if ($version) {
    		//AddLastModified($lastModifiedTime);
    		AddEtagHeader($lastModifiedTime.$user->User->Id);
    		//Process (encode) data somehow
    	}
		echo $v;
    } else {
    	echo "ffw";
    }

?>