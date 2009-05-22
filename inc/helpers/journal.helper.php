<?
	/* Service methods */

    function ReplaceLinks($text, $messageId, $alias) {
		return str_replace(
			"##LINK##", 
			JournalSettings::MakeHref($alias, $messageId),
			$text);
    }

    function TagCutLink($caption="Ïîäðîáíåå") {
      global $cutCount;

    	return "<span class='cutlink'>(<a href='##LINK###cut".$cutCount++."'>".$caption."</a>)</span>";
    }
    
   	$cutCount = 1;
    function MakeCutLink($text) {
      global $cutCount;

    	$cutCount = 1;
    	return preg_replace("/##CUTLINK(=(([^#]|#[^#])+)){0,1}##/e", "TagCutLink('\\2')", $text);
    }

    function MakeCut() {
      global $cutCount;

    	return "<a name='cut".$cutCount++."'></a><div class='cut'><div></div></div>";
    }

	$tagsStack = array();
	function TagStack($tag, $order, $finish) {
	  global $tagsStack;

	    $tag = strtolower($tag);
	    
	    if (!eregi("^(li|p|link|img)$", $tag)) {
		    if ($order != "/") {
		    	$tagsStack[$tag]++;
	    	} else {
	    		if (round($tagsStack[$tag])>0) {
		    		$tagsStack[$tag]--;
		    	}
	    	}
	    }
		return "<".$order.$tag.$finish;
	}

	function CloseTags($text) {
	    $text = preg_replace("/<(\/?)([a-z0-9]+)(>| )/ie", "TagStack('\\2','\\1','\\3')", $text);
	    return $text;
	}

	function InjectionProtection($text) {
        $text = preg_replace("/( |:)on([a-z ]+=)/i", "\\1în\\2", $text);
		$text = preg_replace("/visibility/i","invisibility", $text);
		$text = preg_replace("/z-index/i","zzz-index", $text);
		$text = preg_replace("/noscript/i","yescript", $text);
		$text = preg_replace("/script/i","scrûpt", $text );
		$text = preg_replace("/iframe/i","ûframe", $text );
		$text = preg_replace("/<frame/i","<fràme", $text );
		$text = preg_replace("/display( *)[:=]/i","displày:", $text );
		$text = preg_replace("/absolute/i","absîlute", $text );
		$text = preg_replace("/scroll( *)=( *)no/i","scroll=óãó", $text );
		$text = preg_replace("/ rel( *)=( *)stylesheet/i"," rel=styleshit", $text );
		$text = preg_replace("/:( *)\-/",":- ", $text );

		$text  = CloseTags($text);

		return $text;
	}

    /* Main methods */
	
	function FormatMessageBody($message, $userUrlName, $isSingleMessage) {
      global $cutCount;

		$content = $message->Content;
		$cutPos = strpos($content, "##CUT##");
		if ($cutPos !== false) {
			if ($isSingleMessage) {
    			$cutCount = 1;
				$content = preg_replace("/##CUT##/e", "MakeCut()", $content);
				$content = str_replace("##CUTEND##", "<div class='cutend'><div></div></div>", $content);
			} else {
				while ($cutPos !== false) {
					$cut = substr($content, $cutPos, strlen($content));
					$content = substr($content, 0, $cutPos);
					$cutEndPos = strpos($cut, "##CUTEND##");
					if ($cutEndPos !== false) {
						$content .= substr($cut, $cutEndPos, strlen($cut));
					}
					$cutPos = strpos($content, "##CUT##");
				}
				$content = str_replace("##CUTEND##", "", $content);
			}
			$content = MakeCutLink($content);
		}

		// Link
		$content = ReplaceLinks($content, $message->Id, $userUrlName);

		return $content;
	}

	function FormatMessage($message, $template, $userUrlName, $isSingleMessage = false) {
		if (!$template) {
			return;
		}

		$result = $template;
        $result = str_replace("##TITLE##", $message->Title, $result);

		// Date & time
		$messageTime = strtotime($message->Date);
        $fullDate = date("j.m.Y", $messageTime);
		$day = date("j", $messageTime);
		$month = date("m", $messageTime);
		$year = date("Y", $messageTime);
		$messageTime = date("H:i", $messageTime);
		
		$result = str_replace("##DATE##", $fullDate, $result);
		$result = str_replace("##DAY##", $day, $result);
		$result = str_replace("##MONTH##", $month, $result);
		$result = str_replace("##YEAR##", $year, $result);
		$result = str_replace("##TIME##", $messageTime, $result);

		switch ($message->Type) {
			case 2:
				$kind = "private";
				break;
			case 1:
				$kind = "friends only";
				break;
		   	default:
		   		$kind = "public";
		}
		$result = str_replace("##KIND##", $kind, $result);

		// Comments
		$comments = "";
		$commentsCount = $message->AnswersCount - $message->DeletedCount;
		if ($message->IsCommentable) {
			$comments = JournalComment::MakeLink(
				$message->Id, 
				0, 
				Countable("êîììåíòàðèé", $commentsCount));
		}
		$result = str_replace(
			"##COMMENTS##", 
			$comments,
			$result);

		// Last comment date
		$lastCommentTime = "";
		if ($commentsCount) {
			$lastCommentTime = PrintableShortDate($message->UpdateDate);
		}
		$result = str_replace("##LASTCOMMENTDATE##", $lastCommentTime, $result);

		// Cut		
		$content = FormatMessageBody($message, $userUrlName, $isSingleMessage);
		$result = str_replace("##BODY##", nl2br($content), $result);
		
		// Link
		$result = ReplaceLinks($result, $message->Id, $userUrlName);
		
		return $result;
	}

	function MakeJournalPager($userUrlName, $totalRecords = 0, $showMessages = 0, $currentPage = 0, $forFriends = 0) {
	  global $year, $month, $day, $dateExists;

		$result = "<div class='Pager'>";
        if ($totalRecords > $showMessages && $showMessages > 0) {
        	$linkPrep = "";
        	if (($year && $month) && $dateExists !== false) {
        		$linkPrep = "/".sprintf("%04d", $year)."/".sprintf("%02d", $month);
        		if ($day) {
        			$linkPrep .= "/".sprintf("%02d", $day);
        		}
        	}

            $pages = ceil($totalRecords / $showMessages);
            for ($i = 0; $i < $pages; $i++) {
                $result .= ($i ? " | " : "").($currentPage == $i * $showMessages ? "<b>".($i+1)."</b>" : "<a href='/journal/".$userUrlName.($forFriends ? "/friends" : "").$linkPrep."/".($i ? "page".$i.".html" : "")."'>".($i+1)."</a>");
            }
        }
        $result .= "</div>";
		return $result;
	}

?>