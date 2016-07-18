<?php

    // Constants
    $messageChunk = "##MESSAGE##";

    
    /* Service methods */

    function ReplaceLinks($text, $messageId, $alias) {
        return str_replace(
            "##LINK##", 
            JournalSettings::MakeHref($alias, $messageId),
            $text);
    }

    function TagCutLink($caption) {
      global $cutCount;

        $caption = str_replace("\\\"", "&quot;", $caption);
        return "<span class='cutlink'>(<a href='##LINK###cut".$cutCount++."'>".($caption ? $caption : "Подробнее")."</a>)</span>";
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
    function TagStack($matches) {
      global $tagsStack;

        $order = $matches[1];
        $tag = $matches[2];
        $finish = $matches[3];

        $tag = strtolower($tag);
        
        if (!preg_match("/^(li|p|link|img|area)$/i", $tag)) {
            if (!array_key_exists($tag, $tagsStack)) {
                $tagsStack[$tag] = 0;
            }
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

    // Collect opened tags in stack
    function CloseTags($text) {
        return preg_replace_callback("/<(\/?)([a-z0-9]+)(>| )/i", "TagStack", $text);
    }

    // Closes all opened tags, collected in stack 
    function RenderClosingTags() {
      global $tagsStack;
        $result = "";
        while (list($k, $v) = each($tagsStack)) {
            if (round($v) > 0 && $k != "img" && $k != "br" && $k != "meta" && $k != "hr") {
                $result = str_repeat("</".$k.">", $v).$result;
            }
        }
        return $result;
    }

    function InjectionProtection($text) {
        return CloseTags($text);

        $text = preg_replace("/( |:)on([a-z ]+=)/i", "\\1оn\\2", $text);
        $text = preg_replace("/visibility/i","invisibility", $text);
        $text = preg_replace("/z-index/i","zzz-index", $text);
        $text = preg_replace("/noscript/i","yescript", $text);
        $text = preg_replace("/\<script/i","\<scrыpt", $text );
        $text = preg_replace("/<frame/i","<frаme", $text );
        $text = preg_replace("/display( *)[:=]/i","displаy:", $text );
        $text = preg_replace("/absolute/i","absоlute", $text );
        $text = preg_replace("/scroll( *)=( *)no/i","scroll=угу", $text );
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
      global $usedTags;

        if (!$template) {
            return;
        }

        $result = str_replace("##TITLE##", $message->Title, $template);

        // Date & time
        $msgTime = strtotime($message->Date);
        $fullDate = date("j.m.Y", $msgTime);
        $day = date("j", $msgTime);
        $month = date("m", $msgTime);
        $year = date("Y", $msgTime);
        $messageTime = date("H:i", $msgTime);

        $textualDate = date("c", $msgTime);
        
        $result = str_replace("##DATE##", "<time datetime=\"".$textualDate."\" pubdate>".$fullDate."</time>", $result);
        $result = str_replace("##DAY##", $day, $result);
        $result = str_replace("##MONTH##", $month, $result);
        $result = str_replace("##YEAR##", $year, $result);
        $result = str_replace("##TIME##", "<time datetime=\"".$textualDate."\" pubdate>".$messageTime."</time>", $result);
        $result = str_replace("##AUTHOR##", "<author>".$message->Author."</author>", $result);

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
                $userUrlName,
                0, 
                Countable("комментарий", $commentsCount, "нет"));
            $commentsN = JournalComment::MakeLink(
                $message->Id, 
                $userUrlName,
                0, 
                $commentsCount);
        }
        $result = str_replace("##COMMENTS##", $comments, $result);
        $result = str_replace("##COMMENTSN##", $commentsN, $result);

        // Last comment date
        $lastCommentTime = "";
        if ($commentsCount) {
            $lastCommentTime = PrintableShortDate($message->UpdateDate);
        }
        $result = str_replace("##LASTCOMMENTDATE##", $lastCommentTime, $result);

        // Cut      
        $content = FormatMessageBody($message, $userUrlName, $isSingleMessage);
        $result = str_replace("##BODY##", Smartnl2br($content), $result);
        
        // Tags List
        if (strpos($result, "##TAGS##") !== false) {
            $tag = new Tag();
            $q = $tag->GetByRecordId($message->Id);
            $tags = "";
            $labels = $q->NumRows();
            for ($i = 0; $i < $labels; $i++) {
                $q->NextResult();
                $tag->FillFromResult($q);
                $tags .= $tag->ToPrint($i, $userUrlName);
                $usedTags[$tag->Title] = true;
            }
            $result = str_replace("##TAGS##", $tags, $result);
        }

        // Link
        $result = ReplaceLinks($result, $message->Id, $userUrlName);
        
        return $result;
    }


    // Replaces first occurence of ##MESSAGE## in $bodyText
    // with rendered given message
    function DisplayRecord($message) {
      global $template, $settings, $bodyText, $messageChunk, $record_id, $usedTags;

        $messageText = FormatMessage($message, $template->Message, $settings->Alias, $record_id > 0);

        $position = strpos($bodyText, $messageChunk);
        if ($position === 0) {
            return false;
        }
        $bodyText = substr_replace($bodyText, $messageText, $position, strlen($messageChunk));
    }
    
    // Makes journal link
    function MakeJournalLink($alias, $text, $page = 0, $year = 0, $month = 0, $day = 0, $tag = "", $forFriends = 0) {

        $criteria = "";
        if ($tag) {
            $criteria = "/tag/".urlencode($tag);
        } else {
            // Dates
            if ($year > 1990) {
                $criteria = sprintf("/%04d", $year);
                if ($month > 0 && $month <= 12) {
                    $criteria .= sprintf("/%02d", $month);
                    if ($day > 0 && $day <= 31) {
                        $criteria .= sprintf("/%02d", $day);
                    }
                }
            }
        }
        if ($page > 0) {
            // Paging
            $criteria .= "/page".round($page).".html";
        }
        return "<a href='/journal/".$alias.($forFriends ? "/friends" : "").$criteria."'>".$text."</a>";
    }

    // Makes pager links
    function MakeJournalPager($alias, $totalRecords = 0, $showMessages = 0, $currentPage = 0, $forFriends = 0) {
      global $year, $month, $day, $tag, $datesCondition;

        $result = "<div class='Pager'>";
        if ($totalRecords > $showMessages && $showMessages > 0) {
            $pages = ceil($totalRecords / $showMessages);
            for ($i = 0; $i < $pages; $i++) {
                $result .= ($i ? " | " : "").
                    ($currentPage == $i * $showMessages ? 
                    "<b>".($i+1)."</b>" : 
                    ($datesCondition ? 
                        MakeJournalLink($alias, $i+1, $i, $year, $month, $day, "", $forFriends) :
                        MakeJournalLink($alias, $i+1, $i, 0, 0, 0, $tag, $forFriends))
                        );
            }
        }
        $result .= "</div>";
        return $result;
    }

    // Makes prev + next month calendar links
    function MakeMonthLink($alias, $year, $month) {
      global $MonthsNames;
        if ($month < 1) {
            $month = 12;
            $year--;
        } elseif ($month > 12) {
            $month = 1;
            $year++;
        }
        if (strtotime("$year-$month-01 12:59:57") <= time()) {
            return MakeJournalLink($alias, sprintf("%s", $MonthsNames[$month], $year), 0, $year, $month, 0);
        } else {
            return $MonthsNames[$month];
        }
    }

    // Renders all occurences of #POST\d+ to links to the posts
    function RenderPostsLinks($bodyText) {
        preg_match_all("/#POST(\d+)/i", $bodyText, $matches, PREG_SET_ORDER);

        $ids = array();
        foreach ($matches as $link) {
            $ids[] = $link[1];
        }
        if (!sizeof($ids)) {
            return $bodyText;
        }
        $record = new JournalRecord();
        $q = $record->GetJournalsTopicsByIds($ids);
        $n = $q->NumRows();
        for ($i = 0; $i < $n; $i++) {
            $q->NextResult();
            $record->FillFromResult($q);
            $alias = $q->Get(JournalSettings::ALIAS);
            $bodyText = str_replace("#POST".$record->Id, $record->ToHref($alias), $bodyText);
        }
        return $bodyText;
    }

?>
