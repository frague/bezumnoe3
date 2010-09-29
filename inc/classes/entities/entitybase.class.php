<?

abstract class EntityBase {
	// Fields
	var $table = "";
	var $Id;

	var $FieldsNames = array();
	var $SearchTemplate = "";

	private $IdentityName = "";

	const USER_ID = "USER_ID";

	function __construct($id, $idName) {
		$this->Clear();
		$this->Id = $id;
		$this->IdentityName = $idName;
		$this->Order = "";
	}

	function __destruct() {
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

		if (!$condition) {
			$condition = "1=1";
		}

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

	function GetRange($from = 0, $amount = 0, $condition = "", $expression = "") {
		if (!$condition) {
			$condition = "1=1";
		}
		return $this->GetByCondition(
			$condition.
			($this->Order ? " ORDER BY ".$this->Order : "").
			($amount ? " LIMIT ".($from ? $from."," : "").$amount : ""),
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

	function GetResultsCount($condition = "") {
		if (!$condition) {
			$condition = "1=1";
		}
		$q = $this->GetByCondition(
			$condition, 
			"SELECT COUNT(1) AS RECORDS ".substr($this->ReadExpression(), strpos($this->ReadExpression(), "FROM")));
		$q->NextResult();
		return $q->Get("RECORDS");
	}

	function GetExpressionCount($expr) {
		$position = strpos($expr, "FROM");
		if ($position === false) {
			return 0;
		}
		$expr = "SELECT COUNT(1) AS RECORDS ".substr($expr, $position);

		$q = $this->GetByCondition(" ", $expr);
		if ($q->NumRows()) {
			$q->NextResult();
			return $q->Get("RECORDS");
		}
		return 0;
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

	function DeleteByUserId($id = 0) {
	 global $db;
		if (!$this->IsConnected()) {
			return false;
		}

		if (!$id) {
		 	$id = $this->UserId;
		}
		$id = round($id);

		if ($id <= 0) {
		 	return false;
		}
		$q = $db->Query($this->DeleteByUserExpression($id));
		//print "/* ".$this->DeleteByUserExpression($id)." */\n";
		return $q->AffectedRows() > 0;
	}

	// Abstract methods
	abstract function Clear();
	abstract function FillFromResult($result);

	abstract function CreateExpression();
	abstract function ReadExpression();
	abstract function UpdateExpression();
	abstract function DeleteExpression();

	function DeleteByUserExpression($id) {
		return "DELETE FROM ".$this->table." WHERE ".self::USER_ID."=".round($id);
	}

	// Static methods
}

// Global function for destroying all inherited objects of given one
function destroy(&$var) {
	if (is_object($var)) {
		$var->__destruct();
	}
	unset($var);
}


?>