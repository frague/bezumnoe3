<?php
	require_once $root."inc/classes/like_buttons.class.php";

	function GetHeadIncludes() {
		$result = "";
		foreach (array(VkLikeButton, TwitterLikeButton, GooglePlusButton, FacebookLikeButton) as $b) {
			$button = new $b();
			$result .= $button->getHeadContent()."\n";
		}
		return $result;
	}

	function MakeDescription($record) {
		$DESCRIPTION_LENGTH = 255;
		$strip_expression = "/[. \t()\n\r][^. \t()\n\r]*$/";

		$result = substr(strip_tags($record->Content), 0, $DESCRIPTION_LENGTH);
		$result = preg_replace($strip_expression, "", $result);
//		$result = $record->Title."\\n".$result;
		return $result;
	}
	
	function FillButtonObjects($title = "", $description = "", $url = "", $image = "") {
		$result = array();
		foreach (array(VkLikeButton, TwitterLikeButton, GooglePlusButton, FacebookLikeButton) as $b) {
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

	function GetButtonsMarkup($buttons = array()) {
		$result = "<table class=\"Likes\"><tr>\n";
		foreach ($buttons as $b) {
			$result .= "<td>".$b->getButtonContent()."</td>\n";
		}
		return $result."</tr></table>";
	}

?>