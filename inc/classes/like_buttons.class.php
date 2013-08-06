<?php

    // Common like button
	abstract class LikeButton {
		var $Title, $Description, $Url, $Image;

		function LikeButton($title = "", $description = "", $url = "", $image = "") {
			$this->Title = $title;
			$this->Description = $description;
			$this->Url = $url;
			$this->Image = $image;
		}

		function getBasicMetadata() {
			// Sorry for the stubs in urls (((

			$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			$url = preg_replace("/\/comments$/", "", $url);

			return "<meta property=\"og:title\" content=\"".MetaContent($this->Title)."\" />
<meta property=\"og:description\" content=\"".MetaContent($this->Description)."\" />
<meta property=\"og:type\" content=\"blog\" />
<meta property=\"og:url\" content=\"http://".$url."\" />
<meta property=\"og:image\" content=\"".MetaContent($this->Image)."\" />\n";
		}	

		public static abstract function getHeadContent();
	    abstract function getButtonContent();
	}

	// VKontakte like button
	class VkLikeButton extends LikeButton {
		public static function getHeadContent() {
			return "<script type=\"text/javascript\" src=\"http://userapi.com/js/api/openapi.js?33\"></script><script type=\"text/javascript\">VK.init({apiId: 2411995, onlyWidgets: true});</script>\n";
		}
		function getButtonContent() {
			$id = MakeGuid(5);
			return "<div id=\"vk_like".$id."\"></div>
<script type=\"text/javascript\">VK.Widgets.Like(\"vk_like".$id."\", {type: \"mini\", verb: 1, pageTitle: \"".JsQuote($this->Title)."\", pageDescription: \"".JsQuote($this->Description)."\", pageImage: \"".JsQuote($this->Image)."\"});</script>";
		}
		
		function getMetadata() {
			return "";
		}
	}

	// Facebook like button
	class FacebookLikeButton extends LikeButton {
		public static function getHeadContent() {
			return "<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = \"//connect.facebook.net/en_US/all.js#xfbml=1\";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
";
		}

		function getButtonContent() {
			return "<div id=\"fb-root\"></div>
<div class=\"fb-like\" data-send=\"false\" data-layout=\"button_count\" data-width=\"100\" data-show-faces=\"true\" data-action=\"recommend\"></div>";
//			return "<div id=\"fb-root\"></div><script src=\"http://connect.facebook.net/en_US/all.js#appId=140609669351753&amp;xfbml=1\"></script><fb:like href=\"\" send=\"false\" width=\"450\" show_faces=\"false\" action=\"recommend\" font=\"\"></fb:like>";
		}
		
		function getMetadata() {
			return "<meta property=\"fb:admins\" content=\"100002414166352\" />\n";
		}
	}


	// Facebook like button
	class TwitterLikeButton extends LikeButton {
		public static function getHeadContent() {
			return "";
		}

		function getButtonContent() {
			return "<a href=\"http://twitter.com/share\" class=\"twitter-share-button\" data-count=\"horizontal\" data-via=\"bezumnoe\" data-lang=\"en\">Tweet</a><script type=\"text/javascript\" src=\"http://platform.twitter.com/widgets.js\"></script>";
		}
		
		function getMetadata() {
			return "";
		}
	}

	// Google +1 button
	class GooglePlusButton extends LikeButton {
		public static function getHeadContent() {
			return "<script type=\"text/javascript\" src=\"https://apis.google.com/js/plusone.js\">{lang: 'ru'}</script>\n";
		}
		function getButtonContent() {
			return "<g:plusone size=\"medium\"></g:plusone>";
		}
		
		function getMetadata() {
			return "";
		}
	}





?>