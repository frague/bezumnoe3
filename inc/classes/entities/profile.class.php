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
	const PHOTO_UPLOAD_DATE = "PHOTO_UPLOAD_DATE";
	const AVATAR = "AVATAR";
	const ABOUT = "ABOUT";
	const REGISTERED = "REGISTERED";
	const LAST_VISIT = "LAST_VISIT";
	const GENERATION = "GENERATION";
	const RATING = "RATING";
	const LAST_RATING = "LAST_RATING";

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
	var $PhotoUploadDate;
	var $Avatar;
	var $About;
	var $Registered;
	var $LastVisit;
	var $Generation;
	var $Rating;
	var $LastRating;

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
		$this->PhotoUploadDate = "";
		$this->Avatar = "";
		$this->About = "";
		$this->Registered = NowDateTime();
		$this->LastVisit = NowDateTime();
		$this->Generation = "";
		$this->Rating = 0;
		$this->LastRating = 0;
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
		$this->PhotoUploadDate = $result->Get(self::PHOTO_UPLOAD_DATE);
		$this->Avatar = $result->Get(self::AVATAR);
		$this->About = $result->Get(self::ABOUT);
		$this->Registered = $result->Get(self::REGISTERED);
		$this->LastVisit = $result->Get(self::LAST_VISIT);
		$this->Generation = $result->Get(self::GENERATION);
		$this->Rating = $result->Get(self::RATING);
		$this->LastRating = $result->Get(self::LAST_RATING);
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
		$s.= "<li>".self::PHOTO_UPLOAD_DATE.": ".$this->PhotoUploadDate."</li>\n";
		$s.= "<li>".self::AVATAR.": ".$this->Avatar."</li>\n";
		$s.= "<li>".self::ABOUT.": ".$this->About."</li>\n";
		$s.= "<li>".self::REGISTERED.": ".$this->Registered."</li>\n";
		$s.= "<li>".self::LAST_VISIT.": ".$this->LastVisit."</li>\n";
		$s.= "<li>".self::GENERATION.": ".$this->Generation."</li>\n";
		$s.= "<li>".self::RATING.": ".$this->Rating."</li>\n";
		$s.= "<li>".self::LAST_RATING.": ".$this->LastRating."</li>\n";
		if ($this->IsEmpty()) {
			$s.= "<li> <b>Profile is not saved!</b>";
		}

		$s.= "</ul>";
		return $s;
	}

	function GetRatingDelta() {
		$delta = $this->Rating - $this->LastRating;
		return ($delta > 0 ? "+".$delta : ($delta ? $delta : ""));
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

	function GetPhotos($search = "'") {
		$limit = 21;
		if ($search == "'") {
			$search = "1=1";
			$limit = 20;
		} else {
			$search = substr(trim($search), 0, 20);
			$search = "t2.".User::LOGIN." LIKE '".SqlQuote((strlen($search) > 1 ? "%" : "").$search."%")."'";
		}
		return $this->GetByCondition(
			$search,
			$this->ReadPhotosExpression().($limit ? " LIMIT ".$limit : "")
		);
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
	t1.".self::PHOTO_UPLOAD_DATE.",
	t1.".self::AVATAR.",
	t1.".self::ABOUT.",
	t1.".self::REGISTERED.",
	t1.".self::LAST_VISIT.",
	t1.".self::GENERATION.",
	t1.".self::RATING.",
	t1.".self::LAST_RATING."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	function ReadPhotosExpression() {
		return "SELECT 
	t1.".self::USER_ID.",
	t1.".self::PHOTO.",
	t1.".self::PHOTO_UPLOAD_DATE.",
	t2.".User::LOGIN."
FROM 
	".$this->table." AS t1 
	JOIN ".User::table." AS t2 ON t2.".User::USER_ID."=t1.".self::USER_ID."
WHERE
	##CONDITION##
ORDER BY 
	(".self::PHOTO." IS NOT NULL) DESC,
	".self::PHOTO_UPLOAD_DATE." DESC,
	t2.".User::LOGIN." ASC";
	}

	function RatingExpression() {
		return "SELECT
	t2.".User::LOGIN.",
	t2.".User::USER_ID.",
	t1.".self::RATING.",
	t1.".self::LAST_RATING."
FROM
	".$this->table." AS t1 
	JOIN ".User::table." AS t2 ON t2.".User::USER_ID."=t1.".self::USER_ID."
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
".self::PHOTO_UPLOAD_DATE.", 
".self::AVATAR.", 
".self::ABOUT.", 
".self::REGISTERED.", 
".self::LAST_VISIT.",
".self::GENERATION.",
".self::RATING.",
".self::LAST_RATING."
)
VALUES
('".SqlQuote($this->UserId)."', 
'".SqlQuote($this->Email)."', 
'".SqlQuote($this->Name)."', 
".Nullable(SqlQuote($this->Gender)).", 
'".SqlQuote($this->Birthday)."', 
'".SqlQuote($this->City)."', 
'".SqlQuote($this->Icq)."', 
'".SqlQuote($this->Url)."', 
".Nullable(SqlQuote($this->Photo)).", 
".Nullable(SqlQuote($this->PhotoUploadDate)).", 
".Nullable(SqlQuote($this->Avatar)).", 
'".SqlQuote($this->About)."', 
'".SqlQuote($this->Registered)."', 
'".SqlQuote($this->LastVisit)."',
".(strlen($this->Generation) > 0 ? $this->Generation : "NULL").",
".round($this->Rating).",
".round($this->LastRating)."
)";
	}

	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::USER_ID."='".SqlQuote($this->UserId)."', 
".self::EMAIL."='".SqlQuote($this->Email)."', 
".self::NAME."='".SqlQuote($this->Name)."', 
".self::GENDER."=".Nullable(SqlQuote($this->Gender)).", 
".self::BIRTHDAY."='".SqlQuote($this->Birthday)."', 
".self::CITY."='".SqlQuote($this->City)."', 
".self::ICQ."='".SqlQuote($this->Icq)."', 
".self::URL."='".SqlQuote($this->Url)."', 
".self::PHOTO."=".Nullable(SqlQuote($this->Photo)).", 
".self::PHOTO_UPLOAD_DATE."=".Nullable(SqlQuote($this->PhotoUploadDate)).", 
".self::AVATAR."=".Nullable(SqlQuote($this->Avatar)).", 
".self::ABOUT."='".SqlQuote($this->About)."', 
".self::REGISTERED."='".SqlQuote($this->Registered)."', 
".self::LAST_VISIT."='".SqlQuote($this->LastVisit)."', 
".self::GENERATION."=".(strlen($this->Generation) > 0 ? $this->Generation : "NULL").",
".self::RATING."=".round($this->Rating).",
".self::LAST_RATING."=".round($this->LastRating)."
WHERE 
	".self::PROFILE_ID."=".SqlQuote($this->Id);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::PROFILE_ID."=".SqlQuote($this->Id);
	}

	public static function PushRatingsExpression() {
		return "UPDATE ".Profile::table." SET ".Profile::LAST_RATING."=".Profile::RATING;
	}

	/* Static methods */

	public static function MakeLink($userId = 0, $text = "Инфо") {
		return "<a href='/info.php?id=".$userId."'>".$text."</a>";
	}

}

?>