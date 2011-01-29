<?

class ForumRecordBase extends EntityBase {
	// Constants
	const table = "forum_records";

	const RECORD_ID 	= "RECORD_ID";
	const FORUM_ID 		= "FORUM_ID";
	const INDEX 		= "IND";
	const TYPE 			= "TYPE";
	const AUTHOR 		= "AUTHOR";
	const USER_ID 		= "USER_ID";
	const TITLE 		= "TITLE";
	const CONTENT 		= "CONTENT";
	const DATE 			= "DATE";
	const ADDRESS 		= "IP";
	const CLICKS 		= "CLICKS";
	const GUID 			= "GUID";
	const IS_COMMENTABLE = "IS_COMMENTABLE";
	const IS_DELETED 	= "IS_DELETED";

	const THREAD_ID 	= "THREAD_ID";
	const THREAD_ORDER 	= "THREAD_ORDER";
	const DEPTH 		= "DEPTH";
	const VISIBLE_TO	= "VISIBLE_TO";
	
	const UPDATE_DATE 	= "UPDATE_DATE";
	const ANSWERS_COUNT = "ANSWERS_COUNT";
	const DELETED_COUNT = "DELETED_COUNT";

	const ID_PARAM 		= "id";
	const INDEX_DIVIDER = "_";
	const THREADS_COUNT = "THREADS_COUNT";
	const RECORDS 		= "RECORDS";

	// Type constants
	const TYPE_PUBLIC		= 0;
	const TYPE_FRIENDS_ONLY	= 1;
	const TYPE_PROTECTED	= 1;
	const TYPE_PRIVATE		= 2;

	// First index length
	const FIRST_INDEX_LENGTH = 4;

	// Properties
	var $ForumId;
	var $Index;
	var $Type;
	var $Author;
	var $UserId;
	var $Title;
	var $Content;
	var $Date;
	var $Address;
	var $Clicks;
	var $Guid;
	var $IsCommentable;
	var $IsDeleted;

	var $ThreadId;
	var $ThreadOrder;
	var $Depth;
	var $VisibleTo;

	var $UpdateDate;
	var $AnswersCount;
	var $DeletedCount;

	var $Level;

	// Fields
	function ForumRecordBase($id = -1) {
		$this->table = self::table;
		parent::__construct($id, self::RECORD_ID);

		$this->SearchTemplate = "t1.".self::TITLE." LIKE '%#WORD#%' OR t1.".self::CONTENT." LIKE '%#WORD#%'";
	}

	function Clear() {
		$this->Id 			= -1;
		$this->ForumId		= -1;
		$this->Index		= "";
		$this->Type			= self::TYPE_PUBLIC;
		$this->Author		= "";
		$this->UserId		= 0;
		$this->Title		= "";
		$this->Content		= "";
		$this->Date			= NowDateTime();
		$this->Address		= "";
		$this->Clicks		= 0;
		$this->Guid			= MakeGuid(20);
		$this->IsCommentable= 1;
		$this->IsDeleted	= 0;

		$this->ThreadId		= 0;
		$this->ThreadOrder	= 0;
		$this->Depth		= 0;
		$this->VisibleTo	= -1;
		
		$this->UpdateDate	= NowDateTime();
		$this->AnswersCount	= 0;
		$this->DeletedCount	= 0;

		$this->Level = 0;
	}

	/* Conditional properties */
	
	function IsPublic() {
		return $this->Type == self::TYPE_PUBLIC;
	}

	function IsProtected() {
		return $this->Type == self::TYPE_PROTECTED;
	}
	
	function IsPrivate() {
		return $this->Type == self::TYPE_PRIVATE;
	}

	function IsTopic() {
		return strlen($this->Index) == self::FIRST_INDEX_LENGTH;
	}

	function GetTopicIndex() {
		return substr($this->Index, 0, self::FIRST_INDEX_LENGTH);
	}
	
	function VisibleTo($user, $isFriend = 0) {
		if ($this->Type == self::TYPE_PUBLIC ||
			($user && !$user->IsEmpty() && (
				($this->Type = self::TYPE_FRIENDS_ONLY && $isFriend) ||
				$this->UserId == $user->User->Id
				)
			)
		) {
			return true;
		}
		return false;
	}

