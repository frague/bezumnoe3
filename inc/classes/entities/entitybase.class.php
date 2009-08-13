<?

abstract class EntityBase {
	// Fields
	var $table = "";
	var $Id;

	var $FieldsNames = array();
	var $SearchTemplate = "";

	private $IdentityName = "";

	function __construct($id, $idName) {
		$this->Clear();
		$this->Id = $id;
		$this->IdentityName = $idName;
	}

	function IsEmpty() {
		return ($this->Id <= 0 || $this->Id == "");
	}
	
	function CheckSum($extended = false) {
		return 0;
	}

	// SQL methods
	function IsConnected() {
	 global $db;
		return $db != 0;
	}

	function Retrieve() {
		if (!$this->IsConnected()) {
			return false;
		}
		$this->GetById($this->Id);
	}

	function GetById($id) {
		return $this->FillByCondition("t1.".$this->IdentityName."=".SqlQuote($id));
	}

	function GetByCondition($condition, $expression = "", $show = 0) {
	 global $db;

		if (!$this->IsConnected()) {
			return false;
		}
		if (!$expression) {
			$expression = $this->ReadExpression();
		}
		$query = str_replace("##CONDITION##", $condition, $expression);

		if ($show) {
			echo "<pre>".$query."</pre>";
		}
		return $db->Query($query);
	}

	function GetRange($from = 0, $amount = 0, $condition = "1=1", $expression = "") {
		return $this->GetByCondition(
			$condition.($amount ? " LIMIT ".($from ? $from."," : "").$amount : ""),
			$expression
		);
	}

	function FillByCondition($condition, $expression = "", $show = 0) {
		if (!$this->IsConnected()) {
			return false;
		}
		$this->Clear();

		$q = $this->GetByCondition($condition, $expression, $show);
		if ($q->NumRows()) {
			$q->NextResult();
			$this->FillFromResult($q);
			return $q;
		} else {
			$this->Clear();
		}
	}

	function GetFieldset() {
		return array();
	}

	function HasErrors() {
		return "";
	}

	function GetResultsCount($condition) {
		if (!$condition) {
			$condition = "1=1";
		}
		$q = $this->GetByCondition(
			$condition, 
			"SELECT COUNT(1) AS RECORDS ".substr($this->ReadExpression(), strpos($this->ReadExpression(), "FROM")));
		$q->NextResult();
		return $q->Get("RECORDS");
	}


	function SaveChecked() {
		$errors = $this->HasErrors();
		if (!$errors) {
			$this->Save();
		}
		return $errors;
	}

	function Save($by_query = "") {
	 global $db;
		if (!$this->IsConnected()) {
			return false;
		}
		if ($by_query) {
			$q = $db->Query($by_query);
		} else {
		    $updateFlag = false;
			if (!$this->IsEmpty()) {
				$q = $db->Query("SELECT ".$this->IdentityName." FROM ".$this->table." WHERE ".$this->IdentityName."=".SqlQuote($this->Id));
				$updateFlag = $q->NumRows() > 0;
			}
			if ($updateFlag === true) {
				$q->Query($this->UpdateExpression());
			} else {
				$q = $db->Query($this->CreateExpression());
				$this->Id = $q->GetLastId();
			}
		}
		return mysql_error();
	}

	function Delete() {
		$result = false;
		if (!$this->IsEmpty()) {
			$result = $this->DeleteById($this->Id);
		}
		return $result;
	}
	
	function DeleteById($id) {
	 global $db;
		if (!$this->IsConnected()) {
			return false;
		}
		$this->Id = $id;
		$q = $db->Query($this->DeleteExpression());
		$this->Clear();
		return true;
	}


	// Abstract methods
	abstract function Clear();
	abstract function FillFromResult($result);

	abstract function CreateExpression();
	abstract function ReadExpression();
	abstract function UpdateExpression();
	abstract function DeleteExpression();

	// Static methods
}


?>