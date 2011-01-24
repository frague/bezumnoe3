<?

class OpenIdProvider extends EntityBase {
	// Constants
	const table = "openid_providers";

	const OPENID_PROVIDER_ID = "OPENID_PROVIDER_ID";
	const TITLE = "TITLE";
	const URL = "URL";
	const IMAGE = "IMAGE";

	const LOGIN_CHUNK = "##LOGIN##";

	// Properties
	var $Id;
	var $Title;
	var $Url;
	var $Image;

	// Fields

	function OpenIdProvider($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::OPENID_PROVIDER_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->Title = "";
		$this->Url = "";
		$this->Image = "";
	}

	function GetAll() {
		return $this->GetByCondition("1=1");
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::OPENID_PROVIDER_ID);
		$this->Title = $result->Get(self::TITLE);
		$this->Url = $result->Get(self::URL);
		$this->Image = $result->Get(self::IMAGE);
	}

	function MakeUrl($login) {
		if ($this->IsEmpty()) {
			return "";
		}
		return str_replace(self::LOGIN_CHUNK, $login, $this->Url);
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::OPENID_PROVIDER_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::TITLE.": ".$this->Title."</li>\n";
		$s.= "<li>".self::URL.": ".$this->Url."</li>\n";
		$s.= "<li>".self::IMAGE.": ".$this->Image."</li>\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>OpenID provider is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	function ToPrint($index, $el) {
		return "<a href=\"javascript:void(0)\" onclick=\"SetOpenID(".$this->Id.", '".$el."', this)\" title=\"".HtmlQuote($this->Title)."\"><img src=\"/img/openid/".$this->Image."\"></a>";
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::OPENID_PROVIDER_ID.", 
	t1.".self::TITLE.",
	t1.".self::URL.",
	t1.".self::IMAGE."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##
ORDER BY
	t1.".self::OPENID_PROVIDER_ID." ASC";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::TITLE.",
".self::URL.",
".self::IMAGE.")
VALUES
('".SqlQuote($this->Title)."',
('".SqlQuote($this->Url)."',
('".SqlQuote($this->Image)."')";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::TITLE."='".SqlQuote($this->Title)."'
WHERE 
	".self::TAG_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::OPENID_PROVIDER_ID."=".SqlQuote($this->Id);
	}
}

?>