	function FillFromResult($result) {
		$this->Id = $result->Get(self::RECORD_ID);
		$this->ForumId = $result->Get(self::FORUM_ID);
		$this->Index = $result->Get(self::INDEX);
		$this->Type = $result->Get(self::TYPE);
		$this->Author = $result->Get(self::AUTHOR);
		$this->UserId = $result->GetNullableId(self::USER_ID);
		$this->Title = $result->Get(self::TITLE);
		$this->Title = ($this->Title ? $this->Title : "Без названия");

		$this->Content = $result->Get(self::CONTENT);
		$this->Date = $result->Get(self::DATE);
		$this->Address = $result->Get(self::ADDRESS);
		$this->Clicks = $result->Get(self::CLICKS);
		$this->Guid = $result->Get(self::GUID);
		$this->IsCommentable = $result->Get(self::IS_COMMENTABLE);
		$this->IsDeleted = $result->Get(self::IS_DELETED);

		$this->ThreadId = $result->Get(self::THREAD_ID);
		$this->ThreadOrder = $result->Get(self::THREAD_ORDER);
		$this->Depth = $result->Get(self::DEPTH);
		$this->VisibleTo = $result->Get(self::VISIBLE_TO);
		
		$this->UpdateDate = $result->Get(self::UPDATE_DATE);
		$this->AnswersCount = $result->Get(self::ANSWERS_COUNT);
		$this->DeletedCount = $result->Get(self::DELETED_COUNT);

		$this->Level = substr_count($this->Index, self::INDEX_DIVIDER);
	}

	function FillFromHash($hash) {
		$this->ForumId = round($hash[self::FORUM_ID]);
		$this->Type = round($hash[self::TYPE]);

		$this->Id = $hash[self::RECORD_ID];
		$this->UserId = $hash[self::USER_ID];
		$this->Date = $hash[self::DATE];
		$this->Title = UTF8toWin1251($hash[self::TITLE]);
		$this->Content = UTF8toWin1251($hash[self::CONTENT]);
		$this->IsCommentable = Boolean($hash[self::IS_COMMENTABLE]);
	}
	
	function ToExtendedString($prevLevel, $avatar = "", $alias = "", $user = "", $lastVisit = "", $skipLi = 0) {
	  global $PathToAvatars, $ServerPathToAvatars, $root;

	  	$result = "<a name=\"cm".$this->Id."\"></a>";

		if ($prevLevel != $this->Level) {
			$less = ($prevLevel < $this->Level);
			for ($i = 0; $i < abs($this->Level - $prevLevel); $i++) {
				$result .= $less ? "<ul>" : "</ul>";
			}
		}

		/* Protected & Hidden messages */
		$cssClass = $this->Date > $lastVisit ? "Recent" : "";
		$cssClass .= $this->IsProtected() ? " Protected" : "";

		if (!$skipLi) {
			$result .= "<li class='".($this->IsDeleted ? "Hidden " : "")."'>";
		}

		$result .= "<article>\n<table class='".$cssClass."'><tr>";
		if ($avatar) {
			$result .= "<th>".User::InfoLink($this->UserId, HtmlImage($PathToAvatars.$avatar, $root.$ServerPathToAvatars.$avatar))."</th>";
		}

		$result .= "<td><header><h4>".$this->Title."</h4></header>";
		$result .= ($this->UserId > 1 ? User::InfoLink($this->UserId, $this->Author) : $this->Author);
		if ($alias) {
			$result .= " (<a href='".JournalSettings::MakeHref($alias)."'>журнал</a>)";
		}
		$result .= "<div>".PrintableDate($this->Date)."</div>";
		$result .= "</td></tr></table>";

		$content = MakeTextQuotes($this->Content);
		$result .= "<div class='Content ".$cssClass."'>".OuterLinks(MakeLinks($content, true))."</div>";

		$result .= "<div class='Links'>";
		if ($this->IsCommentable) {
			$result .= "<a name='r".round($this->Id)."' href='#r".round($this->Id)."' id='replyLink' onclick='ForumReply(this,".$this->Id.",".$this->ForumId.")'>Ответить</a>";
		}
		if ($user && ($user->IsAdmin() || $user->User->Id == $this->UserId)) {
			$result .= "&nbsp;<a href='javascript:void(0)' onclick='ForumDelete(this,".$this->Id.",".$this->ForumId.")'>Удалить</a>";
		}
		$result .= "</div></article>";

		return $result;
	}

