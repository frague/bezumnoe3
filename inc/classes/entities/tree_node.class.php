<?

class TreeNode extends EntityBase {
	// Constants
	const table = "tree";

	const NODE_ID = "NODE_ID";
	const FIRST_USER_ID = "FIRST_USER_ID";
	const SECOND_USER_ID = "SECOND_USER_ID";
	const RELATION_TYPE = "RELATION_TYPE";

	// Properties
	var $FirstUserId;
	var $SecondUserId;
	var $RelationType;

	// Fields

	function TreeNode() {
		$this->table = self::table;
		parent::__construct("", self::NODE_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->FirstUserId = "";
		$this->SecondUserId = "";
		$this->RelationType = "";
	}

	function IsValid() {
		return $this->FirstUserId && $this->SecondUserId && $this->RelationType;

	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::NODE_ID);
		$this->FirstUserId = $result->Get(self::FIRST_USER_ID);
		$this->SecondUserId = $result->Get(self::SECOND_USER_ID);
		$this->RelationType = $result->Get(self::RELATION_TYPE);
	}

	function GetTreeUsers() {
		return $this->GetByCondition("", $this->TreeUsersExpression());
	}

	function ToJs() {
		return "[".$this->FirstUserId.",".$this->SecondUserId.",'".$this->RelationType."']";
	}

	function UserInfoToJs($r) {
		return "[".$r->Get(User::USER_ID).",'".JsQuote($r->Get(User::LOGIN))."',".$r->Get(Profile::GENERATION)."]";
	}
	
	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::NODE_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::FIRST_USER_ID.": ".$this->FirstUserId."</li>\n";
		$s.= "<li>".self::SECOND_USER_ID.": ".$this->SecondUserId."</li>\n";
		$s.= "<li>".self::RELATION_TYPE.": ".$this->RelationType."</li>\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Tree node is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::NODE_ID.",
	t1.".self::FIRST_USER_ID.",
	t1.".self::SECOND_USER_ID.",
	t1.".self::RELATION_TYPE."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##
";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." (
	".self::FIRST_USER_ID.", 
	".self::SECOND_USER_ID.", 
	".self::RELATION_TYPE."
) VALUES (
	'".SqlQuote($this->FirstUserId)."', 
	'".SqlQuote($this->SecondUserId)."', 
	'".SqlQuote($this->RelationType)."'
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::FIRST_USER_ID."=".SqlQuote($this->FirstUserId).", 
".self::SECOND_USER_ID."=".SqlQuote($this->SecondUserId).", 
".self::RELATION_TYPE."='".SqlQuote($this->RelationType)."'
WHERE 
	".self::NODE_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::NODE_ID."=".SqlQuote($this->Id);
	}

	function TreeUsersExpression() {
		return "SELECT t1.".User::USER_ID.", t1.".User::LOGIN.", t2.".Profile::GENERATION." 
FROM ".User::table." t1
JOIN ".Profile::table." t2 ON t1.".User::USER_ID."=t2.".Profile::USER_ID."
WHERE t2.".Profile::GENERATION." IS NOT NULL
ORDER BY t2.".Profile::GENERATION." ASC";
	}
}

?>