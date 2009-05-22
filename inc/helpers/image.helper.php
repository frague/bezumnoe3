<?php

     function GetWebSize($path) {
     	$s = @GetImageSize($path);
     	if ($s) {
     		return $s[3];
     	}
     	return "";
     }

     function HtmlImage($path, $serverPath = "", $cssClass = "") {
     	$result = "<img src='".$path."' ".GetWebSize($serverPath).($cssClass ? " class='".$cssClass."'" : "")." />";
     	return $result;
     }
?>