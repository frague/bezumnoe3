<?
    require "menu_base.php";

?>

<h2 id="TITLE"></h2>

<h4>������� ��� ������� (����):</h4>
<div id="SKIN_TEMPLATE_ID" name="SKIN_TEMPLATE_ID" class="Radios"><?
	
		$skin = new JournalSkin();
		echo $skin->ToHtml("/img/journals", "DoShow('templates')");

		$q = $skin->GetByCondition("");
		for ($i = 0; $i < $q->NumRows(); $i++) {
			$q->NextResult();
			$skin->FillFromResult($q);
			echo $skin->ToHtml("/img/journals", "DoHide('templates')");
		}

?></div>

<div id="templates">
	<h2>����������� ������</h2>

	<h4>�������� ������ �������� �������:</h4>
	<textarea id="BODY" name="BODY" onclick="Maximize(this)"></textarea>

	<h4>������ ���������� ���������:</h4>
	<textarea id="MESSAGE" name="MESSAGE" onclick="Maximize(this)"></textarea>

	<h4>����� ����������� (CSS):</h4>
	<textarea id="CSS" name="CSS" onclick="Maximize(this)"></textarea>
</div>

<div id="templates1"></div>

<br />