	function ToFullJs() {
		return "[\"".
round($this->Id)."\",\"".
JsQuote($this->Title)."\",\"".
JsQuote($this->Content)."\",\"".
JsQuote($this->Date)."\",".
round($this->Type).",".
Boolean($this->IsCommentable).",".
round($this->ForumId)."];";
	}

	function ToJs($mark = "") {
		$title = strip_tags($this->Title);
		$content = substr(strip_tags($this->Content), 0, 100);
/*		if ($mark) {
			$title = HightlightWords($title, $mark);
			$content = HightlightWords($content, $mark);
		}*/
		return "new jrdto(\"".
JsQuote($this->Id)."\",\"".
JsQuote($title)."\",\"".
JsQuote($content)."\",\"".
JsQuote($this->Date)."\",".
$this->AnswersCount.",".
round($this->Type).")";
	}

	// Gets forum's top-level threads
	function GetForumThreads($forumId, $access, $from = 0, $amount = 0, $search = "", $sortByDate = false) {
		return $this->GetByCondition(($search ? $search." AND " : "")."
			t1.".self::FORUM_ID."=".round($forumId)." AND 
			LENGTH(t1.".self::INDEX.") = ".self::FIRST_INDEX_LENGTH."
			ORDER BY ".($sortByDate ? self::DATE : self::UPDATE_DATE)." DESC".
			($amount ? " LIMIT ".($from ? $from."," : "").$amount : ""),

			$this->ReadThreadExpression($access), "", 1);
	}


	// Gets threads count for given forum
	function GetForumThreadsCount($forumId, $access = Forum::FULL_ACCESS, $condition = "1=1") {		// MOVE TO FORUM CLASS ?
	  global $db;
	  	$q = $this->GetByCondition($condition, $this->CountForumThreadsExpression($forumId, $access));
	  	$q->NextResult();
	  	return $q->Get(self::THREADS_COUNT);
	}

	// Gets threads count for given forum by given tag
	function GetForumThreadsCountByTag($forumId, $access = Forum::FULL_ACCESS, $tag) {		// MOVE TO FORUM CLASS ?
	  global $db;
	  	$q = $this->GetByCondition("t5.".Tag::TITLE."='".SqlQuote($tag)."'", $this->CountForumThreadsByTagExpression($forumId, $access));
	  	$q->NextResult();
	  	return $q->Get(self::THREADS_COUNT);
	}
	
	// Gets forum thread by index (first 4 digits)
	function GetByIndex($forumId, $access, $index, $from, $amount) {
		$index = preg_replace("[^0-9_]", "", $index);
		if (!$index) {
			return;
		}

		return $this->GetByCondition("
			t1.".self::FORUM_ID."=".round($forumId)." AND 
			t1.".self::INDEX." LIKE '".$index."%'
			ORDER BY ".$this->ThreadOrderExpression()."
			LIMIT ".($from ? $from."," : "").$amount,
			$this->ReadThreadExpression($access));
	}

