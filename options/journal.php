<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>

<h2>������:</h2>

<div id="journal_exists">
	<div id="no_journals">
		<p>������ (����) ��������� ��� ����� � ��������� ������������ �������, � ������� �� ������� �����������
		��������� ��������� � ��������������� �������. ������������������ ������������ ���� ������ ��������������
		��������� ������ �������.
		<p>��� �������� ������������ ������� ������� � ���������, ������� �������� � �������� ������ ������� � �������
		������ "������". ��� ������ ����� ������.
	</div>

	<div id="Selector">
		<h4 id="Title">��������� �������:</h4>
		<select id="FORUM_ID" name="FORUM_ID" class="Wide" onchange="this.obj.React()"></select>
		<input type="checkbox" id="SHOW_JOURNALS" name="SHOW_JOURNALS" checked onchange="this.obj.Bind()"> <label for="SHOW_JOURNALS">�������</label> 
		<input type="checkbox" id="SHOW_FORUMS" name="SHOW_FORUMS" onchange="this.obj.Bind()"> <label for="SHOW_FORUMS">������</label> 
		<input type="checkbox" id="SHOW_GALLERIES" name="SHOW_GALLERIES" onchange="this.obj.Bind()"> <label for="SHOW_GALLERIES">�������</label> 
	</div>

	<div id="Spoilers"></div>

	<ul class="Links" id="Links">
		<li> <a id="linkNewPost" href="javascript:void(0)" onclick="EditJournalPost(this.obj, 0)" class="Add">������� ������ � �������</a>
		<li> <a id="linkDeleteJournal" href="javascript:void(0)" onclick="" class="Delete">������� ������</a>
	</ul>
</div>
