<?

class Registry extends EntityBase implements ArrayAccess {
    // Constants
    const table = "registry";

    const KEY = "REGISTRY_KEY";
    const VALUE = "VALUE";

    // Properties
    private $container;

    function Registry() {
        $this->table = self::table;
        parent::__construct("", self::KEY);
    }

    public function offsetSet($offset, $value) {
        $this->container[$offset] = $value;
    }
    public function offsetExists($offset) {
        return isset($this->container[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->container[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }   
    
    function Clear() {
        $this->container = array();
    }

    function FillFromResult($result) {
        $this->Clear();
        if ($result) {
            for ($i = 0; $i < $result->NumRows(); $i++) {
                $result->NextResult();
                $this->offsetSet($result->Get(self::KEY), $result->Get(self::VALUE));
            }
        }
    }

    function __tostring() {
        $s = "<ul type=square>";
        foreach ($this->container as $key => $value) {
            $s.= "<li>".$key.": ".$value."</li>\n";
        }
        $s .= "</ul>";
        return $s;
    }

    // SQL

    function Save($key, $value) {
     global $db;
        if (!$this->IsConnected()) {
            return false;
        }

        $this->Key = $key;
        $this->Value = $value;

        $q = $db->Query("SELECT ".self::KEY." FROM ".$this->table." WHERE ".self::KEY."='".SqlQuote($key)."'");
        $updateFlag = $q->NumRows() > 0;

        if ($updateFlag === true) {
            $q->Query($this->UpdateExpression());
        } else {
            $q = $db->Query($this->CreateExpression());
        }
        $this->container[$key] = $value;

        unset($this->Key);
        unset($this->Value);

        return mysql_error();
    }

    function Delete($key) {
     global $db;
        if (!$this->IsConnected()) {
            return false;
        }
        $this->Key = $key;
        $q = $db->Query($this->DeleteExpression());
        unset($this->Key);
        unset($this->container[$key]);
        return true;
    }



    function ReadExpression() {
        return "SELECT 
    t1.".self::KEY.",
    t1.".self::VALUE."
FROM 
    ".$this->table." AS t1 
WHERE
    ##CONDITION##";
    }

    function CreateExpression() {
        return "INSERT INTO ".$this->table." (
    ".self::KEY.", 
    ".self::VALUE."
) VALUES (
    '".SqlQuote($this->Key)."', 
    '".SqlQuote($this->Value)."'
)";
    }

    function UpdateExpression() {
        $result = "UPDATE ".$this->table." SET 
".self::VALUE."='".SqlQuote($this->Value)."'
WHERE 
    ".self::KEY."='".SqlQuote($this->Key)."'";
        return $result;
    }

    function DeleteExpression() {
        return "DELETE FROM ".$this->table." WHERE ".self::KEY."='".SqlQuote($this->Key)."'";
    }
}

?>