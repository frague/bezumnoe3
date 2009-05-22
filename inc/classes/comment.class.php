<?

class Comment {
	// Constants
	var $table = "_journal_comments";

	// Properties
	var $Id;
	var $PostId;
	var $Author;
	var $isAuthorized;
	var $Date;
	var $Body;

	function Comment($id = -1) {
		$this->Clear();
		$this->Id = $id;
	}

	function IsNotEmpty() {
		return $this->Id > 0;
	}

	function Touch() {
		$this->Date = time();
	}

	function Clear() {
		$this->Id = -1;
		$this->PostId = -1;
		$this->Author = "";
		$this->isAuthorized = false;
		$this->Comment = "";

		$this->Touch();
	}

	function Retrieve($db) {
		$this->GetById($this->Id, $db);
	}

	function FillFromResult($result) {
		$this->Id = $result->Get("id");
		$this->PostId = $result->Get("post_id");
		$this->Author = $result->Get("author");
		$this->isAuthorized = $result->Get("is_authorized") != 0;
		$this->Date = $result->Get("date");
		$this->Comment = $result->Get("comment");
	}

	function GetById($id, $db) {
		$this->Clear();
		$this->Id = $id;

		$q = $this->GetByCondition("t1.id=".SqlQuote($this->Id), $db);
		if ($q->NumRows()) {
			$q->NextResult();
			$this->FillFromResult($q);
			$this->Id = $id;
		} else {
			$this->Id = -1;
		}
	}

	function GetByCondition($condition, $db) {
		$query = str_replace("##CONDITION##", $condition, $this->ReadExpression());
		return $db->Query($query);
	}

	function Save($db) {
	    $updateFlag = false;
		if ($this->IsNotEmpty()) {
			$q = $db->Query("SELECT id FROM ".$this->table." WHERE id=".SqlQuote($this->Id));
			$updateFlag = $q->NumRows() > 0;
		}
		if ($updateFlag === true) {
			$q->Query($this->UpdateExpression());
		} else {
			$q = $db->Query($this->CreateExpression());
			$this->Id = $q->GetLastId();
		}
		return $q->AffectedRows() > 0;
	}

	function Delete($db) {
		$result = false;
		if ($this->IsNotEmpty()) {
			$result = $this->DeleteById($this->Id, $db);
			$this->Clear();
		}
		return $result;
	}
	
	function DeleteById($id, $db) {
		$this->Id = $id;
		$q = $db->Query($this->DeleteExpression());
		$this->Clear();
	}

	function ToString() {
		$s = "<ul type=square><li>Id: ".$this->Id;
		$s.= "<li>PostId: ".$this->PostId;
		$s.= "<li>Date: ".$this->Date;
		$s.= "<li>Body: ".$this->Comment;
		$s.= "<li>Author: ".$this->Author;
		$s.= "<li>is authorized: ".($this->isAuthorized ? "true" : "false");
		if (!$this->IsNotEmpty()) {
			$s .= "<li> <b>Comment is unsaved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.id,
	t1.post_id,
	t1.author AS author,
	t1.is_authorized,
	t1.date AS date,
	t1.comment AS comment
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." (post_id, author, is_authorized, date, comment) VALUES(".SqlQuote($this->PostId).", '".SqlQuote($this->Author)."','".($this->isAuthorized ? 1 : 0)."','".SqlQuote($this->Date)."','".SqlQuote($this->Comment)."')";
	}

	function UpdateExpression() {
		return "UPDATE ".$this->table." SET post_id=".SqlQuote($this->PostId).", author='".SqlQuote($this->Author)."',is_authorized=".($this->isAuthorized ? 1 : 0).",date='".SqlQuote($this->Date)."',comment='".SqlQuote($this->Comment)."' WHERE id=".SqlQuote($this->Id);
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE id=".SqlQuote($this->Id);
	}
}

?>