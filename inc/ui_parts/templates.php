<?php

	class Page {
		var $title = "";
		var $meta = "";
		var $rss = "";
		var $css = array(
			"<link rel=\"stylesheet\" href=\"/css/global.css\" />", 
			"<link rel=\"stylesheet\" href=\"/css/template_layout.css\" />", 
			"<link rel=\"stylesheet\" href=\"/css/lettering.css\" />", 
			"<!--[if IE]><link rel=\"stylesheet\" href=\"/css/ie.css\" /><![endif]-->"
		);
		var $scripts = array();
		var $buttons = array();

		function Page($title, $meta = "", $header_title = "", $no_jquery = false) {
		    $this->no_jquery = $no_jquery;
			$this->title = $title;
			$this->header_title = $header_title;
			$this->meta = $meta ? $meta : "Старейший саратовский чат. Интересное общение, знакомства, персональные журналы (блоги)";
			
			$this->AddCss($css);

			$this->AddJs(array("jquery/jquery.js", "jquery/jquery-ui.js", "modernizr.js", "jquery/jquery.lettering-0.6.1.min.js", "common.js"));
			$this->AddJs("reply_common.js");
			$this->AddJs($scripts);

			$this->SetRss($rss);
		}
		
		function SetRss($url) {
			if ($url) {
				$this->rss = "\n		<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"".$url."\" />";
			}
		}

		function SetLikeButtons($buttons) {
			$this->buttons = $buttons;
		}

		function AddItems(&$where, $items, $format = "%s") {
	    	if (!$items) {
	    		return;
	    	}
			if (!is_array($items)) {
				$items = array($items);
			}
			foreach ($items as $i) {
				$where[] = str_replace("%s", $i, $format);
			}
			return $where;
		}
	    
	    function AddCss($urls) {
	    	$this->AddItems($this->css, $urls, "<link rel=\"stylesheet\" href=\"/css/%s\" />");
	    }
	
	    function AddJs($urls) {
	    	$this->AddItems($this->scripts, $urls, "<script src=\"/js1/%s\"></script>");
	    }

	    function PrintArray($array, $prefix = "") {
			foreach ($array as $i) {
				print $prefix.$i."\n";
			}
	    }

	    function PrintHeader() {
		  global $user, $root;

?><!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="windows-1251" />
		<title><?php print $this->title; ?></title>
		<link rel="icon" href="/img/icons/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="/img/icons/favicon.ico" type="image/x-icon">
		<meta name="description" content="<?php print $this->meta; ?>" /><?php print $this->rss; ?>

<?php 
			$this->PrintArray($this->css, "		");
			$this->PrintArray($this->scripts, "		"); 
			include "google_analythics.php";

			if (sizeof($this->buttons)) {
				require_once $root."inc/helpers/like_buttons.helper.php";
				print GetMetadata($this->buttons);
			}

?>	</head>
	<body>
		<div id="AlertContainer">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
				<tr><td align="center" valign="middle">
					<div id="AlertBlock">
					</div>
				</td></tr></table></div>

<?php
			if (sizeof($this->buttons)) {
				print GetHeadIncludes();
			}
?>
		<div class="Main">
			<div class='Logged'>Авторизация: <strong id="Logged"><?php print (!$user || $user->IsEmpty() ? "анонимно" : $user->User->Login); ?></strong></div>

			<header>
				<div class="Header">
					<a href="/" class="NoBorder">
						<img alt="На главную" title="На главную" src="/img/t/logo_small.gif" width="31" height="30" /></a>
					<h1<?php print (!$this->header_title && strlen($this->title) > 30) ? " class=\"LongText\"" : ""; ?>><?php print $this->header_title ? $this->header_title : $this->title; ?></h1>
					</div></header>

		<div style="clear: both;" class="Divider Horizontal"><span></span></div>

<?php	    
		}

		function PrintFooter() {
		  global $root;
	?>
				<script src="/js1/template_layout.js"></script>
			<footer>
				<div style="clear: both;" class="Divider Horizontal"><span></span></div>
				<?php include $root."inc/ui_parts/rle_banner.php"; ?>
				<div style="clear: both;" class="Divider Horizontal Alternative"><span></span></div>
<?php

		include "footer.php";
		include $root."/inc/li_spider_check.inc.php";

?>
			</footer>
		</div>
<?php

	include $root."inc/ui_parts/li.php";

?>
	</body>
</html>


<?php	
		}
	}

	function ErrorPage($message, $description = "") {
		
		$p = new Page("Ошибка &mdash; ".$message, "", "Ошибка");
		$p->PrintHeader();

		?>
<div class="ErrorHolder">
	<h2>Ошибка</h2>
	<?php echo $message.($description ? "<br />\n".$description : ""); ?>
</div>
<div class="Spacer"></div>
		<?php
		
		$p->PrintFooter();
	}

?>