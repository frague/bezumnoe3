<?
    require "menu_base.php";

?>

<table class="Wide">
	<tr>
		<td>

<h4>������� ��� ������� (����):</h4>
<select id="SKIN_TEMPLATE_ID" name="SKIN_TEMPLATE_ID" onchange="PreviewSkin(this)">
	<option VALUE="">&nbsp;&lt;����������� ������&gt;<?
	
		$skin = new JournalSkin();
		echo $skin->ToSelect();

	?>
</select>

		</td><td id="previewCell" style="display:none;padding-left:8px;width:220px;">

<h4>������������ �������:</h4>
<div id="skinPreview"></div>
		
		</td>
	</tr>
</table>

<div id="templates">
	<h4>�������� ������ �������� �������:</h4>
	<textarea id="BODY" name="BODY" onclick="Maximize(this)"></textarea>

	<h4>������ ���������� ���������:</h4>
	<textarea id="MESSAGE" name="MESSAGE" onclick="Maximize(this)"></textarea>

	<h4>����� ����������� (CSS):</h4>
	<textarea id="CSS" name="CSS" onclick="Maximize(this)"></textarea>
</div>
<br>