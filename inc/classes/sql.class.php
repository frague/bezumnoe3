<?

class Query {

	var $Q;

	var $Queried = false;
	var $Record;
	var $RecordIndex = 0;

	var $Debugging = false; 		// Debugging mode

	var $DB_stored = 0;
	
	function Query($query, $DB = 0) {
		if (!$DB && !$this->DB_stored) {
			error("��� ���������� � MySQL ��������!");
			return false;
		} else {
			if ($DB) {
				$this->DB_stored = $DB;
			}
		}
		
		if (!$DB) {
			$DB = $this->DB_stored;
		}

		$this->Q=@mysql_query($query, $DB);

		if ($this->Debugging) {
			error("Query: <strong>".$query."</strong>");
		}

		if ($this->Q) {
			$this->Queried = true;
		} else {
			error("������ � �������: <strong>".$query."</strong>: <span class='Red'>".mysql_error()."</span>!");
		}
	}

	function NumRows() {
		if (!$this->Queried) {
			error("�� ���������� ���������� �������!");
			return false;
		}
		return @mysql_numrows($this->Q);
	}

	function Seek($row = 0) {
		if (!$this->Queried) {
			error("�� ���������� ���������� �������!");
			return false;
		}
		
    	mysql_data_seek ($this->Q, $row);
	    $this->RecordIndex = $row;
	}

	function NextResult() {
		if (!$this->Queried) {
			error("�� ���������� ���������� �������!");
			return false;
		}
		
    	$this->Record = @mysql_fetch_array($this->Q);
	    $this->RecordIndex++;
	    if ($this->RecordIndex - 1 >= $this->NumRows() || !$this->NumRows()) {
    		@mysql_data_seek ($this->Q, 0);
    		return false;
	    } else {
	    	return true;
	    }
	}

	function Get($name) {
		return $this->Record[$name];
	}

	function GetNullableId($name) {
		$val = round($this->Get($name));
		return $val > 0 ? $val : -1;
	}

	function GetNullableIdExt($name) {
		$val = round($this->Get($name));
		return $val != 0 ? $val : -1;
	}

	function GetLastId() {
		return @mysql_insert_id($this->DB_stored);
	}

	function AffectedRows() {
		return @mysql_affected_rows($this->DB_stored);
	}
}




class SQL {
    var $DB;
    
    var $DB_name;
	var $Host;
	var $User;
	var $UserPassword;

	var $Connected=0;

	var $Errors=array();
	var $ErrorsIndex=0;


	function SQL($db_name,$host="",$user,$pass) {
		$this->DB_name=$db_name;
		$this->Host=$host;
		$this->User=$user;
		$this->UserPassword=$pass;

		$this->DB = @mysql_connect($this->Host, $this->User, $this->UserPassword);
		if (!$this->DB) {
			error("������ ����������� � MySQL �������!");
		}

		@mysql_query("set names 'cp1251'", $this->DB);


		if (@mysql_select_db($this->DB_name, $this->DB)) {
			$this->Connected=1;
		} else {
			error("���������� ������� ���� ������ <b>$db_name</b>!");
		}
	}


	function Query($query) {
		if (!$query) return 0;
//		if (!$this->Connected) return 0;

		$a = new Query($query, $this->DB);
		return $a;
	}
}


?>