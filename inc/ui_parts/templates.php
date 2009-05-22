<?php

	function Head($title, $css = "", $js = "") {
	  global $user;

?><html>
<head>
	<title><?php echo $title ?></title>
	<link rel="stylesheet" type="text/css" href="/3/css/global.css" />
	<link rel="stylesheet" type="text/css" href="/3/css/template.css" />
	<?php 
		
		if ($css) {
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/3/css/".$css."\" />\n";
		}
		if ($js) {
			echo "<script language=\"javascript\" src=\"/3/js1/".$js."\"></script>\n";
		}
	?>
	<script language="javascript" src="/3/js1/reply_common.js"></script>
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