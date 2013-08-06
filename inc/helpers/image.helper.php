<?php

	function GetWebSize($path) {
		$s = @GetImageSize($path);
		if ($s) {
			return $s[3];
		}
		return "";
	}

	function HtmlImage($path, $serverPath = "", $cssClass = "", $alt = "") {
		$result = "<img src='".$path."' ".GetWebSize($serverPath).($cssClass ? " class='".$cssClass."'" : "").($alt ? " alt='".$alt."' title='".$alt."'" : "")." />";
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

	// Showing Profile Photo

	function ProfilePhoto($profile, $login) {
	  global $root, $PathToPhotos, $PathToThumbs, $ServerPathToPhotos, $ServerPathToThumbs;

		if ($profile->Photo) {
			if (file_exists($root.$ServerPathToThumbs.$profile->Photo)) {
				$size = @GetImageSize($root.$ServerPathToThumbs.$profile->Photo);
				return "<a href=\"".$PathToPhotos.$profile->Photo."\" rel=\"pp[pp_gal]\"><img src=\"".$PathToThumbs.$profile->Photo."\" ".$size[3]." class=\"Photo\" alt=\"".HtmlQuote($login)."\" title=\"".HtmlQuote($login)."\" /></a>";
			} else {
				$lnk = "";
				$w = "width=\"300\"";
				$cache = preg_replace("/[^0-9]/", "", $profile->PhotoUploadDate);

				if (file_exists($root.$ServerPathToPhotos.$profile->Photo)) {
					$size = @GetImageSize($root.$ServerPathToPhotos.$profile->Photo);
					if ($size[0] <= 300) {
						$w = $size[3];
					} else {
						$lnk = "<a href=\"".$PathToPhotos.$profile->Photo."\" rel=\"pp[pp_gal]\">";
					}
				}
				return $lnk."<img src=\"".$PathToPhotos.$profile->Photo."\" ".$w." class=\"Photo\" alt=\"".HtmlQuote($login)."\" title=\"".HtmlQuote($login)."\" />".($lnk ? "</a>" : "");
			}
		}
		return "";
	}


?>