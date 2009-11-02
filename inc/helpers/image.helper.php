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

	// Simple Image class

	class SimpleImage {
		var $image;
		var $image_type;
 
		function Load($filename) {
			$image_info = getimagesize($filename);
			$this->image_type = $image_info[2];
			if( $this->image_type == IMAGETYPE_JPEG ) {
				$this->image = imagecreatefromjpeg($filename);
			} elseif ($this->image_type == IMAGETYPE_GIF) {
				$this->image = imagecreatefromgif($filename);
			} elseif ($this->image_type == IMAGETYPE_PNG) {
				$this->image = imagecreatefrompng($filename);
			}
		}

		function Save($filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null) {
			if ($image_type == IMAGETYPE_JPEG) {
				imagejpeg($this->image, $filename, $compression);
			} elseif ($image_type == IMAGETYPE_GIF) {
				imagegif($this->image, $filename);
			} elseif ($image_type == IMAGETYPE_PNG) {
				imagepng($this->image, $filename);
			}
			if( $permissions != null) {
				chmod($filename, $permissions);
			}
		}

		function Output($image_type = IMAGETYPE_JPEG) {
			if ($image_type == IMAGETYPE_JPEG) {
				imagejpeg($this->image);
			} elseif ($image_type == IMAGETYPE_GIF) {
				imagegif($this->image);
			} elseif ($image_type == IMAGETYPE_PNG) {
				imagepng($this->image);
			}
		}

		function GetWidth() {
			return imagesx($this->image);
		}

		function GetHeight() {
			return imagesy($this->image);
		}

		function ResizeToHeight($height) {
			$ratio = $height / $this->GetHeight();
			$width = $this->GetWidth() * $ratio;
			$this->Resize($width,$height);
		}

		function ResizeToWidth($width) {
			$ratio = $width / $this->GetWidth();
			$height = $this->GetHeight() * $ratio;
			$this->Resize($width, $height);
		}

		function Scale($scale) {
			$width = $this->GetWidth() * $scale/100;
			$height = $this->GetHeight() * $scale/100; 
			$this->Resize($width, $height);
		}

		function Resize($width, $height) {
			$new_image = imagecreatetruecolor($width, $height);
			imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->GetWidth(), $this->GetHeight());
			$this->image = $new_image;
		}
	}


?>