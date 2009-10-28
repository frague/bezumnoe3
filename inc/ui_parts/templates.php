<?php

	function Head($title, $css = "", $js = "") {
	  global $user;

?><html>
<head>
	<title><?php echo $title ?></title>
	<link rel="stylesheet" type="text/css" href="/css/global.css" />
	<link rel="stylesheet" type="text/css" href="/css/template.css" />
	<link rel="icon" href="/img/icons/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="/img/icons/favicon.ico" type="image/x-icon">
	<?php 
		
		if ($css) {
			echo "	<link rel=\"stylesheet\" type=\"text/css\" href=\"/css/".$css."\" />\n";
		}
		if ($js) {
			echo "	<script language=\"javascript\" src=\"/js1/".$js."\"></script>\n";
		}
	?>
	<script language="javascript" src="/js1/reply_common.js"></script>
</head>
<body>

<?php
		echo "<div class='Logged'>Авторизация: <b>".(!$user || $user->IsEmpty() ? "анонимно" : $user->User->Login)."</b></div>";
	}

	function Foot() {

		include "footer.php";
?>
</body>
</html>
<?php
	}


?>