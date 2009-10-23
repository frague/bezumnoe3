<?

class Profile extends EntityBase {
	// Constants
	const table = "profiles";

	const PROFILE_ID = "PROFILE_ID";
	const USER_ID = "USER_ID";
	const EMAIL = "EMAIL";
	const NAME = "NAME";
	const GENDER = "GENDER";
	const BIRTHDAY = "BIRTHDAY";
	const CITY = "CITY";
	const ICQ = "ICQ";
	const URL = "URL";
	const PHOTO = "PHOTO";
	const AVATAR = "AVATAR";
	const ABOUT = "ABOUT";
	const REGISTERED = "REGISTERED";
	const LAST_VISIT = "LAST_VISIT";
	const GENERATION = "GENERATION";

	// Properties
	var $UserId;
	var $Email;
	var $Name;
	var $Gender;
	var $Birthday;
	var $City;
	var $Icq;
	var $Url;
	var $Photo;
	var $Avatar;
	var $About;
	var $Registered;
	var $LastVisit;
	var $Generation;

	var $FieldsNames = array(
		"Имя",
		"Пол",
		"День рождения",
		"Город",
		"ICQ",
		"Адрес сайта",
		"О себе"
	);

	// Constructor
	function Profile($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::PROFILE_ID);
	}

	function Clear() {
		$this->Id = -1;
		$this->UserId = -1;
		$this->Email = "";
		$this->Name = "";
		$this->Gender = "";
		$this->Birthday = "";
		$this->City = "";
		$this->Icq = "";
		$this->Url = "";
		$this->Photo = "";
		$this->Avatar = "";
		$this->About = "";
		$this->Registered = NowDateTime();
		$this->LastVisit = NowDateTime();
		$this->Generation = "";
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::PROFILE_ID);
		$this->UserId = $result->Get(self::USER_ID);
		$this->Email = $result->Get(self::EMAIL);
		$this->Name = $result->Get(self::NAME);
		$this->Gender = $result->Get(self::GENDER);
		$this->Birthday = $result->Get(self::BIRTHDAY);
		$this->City = $result->Get(self::CITY);
		$this->Icq = $result->Get(self::ICQ);
		$this->Url = $result->Get(self::URL);
		$this->Photo = $result->Get(self::PHOTO);
		$this->Avatar = $result->Get(self::AVATAR);
		$this->About = $result->Get(self::ABOUT);
		$this->Registered = $result->Get(self::REGISTERED);
		$this->LastVisit = $result->Get(self::LAST_VISIT);
		$this->Generation = $result->Get(self::GENERATION);
	}

	function FillFromHash($hash) {
		$this->Name = UTF8toWin1251($hash[self::NAME]);
		$this->Gender = $hash[self::GENDER];
		$this->Birthday = $hash[self::BIRTHDAY];
		$this->City = UTF8toWin1251($hash[self::CITY]);
		$this->Icq = UTF8toWin1251($hash[self::ICQ]);
		$this->Url = UTF8toWin1251($hash[self::URL]);
		$this->About = UTF8toWin1251($hash[self::ABOUT]);
	}

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::PROFILE_ID.": ".$this->Id."</li>\n";
		$s.= "<li>".self::USER_ID.": ".$this->UserId."</li>\n";
		$s.= "<li>".self::EMAIL.": ".$this->Email."</li>\n";
		$s.= "<li>".self::NAME.": ".$this->Name."</li>\n";
		$s.= "<li>".self::GENDER.": ".$this->Gender."</li>\n";
		$s.= "<li>".self::BIRTHDAY.": ".$this->Birthday."</li>\n";
		$s.= "<li>".self::CITY.": ".$this->City."</li>\n";
		$s.= "<li>".self::ICQ.": ".$this->Icq."</li>\n";
		$s.= "<li>".self::URL.": ".$this->Url."</li>\n";
		$s.= "<li>".self::PHOTO.": ".$this->Photo."</li>\n";
		$s.= "<li>".self::AVATAR.": ".$this->Avatar."</li>\n";
		$s.= "<li>".self::ABOUT.": ".$this->About."</li>\n";
		$s.= "<li>".self::REGISTERED.": ".$this->Registered."</li>\n";
		$s.= "<li>".self::LAST_VISIT.": ".$this->LastVisit."</li>\n";
		$s.= "<li>".self::GENERATION.": ".$this->Generation."</li>\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Profile is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	function GetFieldset() {
		return array(
			$this->Name,
			$this->Gender,
			$this->Birthday,
			$this->City,
			$this->Icq,
			$this->Url,
			$this->About
		);
	}

	function ToJs($user, $adminView) {
		if (!$user || $user->IsEmpty()) {
			return;
		}
		$s = "[\"".
JsQuote($user->Login)."\", \"".
JsQuote($this->Email)."\", \"".
JsQuote($this->Name)."\", \"".
JsQuote($this->Gender)."\", \"".
JsQuote($this->Birthday)."\", \"".
JsQuote($this->City)."\", \"".
JsQuote($this->Icq)."\", \"".
JsQuote($this->Url)."\", \"".
JsQuote($this->Photo)."\", \"".
JsQuote($this->Avatar)."\", \"".
JsQuote($this->About)."\",  \"".
JsQuote($this->Registered)."\",  \"".
JsQuote($this->LastVisit)."\"]";
		return $s;
	}

	// SQL
	function GetByUserId($id) {
		$this->FillByCondition("t1.".self::USER_ID."=".SqlQuote($id));
	}

	// Overloaded methods
	function ReadExpression() {
		return "SELECT 
	t1.".self::PROFILE_ID.",
	t1.".self::USER_ID.",
	t1.".self::EMAIL.",
	t1.".self::NAME.",
	t1.".self::GENDER.",
	t1.".self::BIRTHDAY.",
	t1.".self::CITY.",
	t1.".self::ICQ.",
	t1.".self::URL.",
	t1.".self::PHOTO.",
	t1.".self::AVATAR.",
	t1.".self::ABOUT.",
	t1.".self::REGISTERED.",
	t1.".self::LAST_VISIT.",
	t1.".self::GENERATION."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::USER_ID.", 
".self::EMAIL.", 
".self::NAME.", 
".self::GENDER.", 
".self::BIRTHDAY.", 
".self::CITY.", 
".self::ICQ.", 
".self::URL.", 
".self::PHOTO.", 
".self::AVATAR.", 
".self::ABOUT.", 
".self::REGISTERED.", 
".self::LAST_VISIT.",
".self::GENERATION."
)
VALUES
('".SqlQuote($this->UserId)."', 
'".SqlQuote($this->Email)."', 
'".SqlQuote($this->Name)."', 
'".SqlQuote($this->Gender)."', 
'".SqlQuote($this->Birthday)."', 
'".SqlQuote($this->City)."', 
'".SqlQuote($this->Icq)."', 
'".SqlQuote($this->Url)."', 
".Nullable(SqlQuote($this->Photo)).", 
".Nullable(SqlQuote($this->Avatar)).", 
'".SqlQuote($this->About)."', 
'".SqlQuote($this->Registered)."', 
'".SqlQuote($this->LastVisit)."',
".(strlen($this->Generation) > 0 ? $this->Generation : "NULL")."
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::USER_ID."='".SqlQuote($this->UserId)."', 
".self::EMAIL."='".SqlQuote($this->Email)."', 
".self::NAME."='".SqlQuote($this->Name)."', 
".self::GENDER."='".SqlQuote($this->Gender)."', 
".self::BIRTHDAY."='".SqlQuote($this->Birthday)."', 
".self::CITY."='".SqlQuote($this->City)."', 
".self::ICQ."='".SqlQuote($this->Icq)."', 
".self::URL."='".SqlQuote($this->Url)."', 
".self::PHOTO."=".Nullable(SqlQuote($this->Photo)).", 
".self::AVATAR."=".Nullable(SqlQuote($this->Avatar)).", 
".self::ABOUT."='".SqlQuote($this->About)."', 
".self::REGISTERED."='".SqlQuote($this->Registered)."', 
".self::LAST_VISIT."='".SqlQuote($this->LastVisit)."', 
".self::GENERATION."=".(strlen($this->Generation) > 0 ? $this->Generation : "NULL")." 
WHERE 
	".self::PROFILE_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::PROFILE_ID."=".SqlQuote($this->Id);
	}

	/* Static methods */

	public static function MakeLink($userId = 0, $text = "Инфо") {
		return "<a href='/info.php?id=".$userId."'>".$text."</a>";
	}

}

?>