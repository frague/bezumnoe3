<?
	// Base methods

	function SaveLog($content, $userId = -1, $adminLogin = "", $severity = 0) {
		$comment = new AdminComment();
		$comment->UserId = $userId;
		$comment->Content = $content;
		$comment->AdminLogin = $adminLogin;
		$comment->Severity = $severity;
		$comment->Save();
	}

	function SaveSystemLog($comment, $severity) {
		SaveLog($comment, -1, "", $severity);
	}

	function Compare($obj, $fieldset) {
		$result = "";
		$newFieldset = $obj->GetFieldset();
		for ($i = 0; $i < sizeof($obj->FieldsNames); $i++) {
			if ($newFieldset[$i] != $fieldset[$i]) {
				$result .= "<li> <b>".$obj->FieldsNames[$i].":</b> ".$fieldset[$i]." <b>&rarr;</b> ".$newFieldset[$i];
			}
		}
		return ($result ? "<ul class='ListChanges'>".$result."</ul>" : "");
	}

    // User-related logs
	
	function LogRegistration($userId, $login) {
		SaveLog("����������� ������������ <b>".$login."</b>.", $userId);
	};

	function LogProfileChanges($userId, $obj, $fieldset, $adminLogin) {
		$changes = Compare($obj, $fieldset);
		if ($changes) {
			SaveLog("��������� � �������:".$changes, $userId, $adminLogin);
		}
	};

	function LogBan($userId, $reason, $adminLogin, $till = "") {
		 SaveLog("<i>��� ".($reason ? "� ������������� &laquo;".$reason."&raquo;" : "��� �������� �������").($till ? " �� ".PrintableDate($till) : "")."</i>", $userId, $adminLogin);
	}

	function LogBanEnd($userId, $adminLogin) {
		SaveLog("<i>������������ ��������</i>", $userId, $adminLogin);
	}

	function LogStatusChange($userId, $oldStatus, $newStatus, $adminLogin) {
		SaveLog("<i>����� �������:<br>� ".$oldStatus->ToLog()." �� ".$newStatus->ToLog()."</i>", $userId, $adminLogin);
	}

	function LogAddressBan($userId, $adminLogin, $content, $parts) {
		SaveLog("<i>����� ".$content." ������� (".$parts.")</i>", $userId, $adminLogin, 1);
	}

	// Common admin logs (no AdminLogin & UserId defined)

?>