	function GetReplyRecord($replyId, $forumId) {
		$this->FillByCondition(
			self::FORUM_ID."=".round($forumId)."
		ORDER BY 
			(".self::RECORD_ID." = ".round($replyId).") DESC, 
			SUBSTR(".self::INDEX.", 1, ".self::FIRST_INDEX_LENGTH.") DESC
		LIMIT 1");
	}

	function GetNextSubIndexOf($index, $forumId) {
		$q = $this->GetByCondition(
			self::INDEX." LIKE '".SqlQuote($index)."_%' AND
			LENGTH(".self::INDEX.")=".(3 + strlen($index))." AND
			".self::FORUM_ID."=".round($forumId)."
		ORDER BY ".self::INDEX." DESC
		LIMIT 1", 
			$this->GetSubIndexExpression());

		if ($q->NumRows()) {
			$q->NextResult();
			$i = $q->Get(self::INDEX);
			return substr($i, 0, -2).sprintf("%02d", 1 + substr($i, -2));
		}
		return $index."_01";
	}

	function GetAdditionalUserInfo() {
		return $this->GetByCondition(
			"t1.".Profile::USER_ID."=".round($this->UserId),
			$this->GetAdditionalUserDataExpression()
		);
	}

	function SetChildType() {
	  global $db;

		if ($this->IsEmpty()) {
			return;
		}
		if (!$this->IsConnected()) {
			return 0;
		}
		$db->Query($this->SetChildTypeExpression());
	}
	
	function UpdateAnswersCount() {
	  global $db;
	  	
		if (!$this->IsConnected()) {
			return 0;
		}

	  	if ($this->Index && $this->ForumId) {
	  		$q = $db->Query($this->CountAnswersExpression());
	  		$q->NextResult();
	  		$answers = round($q->Get(0)) - 1;
  			$deleted = round($q->Get(1));
	  		$q = $db->Query($this->UpdateAnswersCountExpression($answers, $deleted));
	  		return $answers;
	  	}
		return 0;
	}

	function UpdateThreadDate() {
		if ($this->Index && $this->ForumId) {
			$q = $this->GetByCondition(
				self::INDEX." LIKE '".$this->GetTopicIndex()."%' AND
				".self::FORUM_ID."=".round($this->ForumId),
				$this->UpdateThreadDateExpression()
			);
		}	
	}
	
	function DeleteByRecordIndex($index) {
		return $this->GetByCondition(
			self::INDEX." LIKE '".round($index)."_%",
			"DELETE FROM ".$this->table." WHERE ##CONDITION##"
		);
	}

	/* Printing methods */

	function __tostring() {
		$s = "<ul type=square>";
		$s.= "<li>".self::RECORD_ID." = ".$this->Id."</li>\n";
		$s.= "<li>".self::FORUM_ID." = ".$this->ForumId."</li>\n";
		$s.= "<li>".self::INDEX." = ".$this->Index."</li>\n";
		$s.= "<li>".self::TYPE." = ".$this->Type."</li>\n";
		$s.= "<li>".self::AUTHOR." = ".$this->Author."</li>\n";
		$s.= "<li>".self::USER_ID." = ".$this->UserId."</li>\n";
		$s.= "<li>".self::TITLE." = ".$this->Title."</li>\n";
		$s.= "<li>".self::CONTENT." = ".$this->Content."</li>\n";
		$s.= "<li>".self::DATE." = ".$this->Date."</li>\n";
		$s.= "<li>".self::ADDRESS." = ".$this->Address."</li>\n";
		$s.= "<li>".self::CLICKS." = ".$this->Clicks."</li>\n";
		$s.= "<li>".self::GUID." = ".$this->Guid."</li>\n";
		$s.= "<li>".self::IS_COMMENTABLE." = ".$this->IsCommentable."</li>\n";
		$s.= "<li>".self::IS_DELETED." = ".$this->IsDeleted."</li>\n";

		$s.= "<li>".self::THREAD_ID." = ".$this->ThreadId."</li>\n";
		$s.= "<li>".self::THREAD_ORDER." = ".$this->ThreadOrder."</li>\n";
		$s.= "<li>".self::DEPTH." = ".$this->Depth."</li>\n";
		$s.= "<li>".self::VISIBLE_TO." = ".$this->VisibleTo."</li>\n";
		
		$s.= "<li>".self::UPDATE_DATE." = ".$this->UpdateDate."</li>\n";
		$s.= "<li>".self::ANSWERS_COUNT." = ".$this->AnswersCount."</li>\n";
		$s.= "<li>".self::DELETED_COUNT." = ".$this->DeletedCount."</li>\n";

		if ($this->IsEmpty()) {
			$s.= "<li> <b>Forum Record is not saved!</b>";
		}
		$s.= "</ul>";
		return $s;
	}


	/* Saving methods */
	
	function DoSave() {
	  global $db;

	  	$this->Save();
	  	$this->UpdateAnswersCount();
	  	$this->UpdateThreadDate();
	}

	function SaveAsReplyTo($reply_record_id) {
		if ($this->ForumId <= 0) {
			return false;
		}

		$record = new ForumRecord();
		$record->GetReplyRecord($reply_record_id, $this->ForumId);

		if ($record->IsEmpty()) {
			$this->Index = "0001";	// 1st forum post
		} else {
			if ($record->Id == $reply_record_id) {
				// Make reply
				$this->Index = $record->GetNextSubIndexOf($record->Index, $record->ForumId);
				if ($record->IsProtected()) {
					$this->Type = ForumRecord::TYPE_PROTECTED;	//If parent is protected - reply should also be.
				}
			} else {
				// Post new topic
				$this->Index = sprintf("%0".self::FIRST_INDEX_LENGTH."d", 1 + $record->Index);
			}
		}
		$this->DoSave();
		return true;
	}

	function SaveAsTopic() {
		return $this->SaveAsReplyTo(0);
	}

	// SQL
	function ReadExpression() {
		return "SELECT 
	t1.".self::RECORD_ID.",
	t1.".self::FORUM_ID.",
	t1.".self::INDEX.",
	t1.".self::TYPE.",
	t1.".self::AUTHOR.",
	t1.".self::USER_ID.",
	t1.".self::TITLE.",
	t1.".self::CONTENT.",
	t1.".self::DATE.",
	t1.".self::ADDRESS.",
	t1.".self::CLICKS.",
	t1.".self::GUID.",
	t1.".self::IS_COMMENTABLE.",
	t1.".self::IS_DELETED.",
	t1.".self::UPDATE_DATE.",
	t1.".self::ANSWERS_COUNT.",
	t1.".self::DELETED_COUNT.",
	t1.".self::THREAD_ID.",
	t1.".self::THREAD_ORDER.",
	t1.".self::DEPTH.",
	t1.".self::VISIBLE_TO."
FROM 
	".$this->table." AS t1 
WHERE
	##CONDITION##";
	}

	// Transforms read expression into read thread one
	function ReadThreadExpression($access = Forum::NO_ACCESS) {
		$s = str_replace("FROM", ",
	t2.".Profile::AVATAR.",
	t3.".JournalSettings::ALIAS.",
	t3.".JournalSettings::LAST_MESSAGE_DATE."
FROM", $this->ReadExpression());

		$s = str_replace("WHERE", "
	LEFT JOIN ".Profile::table." AS t2 ON t2.".Profile::USER_ID."=t1.".self::USER_ID."
	LEFT JOIN ".JournalSettings::table." AS t3 ON t3.".JournalSettings::FORUM_ID."=t1.".self::FORUM_ID."
WHERE
	".self::AccessCondition($access),
	$s);
		return $s;
	}

	// Makes condition from access
	function AccessCondition($access = Forum::NO_ACCESS) {
		$hideDeleted = "t1.".self::IS_DELETED."<>1";
		switch ($access) {
			case Forum::FULL_ACCESS:
				return "1=1 AND ";
			case Forum::FRIENDLY_ACCESS:
			case Forum::READ_ONLY_ACCESS:
			case Forum::READ_ADD_ACCESS:
				return "t1.".self::TYPE."='".self::TYPE_PUBLIC."' AND ".$hideDeleted." AND ";
//				return "(t1.".self::TYPE."='".self::TYPE_PUBLIC."' OR t1.".self::TYPE."='".self::TYPE_FRIENDS_ONLY."') AND ".$hideDeleted." AND ";
			default:
				return "1<>1 AND ";
		}
	}

	// Makes SQL expression to determine acces to records
	// mixed from different forums
	function AccessExpression($userId, $forumTable, $forumUserTable, $forumRecordTable = "t1", $seeDeleted = 0) {
		$hideDeleted = "(".$forumRecordTable.".".self::IS_DELETED."<>1)";
		$userId = round($userId);
		$result = "";
		if ($userId) {
			// Access for given User ID
			$result .= "((";
			// Public forum, public messages
			$result .= "(((".$forumTable.".".Forum::IS_PROTECTED."<>1 AND ".$forumRecordTable.".".self::TYPE."='".self::TYPE_PUBLIC."') OR ";
			// Forum user
			$result .= "(".$forumUserTable.".".ForumUser::ACCESS."=".Forum::READ_ONLY_ACCESS." AND (".$forumRecordTable.".".self::TYPE."='".self::TYPE_PUBLIC."' OR ".$forumRecordTable.".".self::TYPE."='".self::TYPE_PROTECTED."'))) AND ".$hideDeleted.") OR ";
			// Forum moderator
			$result .= "(".$forumUserTable.".".ForumUser::ACCESS."=".Forum::FULL_ACCESS.") OR ";
		    // Forum owner
			$result .= "(".$forumTable.".".Forum::LINKED_ID."='".$userId."'))";	
			$result .= $seeDeleted ? ")" : " AND ".$hideDeleted.")";
		} else {
			// Anonymous access
			// Public forum
			$result .= "((".$forumTable.".".Forum::IS_PROTECTED."<>1) AND ";
			// And public messages
			$result .= "(".$forumRecordTable.".".self::TYPE."='".self::TYPE_PUBLIC."') AND ";
			$result .= $hideDeleted.")";
		}
		return $result;
	}

	function CreateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::FORUM_ID.",
".self::INDEX.",
".self::TYPE.",
".self::AUTHOR.",
".self::USER_ID.",
".self::TITLE.",
".self::CONTENT.",
".self::DATE.",
".self::ADDRESS.",
".self::CLICKS.",
".self::GUID.",
".self::IS_COMMENTABLE.",
".self::IS_DELETED.",
".self::UPDATE_DATE."
".($this->AnswersCount > 0 ? ", ".self::ANSWERS_COUNT : "").",
".self::THREAD_ID.",
".self::THREAD_ORDER.",
".self::DEPTH.",
".self::VISIBLE_TO."
)
VALUES
(".round($this->ForumId).", 
'".SqlQuote($this->Index)."',
'".round($this->Type)."',
'".SqlQuote($this->Author)."',
".NullableId($this->UserId).",
'".SqlQuote($this->Title)."',
'".SqlQuote($this->Content)."',
'".SqlQuote($this->Date)."',
'".SqlQuote($this->Address)."',
'".round($this->Clicks)."',
'".SqlQuote($this->Guid)."',
".Boolean($this->IsCommentable).",
".Boolean($this->IsDeleted).",
'".SqlQuote($this->UpdateDate)."'
".($this->AnswersCount > 0 ? ", ".round($this->AnswersCount) : "").",
".round($this->ThreadId).",
".round($this->ThreadOrder).",
".round($this->Depth).",
".NullableId($this->VisibleTo)."
)";
	}
	
