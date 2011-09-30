<?php
	require_once $root."inc/classes/like_buttons.class.php";

	function GetHeadIncludes() {
		$result = "";
		foreach (array(VkLikeButton, TwitterLikeButton, FacebookLikeButton) as $b) {
			$button = new $b();
			$result .= $button->getHeadContent()."\n";
		}
		return $result;
	}

	function FillButtonObjects($title = "", $description = "", $url = "", $image = "") {
		$result = array();
		foreach (array(VkLikeButton, TwitterLikeButton, FacebookLikeButton) as $b) {
			array_push($result, new $b($title, $description, $url, $image));

		}
		return $result;
	}
	
	function GetMetadata($buttons = array()) {
		$result = "";
		foreach ($buttons as $b) {
			if (!$result) {
				$result .= $b->getBasicMetadata()."\n";
			}
			$result .= $b->getMetadata()."\n";
		}
		return $result;
	}

	function GetButtonsMarkup($title = "", $description = "", $url = "", $image = "") {
		$result = "";
		foreach (array(VkLikeButton, TwitterLikeButton, FacebookLikeButton) as $b) {
			$button = new $b($title, $description, $url, $image);
			$result .= $button->getButtonContent()."\n";
		}
		return $result;
	}

?>