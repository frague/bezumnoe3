<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>
<h4>�������� ����:</h4>
<div id="TagsContainer">�� �������</div>

<h4>�������� ��� (�����):</h4>
<ul id="Errors"></ul>
<table>
	<tr>
		<td>
			<input name="SEARCH_TAG" id="SEARCH_TAG" maxlength="100" size="40" />
			<div class="Options">
				<ul id="FoundTags"></ul>
			</div>
		</td><td valign="top">
			<input type="image" src="/3/img/icons/add_to.gif" id="AddTag" onclick="if (!this.obj.Tab.Validators.AreValid()) {return false;};this.obj.AddNewTag(this)" /></td></tr></table>
