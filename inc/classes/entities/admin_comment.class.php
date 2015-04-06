<?

class AdminComment extends EntityBase {
    // Constants
    const table = "admin_comments";

    const ADMIN_COMMENT_ID = "ADMIN_COMMENT_ID";
    const USER_ID = "USER_ID";
    const DATE = "DATE";
    const CONTENT = "CONTENT";
    const ADMIN_LOGIN = "ADMIN_LOGIN";
    const SEVERITY = "SEVERITY";

    const SEVERITY_NORMAL = 0;
    const SEVERITY_WARNING = 1;
    const SEVERITY_ERROR = 2;

    // Properties
    var $UserId;
    var $Date;
    var $Content;
    var $AdminLogin;
    var $Severity;

    // Fields

    function AdminComment($id = -1) {
        $this->table = self::table;
        parent::__construct($id, self::ADMIN_COMMENT_ID);

        $this->SearchTemplate = "t1.".self::CONTENT." LIKE '%#WORD#%'";
        $this->Order = "t1.".self::DATE." DESC";
    }

    function Clear() {
        $this->Id = -1;
        $this->UserId = -1;
        $this->Date = NowDateTime();
        $this->Content = "";
        $this->AdminLogin = "";
        $this->Severity = 0;
    }

    function FillFromResult($result) {
        $this->Id = $result->Get(self::ADMIN_COMMENT_ID);
        $this->UserId = $result->GetNullableId(self::USER_ID);
        $this->Date = $result->Get(self::DATE);
        $this->Content = $result->Get(self::CONTENT);
        $this->AdminLogin = $result->Get(self::ADMIN_LOGIN);
        $this->Severity = $result->Get(self::SEVERITY);
    }

    function GetByUserId($userId, $from = 0, $amount = 0, $condition = "") {
        return $this->GetByCondition(
            ($condition  ? $condition." AND " : "").
            "t1.".self::USER_ID."=".round($userId).
            " ORDER BY ".$this->Order.($amount ? " LIMIT ".($from ? $from."," : "").$amount : ""));
    }

    function GetUserCommentsCount($userId, $condition = "") {
        if (!$condition) {
            $condition = "1=1";
        }
        $userId = round($userId);
        $condition = ($userId ? $condition." AND t1.".self::USER_ID."=".$userId : $condition);

        $q = $this->GetByCondition(
            $condition, 
            "SELECT COUNT(1) AS RECORDS FROM ".self::table." t1 WHERE ##CONDITION##");
        $q->NextResult();
        return $q->Get("RECORDS");
    }

    function __tostring() {
        $s = "<ul type=square>";
        $s.= "<li>".self::ADMIN_COMMENT_ID.": ".$this->Id."</li>\n";
        $s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
        $s.= "<li>".self::DATE.": ".$this->Date."</li>\n";
        $s.= "<li>".self::CONTENT.": ".$this->Content."</li>\n";
        $s.= "<li>".self::ADMIN_LOGIN.": ".$this->AdminLogin."</li>\n";
        $s.= "<li>".self::SEVERITY.": ".$this->Severity."</li>\n";
        if ($this->IsEmpty()) {
            $s.= "<li> <b>Admin comment is not saved!</b>";
        }
        $s.= "</ul>";
        return $s;
    }

    function ToJs($login) {
        return "new acdto(\"".JsQuote($this->Date).
"\",\"".JsQuote($this->Content).
"\",\"".JsQuote($this->AdminLogin).
"\",".round($this->Severity).
",\"".JsQuote($login)."\"".
")";
    }

    // SQL
    function ReadExpression() {
        return "SELECT 
    t1.".self::ADMIN_COMMENT_ID.",
    t1.".self::USER_ID.",
    t1.".self::DATE.", 
    t1.".self::CONTENT.",
    t1.".self::ADMIN_LOGIN.",
    t1.".self::SEVERITY."
FROM
    ".$this->table." AS t1 
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table." 
(".self::USER_ID.", 
".self::DATE.", 
".self::CONTENT.",
".self::ADMIN_LOGIN.",
".self::SEVERITY."
)
VALUES
(".NullableId($this->UserId).", 
'".SqlQuote($this->Date)."', 
'".SqlQuote($this->Content)."',
'".SqlQuote($this->AdminLogin)."',
'".round($this->Severity)."'
)";
    }

    function UpdateExpression() {
        $result = "UPDATE ".$this->table." SET 
".self::USER_ID."=".NullableId($this->UserId).", 
".self::DATE."='".SqlQuote($this->Date)."', 
".self::CONTENT."='".SqlQuote($this->Content)."', 
".self::ADMIN_LOGIN."='".SqlQuote($this->AdminLogin)."',
".self::SEVERITY."='".round($this->Severity)."'
WHERE
    ".self::ADMIN_COMMENT_ID."=".SqlQuote($this->Id);
        return $result;
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table." WHERE ".self::ADMIN_COMMENT_ID."=".SqlQuote($this->Id);
    }

    function ReadWithNameExpression() {
        return "SELECT 
    t1.".self::DATE.", 
    t1.".self::CONTENT.",
    t1.".self::ADMIN_LOGIN.",
    t1.".self::SEVERITY.",
    t2.".User::LOGIN."
FROM
    ".$this->table." AS t1 
    LEFT JOIN ".User::table." AS t2 ON t2.".User::USER_ID."=t1.".self::USER_ID."
WHERE
    ##CONDITION##";
    }

}

?>