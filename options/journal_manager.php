<?
    require "menu_base.php";

	if ($user->IsEmpty()) {
		exit();
	}

?>

<h2>�������/������/�����������:</h2>

<div>
	<div id="Selector">
		<h4>�����:</h4>
		<INPUT id="SEARCH" name="SEARCH" class="Wide" onchange="this.obj.Request()" />
		<input type="checkbox" id="SHOW_JOURNALS" name="SHOW_JOURNALS" checked onchange="this.obj.Request()"> <label for="SHOW_JOURNALS">�������</label> 
		<input type="checkbox" id="SHOW_FORUMS" name="SHOW_FORUMS" onchange="this.obj.Request()"> <label for="SHOW_FORUMS">������</label> 
		<input type="checkbox" id="SHOW_GALLERIES" name="SHOW_GALLERIES" onchange="this.obj.Request()"> <label for="SHOW_GALLERIES">�����������</label> 
	</div>

	<div id="ForumsContainer"></div>

	<ul class="Links">
		<li> <a href="javascript:void(0)" onclick="ReRequestData(this)" id="linkRefresh" class="Refresh">�������� ������ � �������</a>
		<li id="CreateJournal"> <a href="javascript:void(0)" onclick="CreateForum(this.obj)" id="linkNewForum" class="Add">������� ������</a>
	</ul>
</div>