	function UpdateExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::FORUM_ID."=".round($this->ForumId).", 
".self::INDEX."='".SqlQuote($this->Index)."', 
".self::TYPE."='".round($this->Type)."', 
".self::AUTHOR."='".SqlQuote($this->Author)."', 
".self::TITLE."='".SqlQuote($this->Title)."', 
".self::CONTENT."='".SqlQuote($this->Content)."', 
".self::DATE."='".SqlQuote($this->Date)."', 
".self::ADDRESS."='".SqlQuote($this->Address)."', 
".self::CLICKS."=".round($this->Clicks).", 
".self::GUID."='".SqlQuote($this->Guid)."', 
".self::IS_COMMENTABLE."=".Boolean($this->IsCommentable).", 
".self::IS_DELETED."=".Boolean($this->IsDeleted).", 
".self::UPDATE_DATE."='".SqlQuote($this->UpdateDate)."',
".self::THREAD_ID."=".round($this->ThreadId).",
".self::THREAD_ORDER."=".round($this->ThreadOrder).",
".self::DEPTH."=".round($this->Depth).",
".self::VISIBLE_TO."=".NullableId($this->VisibleTo)."
WHERE 
	".self::RECORD_ID."=".SqlQuote($this->Id);
		return $result;
//".self::USER_ID."=".NullableId($this->UserId).", // To avoid inconsistency between message's author and updater

	}

	function UpdateAnswersCountExpression($answers, $deleted = 0) {
		$index = $this->GetTopicIndex();

		$result = "UPDATE ".$this->table." SET 
	".self::ANSWERS_COUNT."=".round($answers).",
	".self::DELETED_COUNT."=".round($deleted)."
WHERE 
	".self::INDEX."='".SqlQuote($index)."' AND 
	".self::FORUM_ID."=".round($this->ForumId);
		return $result;
	}

	function DeleteExpression() {
		return "DELETE FROM ".$this->table." WHERE ".self::RECORD_ID."=".SqlQuote($this->Id);
	}

	function DeleteThreadExpression() {
		return "DELETE FROM ".$this->table." 
WHERE ##CONDITION##";
	}

	function HideThreadExpression($hidden) {
		return "UPDATE ".$this->table." 
SET ".self::IS_DELETED."=".Boolean($hidden)."
WHERE ##CONDITION##";
	}

	function CountAnswersExpression() {
		$index = $this->GetTopicIndex();

		return "SELECT 
	COUNT(1) AS TOTAL, SUM(IS_DELETED) AS DELETED
FROM 
	".$this->table." 
WHERE 
	".self::INDEX." LIKE '".SqlQuote($index)."%' AND
	".self::FORUM_ID."=".round($this->ForumId)."
GROUP BY NULL";
	}

	// Expression to count forum threads
	function CountForumThreadsExpression($forumId, $access = Forum::NO_ACCESS) {
		$result = $this->ReadThreadExpression($access);
		$result = substr($result, strpos($result, "FROM"));
		$result = "SELECT 
	COUNT(".self::RECORD_ID.") AS ".self::THREADS_COUNT." ".$result." AND
	LENGTH(t1.".self::INDEX.")=".self::FIRST_INDEX_LENGTH." AND
	t1.".self::FORUM_ID."=".round($forumId);
		return $result;
	}

	function CountForumThreadsByTagExpression($forumId, $access = Forum::NO_ACCESS) {
		$result = $this->ReadThreadExpression($access);
		$result = substr($result, strpos($result, "FROM"));
		$result = "SELECT 
	COUNT(t1.".self::RECORD_ID.") AS ".self::THREADS_COUNT." ".$result." AND
	LENGTH(t1.".self::INDEX.")=".self::FIRST_INDEX_LENGTH." AND
	t1.".self::FORUM_ID."=".round($forumId);
		$result = str_replace(
			"WHERE", 
			"
	JOIN ".RecordTag::table." AS t4 ON t4.".RecordTag::RECORD_ID."=t1.".self::RECORD_ID."
	JOIN ".Tag::table." AS t5 ON t5.".Tag::TAG_ID."=t4.".RecordTag::TAG_ID."
WHERE", 
			$result);
		return $result;
	}

	function GetSubIndexExpression() {
		return "SELECT t1.".self::INDEX." FROM ".$this->table." t1 WHERE ##CONDITION##";
	}

	function UpdateThreadDateExpression() {
		return "UPDATE ".$this->table." SET ".self::UPDATE_DATE."='".NowDateTime()."' WHERE ##CONDITION##";
	}

	function GetAdditionalUserDataExpression() {
		return "SELECT
	t1.".Profile::AVATAR.",
	t2.".JournalSettings::ALIAS.",
	t2.".JournalSettings::LAST_MESSAGE_DATE."
FROM
	".Profile::table." AS t1
	LEFT JOIN ".JournalSettings::table." AS t2 ON t2.".JournalSettings::FORUM_ID."=".$this->ForumId."
WHERE
	##CONDITION##";
	}

	function ThreadOrderExpression() {
		$threadOrder = "";
		for ($i = 0; $i < 30; $i++) {
			$threadOrder .= ($threadOrder ? "," : "")."MID(CONCAT(".self::INDEX.",'_99'), ".(6 + $i*3).", 2) DESC";
		}
		return $threadOrder;
	}

	function UserAccessExpression($user, $hasForumAccess = 0) {
		$isDeleted = self::IS_DELETED." <> 1";
		$isProtected = self::TYPE." = ".self::TYPE_PUBLIC;

		if ($user && !$user->IsEmpty()) {
			if ($user->IsSuperAdmin()) {
				$isDeleted = "";
				$isProtected = "";
			} elseif ($user->IsAdmin()) {
				$isDeleted = "";
			} elseif ($hasForumAccess) {
				$isProtected = "";
			}
		}
		$result = $isDeleted.($isDeleted && $isProtected ? " AND " : "").$isProtected;
		return ($result ? " AND ".$result : "");
	}

	function MonthDaysExpression($forumId, $month, $year) {
		return "SELECT DISTINCT 
		DAY(t1.".self::DATE.") AS DAY
	FROM ".$this->table." AS t1
	WHERE 
		t1.".self::FORUM_ID."=".$forumId." AND
		LENGTH(t1.".self::INDEX.")=".self::FIRST_INDEX_LENGTH." AND
		t1.".self::DATE." > '".sprintf("%04d-%02d-%02d", $year, $month, 1)."' AND
		t1.".self::DATE." < '".sprintf("%04d-%02d-%02d", $year, $month, 31)."'";
	}

	function MigrateExpression() {
		return "INSERT INTO ".$this->table." 
(".self::RECORD_ID.",
".self::FORUM_ID.",
".self::INDEX.",
".self::TYPE.",
".self::AUTHOR.",
".self::USER_ID.",
".self::TITLE.",
".self::CONTENT.",
".self::DATE.",
".self::ADDRESS.",
".self::CLICKS.",
".self::GUID.",
".self::IS_DELETED.",
".self::IS_COMMENTABLE.",
".self::UPDATE_DATE."
)
VALUES
(".round($this->Id).", 
".round($this->ForumId).", 
'".SqlQuote($this->Index)."',
".round($this->Type).",
'".SqlQuote($this->Author)."',
".NullableId($this->UserId).",
'".SqlQuote($this->Title)."',
'".SqlQuote($this->Content)."',
'".SqlQuote($this->Date)."',
'".SqlQuote($this->Address)."',
'".round($this->Clicks)."',
'".SqlQuote($this->Guid)."',
".Boolean($this->IsCommentable).",
".Boolean($this->IsDeleted).",
'".SqlQuote($this->UpdateDate)."'
)";
	}

	function SetChildTypeExpression() {
		$result = "UPDATE ".$this->table." SET 
".self::TYPE."='".round($this->Type)."' 
WHERE 
".self::FORUM_ID."=".round($this->ForumId)." AND 
".self::INDEX." LIKE '".SqlQuote($this->Index)."%'";
		return $result;
	}

	/* Static methods */

	public static function GetLoggedCondition($user) {
		$loggedCondition = "t1.".self::TYPE."='".self::TYPE_PUBLIC."'";
		if ($user && !$user->IsEmpty()) {
			$loggedCondition = "(".$loggedCondition." OR t1.".self::USER_ID."=".$user->User->Id."
				OR (t1.".self::TYPE."='".self::TYPE_FRIENDS_ONLY."' AND t4.".JournalFriend::USER_ID." IS NOT NULL)
				OR (t1.".self::TYPE."='".self::TYPE_PRIVATE."' AND t3.".self::USER_ID."=".$user->User->Id.")
			)";
		}
		return $loggedCondition;
	}

	public static function GetDeletedCondition($user) {
		$deletedCondition = "t1.".self::IS_DELETED."=0";
		if ($user && $user->IsSuperAdmin()) {
			$deletedCondition = "1";
		}
		return $deletedCondition;
	}

	public static function GetRecordCommentsExpression($record_id, $user) {
		$result = "t1.".self::RECORD_ID."=".$record_id;
		if ($user && !$user->IsEmpty()) {
			$result .= " AND (t1.".self::TYPE."=".self::TYPE_PUBLIC." OR t1.".self::USER_ID."=".round($user->User->Id).")";
		} else {
			$result .= " AND t1.".self::TYPE."=".self::TYPE_PUBLIC;
		}
		return $result;
	}

}

?>