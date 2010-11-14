<?php

	function Head($title, $css = "", $js = "", $rss = "", $is_wide = false, $title_img = "") {
	  global $user, $meta_description;

	  	if (!$meta_description) {
	  		$meta_description = "Старейший саратовский чат. Интересное общение, знакомства, персональные журналы (блоги)";
	  	}

?><html>
<head>
	<title><?php echo $title ?></title>
	<link rel="stylesheet" type="text/css" href="/css/global.css" />
	<link rel="stylesheet" type="text/css" href="/css/template.css" />
	<link rel="icon" href="/img/icons/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="/img/icons/favicon.ico" type="image/x-icon">
	<meta name="description" content="<? echo $meta_description ?>" />
<?php 
		
		if ($css) {
			echo "	<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/".$css."\" />\n";
		}
		if ($rss) {
			echo "	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"".$rss."\" />\n";
		}
		if ($js) {
			echo "	<script language=\"javascript\" src=\"/js1/".$js."\"></script>\n";
		}
	?>
	<script language="javascript" src="/js1/reply_common.js"></script>
	<?php include "google_analythics.php" ?>
</head>
<body>
	<div class="Main">
<?php
		echo "<div class='Logged'>Авторизация: <strong id=\"Logged\">".(!$user || $user->IsEmpty() ? "анонимно" : $user->User->Login)."</strong></div>";
		?>
		<?php echo $title_img ? "<a href=\"/\" class=\"Noborder\"><img alt=\"На главную\" title=\"На главную\" src=\"/img/t/logo_small.gif\" width=\"31\" height=\"30\" style=\"vertical-align:top;margin-top:22px;\" /></a> <img alt=\"".$title."\" title=\"".$title."\" src=\"/img/titles/".$title_img."\" style=\"margin-top:20px\" />" : "<h1>".$title."</h1>" ?>
		<div style="clear: both;" class="Divider Horizontal"><span></span></div>
		
<?php
	}

	function Foot() {
	  global $root;
	?>
		<div style="clear: both;" class="Divider Horizontal"><span></span></div>

		<?php include $root."inc/ui_parts/rle_banner.php"; ?>

		<div style="clear: both;" class="Divider Horizontal Alternative"><span></span></div>
<?php
		include "footer.php";
		include $root."/inc/li_spider_check.inc.php";
?>
	</div>

<?php include $root."inc/ui_parts/li.php" ?>

</body>
</html>
<?php
	}


?>