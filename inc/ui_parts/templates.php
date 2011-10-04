<?php

	function Head($title, $css = "", $js = "", $rss = "", $is_wide = false, $main_title = "", $like_buttons = array()) {
	  global $user, $meta_description, $root;

	  	if (!$meta_description) {
	  		$meta_description = "Старейший саратовский чат. Интересное общение, знакомства, персональные журналы (блоги)";
	  	}

?><!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="windows-1251" />
		<title><?php echo $title ?></title>
		<link rel="stylesheet" href="/css/global.css" />
		<link rel="stylesheet" href="/css/template_layout.css" />
		<link rel="icon" href="/img/icons/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="/img/icons/favicon.ico" type="image/x-icon">
		<meta name="description" content="<? echo $meta_description ?>" />
		<script language="javascript" src="/js1/common.js"></script>
		<script language="javascript" src="/js1/jquery/jquery-1.6.4.min.js"></script>
		<script language="javascript" src="/js1/jquery/jquery-ui-1.8.16.custom.min.js"></script>
<?php 
		
		if ($css) {
			if (!is_array($css)) {
				$css = array($css);
			}
			foreach ($css as $i) {
				echo "		<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/".$i."\" />\n";
			}
		}
		if ($rss) {
			echo "		<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"".$rss."\" />\n";
		}
		if ($js) {
			if (!is_array($js)) {
				$js = array ($js);
			}
			foreach ($js as $i) {
				echo "		<script language=\"javascript\" src=\"/js1/".$i."\"></script>\n";
		   	}
		}
	?>
		<script language="javascript" src="/js1/reply_common.js"></script>
		<?php include "google_analythics.php" ?>

<?php
		if (sizeof($like_buttons)) {
			require_once $root."inc/helpers/like_buttons.helper.php";
			echo GetMetadata($like_buttons);
		}
?>
	</head>
	<body>
		<div id="AlertContainer">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
				<tr><td align="center" valign="middle">
					<div id="AlertBlock">
					</div>
				</td></tr></table></div>

<?php
		if (sizeof($like_buttons)) {
			echo GetHeadIncludes();
		}
?>
		<div class="Main">
			<div class='Logged'>Авторизация: <strong id="Logged"><?php echo (!$user || $user->IsEmpty() ? "анонимно" : $user->User->Login); ?></strong></div>

			<header>
				<h1<?php echo (!$main_title && strlen($title) > 30) ? " class='LongText'" : ""; ?>>
					<a href="/" class="NoBorder"><img alt="На главную" title="На главную" src="/img/t/logo_small.gif" width="31" height="30" style="vertical-align:top;margin-top:2px;" /></a>
					<?php echo $main_title ? $main_title : $title; ?>
					</h1></header>

		<div style="clear: both;" class="Divider Horizontal"><span></span></div>
<?php
	}

	function Foot() {
	  global $root;
	?>
				<script language="javascript" src="/js1/template_layout.js"></script>
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

	function ErrorPage($message, $description = "") {
		Head("Ошибка &mdash; ".$message, "", "", "", false, "error.gif");
		?><div class="ErrorHolder">
	<h2>Ошибка</h2>
	<?php echo $message.($description ? "<br />\n".$description : ""); ?>
		</div>
		<div class="Spacer"></div>
		<?php
		
		Foot();
	}


?>