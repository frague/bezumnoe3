<div id="Hider">
	<div id="ReplyForm"><?
		if (!$postAccess) {

	?>
		<span class="Warning">��� ������ ������ � ���������� ���������.</span>

	<?

		} elseif (!$user->IsEmpty()) {
	?>
		<div class='Red' id="ERROR"></div>
		<h4>���������:</h4>
		<input id="TITLE" name="TITLE" class="Wide" /><br>
		<h4>����� ���������:</h4>
		<textarea id="CONTENT" name="CONTENT" class="Wide" rows="10">
		</textarea><br>
		<div class="Right">	
			<div style="float:left" id="IsProtected">
				<input type="checkbox" name="IS_PROTECTED" id="IS_PROTECTED" /> ������� ���������
			</div>
			<input type="Button" value="���������" onclick="AddMessage()">
			<input type="Button" value="����������" onclick="MakeCite()" id="buttonCite">
			<input type="Button" value="��������" onclick="CancelReply()">
		</div>
	<?
		} else {
	?>
		<span class="Warning">��� ���������� ��������� � ������ ���������� <a href="/3/prototype">��������������</a> � �������.</span>

	<?
		}
	?>
	</div>
</div>
