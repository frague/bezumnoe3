<?

	$root = "../";
	require "../inc/ui_parts/templates.php";
	Head("�������� ������", "forum.css", "");

	require_once $root."references.php";

?>	<script language="javascript" src="migration.js"></script>
	<style>
		h4 {margin:4px 0 0 0; padding:0;}
		#indicator { padding:10px 0 0 0; }
		span.Step { padding:0px 30px; border:solid 1px #e0e0e0; margin:2px; background-color:#F0F0F0; font-size:7pt; }
		span.Step.Passed { background-color:#AFC36C; }
		span.Step.Failed { background-color:#E14B4B; }
		span.Step.InProgress { border:solid 1px black; }
		.Error { color:red; }
		#errors {font-size:8pt;}
	</style>
	<body>
		<h3>�������� ������</h3>
		������� �� ������ ��� ������ �������� ��������: <input type="button" value="������" onclick="Init(this);" />

		<div id="indicator"></div>
		<div id="description"></div>
		
		<br />

		<iframe width="100%" height="300px" id="steps">
		</iframe>

		���:
		<div id="errors"></div>

<?php

	Foot();

?>