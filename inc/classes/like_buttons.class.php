<?php

  // Common like button
  abstract class LikeButton {
    var $Title, $Description, $Url, $Image;

    function LikeButton($title = "", $description = "", $url = "", $image = "", $site_name = "", $tags = "") {
      $this->Title = $title;
      $this->Description = $description;
      if (!$url) {
        $url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        $url = "http://".preg_replace("/\/comments.*$/", "", $url);
      }
      $this->Url = $url;
      $this->Image = $image;
      $this->SiteName = $site_name;
      $this->Tags = $tags ? $tags : array();
    }

    function indent($n, $text) {
      $n = str_repeat("\t", $n);
      return $n.str_replace("\n", "\n".$n, $text);
    }

    function getBasicMetadata() {
      // Sorry for the stubs in urls (((
      $tags = "";
      foreach ($this->Tags as $tag) {
        $tags .= "\n<meta property=\"article:tag\" content=\"".MetaContent($tag)."\" />";
      }

      return $this->indent(2, "<!-- Open Graph data -->
<meta property=\"og:url\" content=\"".$this->Url."\" />
<meta property=\"og:type\" content=\"blog\" />
<meta property=\"og:title\" content=\"".MetaContent($this->Title)."\" />
<meta property=\"og:description\" content=\"".MetaContent($this->Description)."\" />
<meta property=\"og:image\" content=\"".MetaContent($this->Image)."\" />
<meta property=\"og:site_name\" content=\"".MetaContent($this->SiteName)."\" />".$tags);
    }

    public static abstract function getHeadContent();
    abstract function getButtonContent();
  }

  // VKontakte like button
  class VkLikeButton extends LikeButton {
    public static function getHeadContent() {
      return "<script type=\"text/javascript\" src=\"//vk.com/js/api/openapi.js?154\"></script>\n
<script type=\"text/javascript\">VK.init({apiId: 6056026, onlyWidgets: true});</script>\n";
    }

    function getButtonContent() {
      $id = MakeGuid(5);
      return "<div id=\"vk_like".$id."\"></div>
<script type=\"text/javascript\">VK.Widgets.Like(\"vk_like".$id."\", {type: \"mini\", verb: 1, height: 20, pageTitle: \"".JsQuote($this->Title)."\", pageDescription: \"".JsQuote($this->Description)."\", pageImage: \"".JsQuote($this->Image)."\"});</script>";
    }

    function getMetadata() {
      return $this->indent(2, "<!-- VK.com data -->");
    }
  }

  // Facebook like button
  class FacebookLikeButton extends LikeButton {
    public static function getHeadContent() {
      return "<div id=\"fb-root\"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = 'https://connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v3.0&appId=223828064791316&autoLogAppEvents=1';
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>";
    }

    function getButtonContent() {
      return "<div class=\"fb-share-button\" data-href=\"".MetaContent($this->Url)."\" data-layout=\"button_count\" data-size=\"small\" data-mobile-iframe=\"false\"><a target=\"_blank\" href=\"https://www.facebook.com/sharer/sharer.php?u=".MetaContent($this->Url)."&amp;src=sdkpreparse\" class=\"fb-xfbml-parse-ignore\">Поделиться</a></div>";
    }

    function getMetadata() {
      return $this->indent(2, "<!-- Facebook data -->
<meta property=\"fb:admins\" content=\"495615507450859\" />");
    }
  }

  // Twitter like button
  class TwitterLikeButton extends LikeButton {
    public static function getHeadContent() {
      return "";
    }

    function getButtonContent() {
      return "<a class=\"twitter-share-button\"
  href=\"https://twitter.com/share\"
  data-size=\"small\"
  data-text=\"".MetaContent($this->Title)."\"
  data-url=\"".MetaContent($this->Url)."\"
  data-hashtags=\"".MetaContent(join(",", $this->Tags))."\"
  data-via=\"bezumnoe\"
  data-related=\"bezumnoe\">
Tweet
</a><script>window.twttr = (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0],
    t = window.twttr || {};
  if (d.getElementById(id)) return t;
  js = d.createElement(s);
  js.id = id;
  js.src = \"https://platform.twitter.com/widgets.js\";
  fjs.parentNode.insertBefore(js, fjs);

  t._e = [];
  t.ready = function(f) {
    t._e.push(f);
  };

  return t;
}(document, \"script\", \"twitter-wjs\"));</script>";
    }

    function getMetadata() {
      return $this->indent(2, "<!-- Twitter Card data -->
<meta name=\"twitter:card\" content=\"summary\" />
<meta name=\"twitter:site\" content=\"bezumnoe\" />
<meta name=\"twitter:title\" content=\"".MetaContent($this->Title)."\" />
<meta name=\"twitter:description\" content=\"".MetaContent($this->Description)."\" />");
    }
  }

  // Google +1 button
  class GooglePlusButton extends LikeButton {
    public static function getHeadContent() {
      return "<script src=\"https://apis.google.com/js/platform.js\" async defer></script>\n";
    }
    function getButtonContent() {
      return "<div class=\"g-plusone\" data-size=\"medium\" data-href=\"".$this->Url."\"></div>";
    }

    function getMetadata() {
      return $this->indent(2, "<!-- Schema.org markup for Google+ -->
<meta itemprop=\"name\" content=\"".MetaContent($this->Title)."\" />
<meta itemprop=\"description\" content=\"".MetaContent($this->Description)."\" />
<meta itemprop=\"image\" content=\"".MetaContent($this->Image)."\" />");
    }
  }

?>