<?php 

    $root = "../";
    require_once $root."server_references.php";
    require_once "forum.template.php";

    $forum_id = round($_GET[Forum::ID_PARAM]);
    $from = round($_GET["from"]);

    $lastModified = "";

    $forum = new Forum($forum_id);
    $forum->Retrieve();

    if ($forum->IsEmpty()) {
        DieWith404();
    }

    $meta_description = "Форум '".MetaContent($forum->Title)."': ".MetaContent($forum->Description);

    $yesterday = DateFromTime(time() - 60*60*24);   // Yesterday

    $p = new Page($forum->Title, $meta_description, "Форумы");
    $p->AddCss(array("forum.css", "jqueryui.css"));

    require_once $root."references.php";

    $access = 1 - $forum->IsProtected;
    if ($someoneIsLogged) {
        $access = $forum->GetAccess($user->User->Id);
    }

    if ($access == Forum::NO_ACCESS || $forum->IsHidden) {
        header("HTTP/1.0 403 Forbidden");
        include("../403.html");
        die;
    }
    
    $p->PrintHeader();
    $result.= $forum->ToPrint("forum.php");
    $threadsPerPage = 20;

    $record = new ForumRecord();
    $q = $record->GetForumThreads(
        $forum->Id,
        $access,
        $from * $threadsPerPage, 
        $threadsPerPage);
    
    $result.= "<style>#buttonCite".($forum->IsProtected ? "" : ", #IsProtected")." {display:none;}</style>";
    $result.= "<div class='NewThread'><div>";
    $result.= "<a href='javascript:void(0)' class='replyLink' onclick='ForumReply(this,0,".$forum->Id.")'>Создать новую тему</a>";
    $result.= "</div></div>";
    
    $result.= "<ul class='random threads'>";

    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();

        $record->FillFromResult($q);
        $result.= "\n".$record->ToPrint($forum, 0, $yesterday);
        if ($record->UpdateDate > $lastModified) {
            $lastModified = $record->UpdateDate;
        }
    }
    $result.= "</ul>";
    $q->Release();

    $threads = $record->GetForumThreadsCount($forum_id, $access);
    $pager = new Pager($threads, $threadsPerPage, $from, $forum->BasePath());
    $result.= $pager;


    // Printing
//  AddEtagHeader(strtotime($lastModified));

    echo $result;
    include $root."inc/ui_parts/post_form.php";

    $p->PrintFooter();
?>