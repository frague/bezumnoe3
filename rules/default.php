<?php
	
	$root = "../";
	require_once $root."server_references.php";
	require $root."inc/ui_parts/templates.php";

	$user = GetAuthorizedUser(true);

	Head("������� ����");

	require_once $root."references.php";

?>
����� ������������ � ���� � �� ����� ������� �������� ������������� ������������ � ������� ���������. 
��� ������������� ����� �������� � ���������� ������������ ������ ���� ����������� � <a href="/forum1/">�����</a>.

<h3>������� ����</h3>
��� &laquo;�������� �������� � ����������� �����&raquo; ����������������. 
��� ��������, ��� � ���� ���������� ������������ ������� ��������� � �� �� ����������� ������ ��������������. 
� ����������� ���� ������ ����������� ���� �����������, ��������� ����. 

<p>��������� ������ ���� ������ ���������� ������� ��� ������������� ������� � ��� �� ����, ������������ ����������������. 
� ������, ���� �������� �� �������� ������� ���������, ������� � ��� ������������� �������� �� �������������� ������������� ����. 
��� ������� ������� ����� ���� ��������� ���������� ������, ��� ������� ������������.
<p>������ �� ������������� �������� ��������������� ����������� �� ������������ <a href="javascript:void(0)" onclick="Feedback()">e-mail</a> ��� 
���������� � <a href="/forum1/">������</a>.

<h3>� ���� � � ������� �����������:</h3>
���, ��� �� ��������� ���������� ���������, � ������ ��������, ��������, � ��� ��� ��������.

<h3>� ���� � ������� �������������:</h3>
	<li> ���������� � ��������� �� ���� ���������� ����
	<li> ������������ ��� ������� ����, �������� ����������� ������c������� � ���� ��� �������
	<li> � ����� ��������� ������������ ������������� ������������ ������ ������ �� ���� � ���

<p><a name=punishment></a>
<h3>� ���� � � ������� �����������:</h3>
	<li> ������ � ������������ ������������� ������� (� ��� ����� � ������� ���� &laquo;���&raquo;, &laquo;3.14����&raquo; � �.�.)
	<li> ������� (�.�. ��������� ������������� ���������, ������������ � ���������� ������� ���������� ������������� ��������, �������� �����)
	<li> ������������ ����������� HTML-����
	<li> ������������� ��������� ����� ������������, � ������������ � � ����������������
	<li> �������������� �������������� "CAPS LOCK" (��������� ����)
	<li> ������������ ��� �������� ��������� ���������� ���������������
	<li> ������������ ���������� ���� � ��������� ����� ���� � ����
	<li> ������������ ����� ������ ������
	<li> ������������� ���������� ����������������� ��� ���������� ����������
	<li> ����������� � ���� ��� ����� ��� �������� ��������� ���������� ��������� ��� ��������� ��� ��������� ��������� ������
	<li> �������� ���������� ���������������� ���� � �������
	<li> &laquo;�����������&raquo; �������
	<li> ����������� � ���� � ��� �������� ���������, �������������� ���������������� �A ��� ���������� ��������� ������. ������������� ��������� 
		�� ����� ����� ������� ����� ���������, ����������� ��� ������ ����������� ��� �� �� ���������� ����������������
<p>
<div class="Comment">����� ���� ������ �� ������������� ���� �� ������������ ���������, ������������ ���� ������������� ��������� �������� ����� 
<b>������</b> ����������� ���� (�� ��� ���������� ���������).

<p>����������������� ������������ ��������� �� ���� ����� <b>������������</b> ����������� ����.
<p>� ����������� ������ �������������� � ����� ��������� ����������������� ���� (��������� ��������������, ������� �������� �� ���� (���). 
� ������� ������� ����������� ����� ��������� ���� (�������� �������� ������������, �������� ������ ����������������� ���������� � "������ ������").</div>

<h3>�������� ������� ����:</h3>
��� ��������� ���-������� ��������������� ��� ������������ ���, ������� �����������, ���� ������������� � 
��������� � ���������� � �������. � ������ ����������� �������� ���������, �� ��������������� ��������.
<ul><b>���� ������</b>:
	<li> <a href="/forum1/">�������� �������� � ����������� �����</a> - �����, ����������� ��������� �����, �����������, ��������, �������� � ������������� � �����-�����, ���������� � �����. 
	<li class="li2"> <a href="/forum2/">����������� �� ���������</a> - ����� ��� ���������� ����������� �� ������������������ ����, ������������ ������, �������, ����� ����, ���������� � �.�. 
	<li class="li1"> <a href="/forum3/">��������� ����� ����</a> - ������ �� ����������� ������/�������� � ����. 
	<li> <a href="/forum4/">�������� ����������</a> - �� ������������� �������� ���� ����� ������ ���������� ��� ����, ����� ������� ����� ����������� �� �������� ��������� ���� ��������.
	<li> � ������...
</ul>

<h3>�������� ����:</h3>
� ���� ���������� �������� �������������, ������������ ������� ���� ������� ������������ � ������������ �������. �� ������ ���� ������� ����������� ��������� ���������:
<ul>
	<li>������ &laquo;�������&raquo; (1) ��������������� ��� �����������. ������������ ����� ������� ������� ���������� � ��������� �������
	<li class="li3">������ &laquo;V.I.P&raquo; ����� ��������� �������:
	<ul class="Artistic">
		<li> &laquo;2&raquo; - ����������� �� ������� ������������, ����� ������ ���������� � ���� �� ��� ����������� 
		(��� ���� ������������ �������� ����������� ����� ��������� �� ����/����� �� ���� � ��� ������� ����� ������� ����� �� <b>����������</b>)
		<li class="li1"> &laquo;3-4&raquo; - ����������� �� �����, ��� ����� 2 ������� ���������� � ����, � �� ���������� �������������� 
		(��� ���� ������������ �������� ������ � ����� ���������� ������, ��� <i>���������</i> � <u>������������</u>)
		<li class="li2"> &laquo;5-6&raquo; - ����������� �� �����, ��� ����� 3 ������ ���������� � ���� �� ���������� �������������� 
		(��� ���� ������������ �������� ����������� ����� ���� � ������� ������)
		<li> &laquo;10&raquo; - ����������� ��������������� ��� ����� ����������������� � ��������� ������������� ���� 
		(����� ����� ���� ����������� ������ � ���� ����)
	</ul>

	<li>������ &laquo;��������&raquo; ����� �������������, ������������������ � ���� ����� 1 ����
	<li class="li2">������ &laquo;���������&raquo; - �������, ������������� �� ����������� ����������� ������ � ����, ����������� ������ � �������� 
	(����������� ���������� ������)
	<li class="li1">������ &laquo;�������������&raquo; - �������, �������� � ���� �� ����������� ������, ���������� ����������� � ������������� 
	��������� ������������� (����������� ���������� ������)
</ul>


<h3>����������� �����:</h3>
���������� ����������� ������� ���� ����������� � ���, ��� ����� ���������� ����������� �����. 
����� �������� ����������� �����, � ����� ����������� � ����������. 
��������� ����� ���� - ��� ���� ����������� ����������� ������, ����������� ������ � ������ ����������. 
����� ������� � ��� ���������� ���������� �������� ��� � ���� ������� � ��������������� <a href="/forum3/">������</a>.


<?php

	Foot();
?>