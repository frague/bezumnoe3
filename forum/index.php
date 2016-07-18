<?php
    
    $root = "../";
    require_once $root."server_references.php";
    require_once "forum.template.php";

    $meta_description = "Форум саратовского чата Безумное Чаепитие у Мартовского Зайца. Самые интересные темы, вопросы, объявления.";

    $p = new Page("Форумы", $meta_description);
    $p->AddCss("forum.css");
    $p->PrintHeader();

    require_once $root."references.php";

    $yesterday = DateFromTime(time() - 60*60*24);   // Yesterday

    $forum = new Forum();
    if ($someoneIsLogged) {
        $q = $forum->GetByConditionWithUserAccess("1=1", $user->User->Id);
    } else {
        $q = $forum->GetByCondition("1=1");
    }
    echo "<ul class='forums'>";

    $forumUser = new ForumUser();

    for ($i = 0; $i < $q->NumRows(); $i++) {
        $q->NextResult();
        $forum->FillFromResult($q);

        $access = 1 - $forum->IsProtected;
        if ($someoneIsLogged) {
            $forumUser->FillFromResult($q);
            $access = $forum->LoggedUsersAccess($forumUser, $user->User->Id);
        }

        if ($access > Forum::NO_ACCESS && !$forum->IsHidden) {
            echo "<li>";
            $forum->DoPrint("/forum", $yesterday);
        }
    }
    echo "</ul>";
    $q->Release();

    $p->PrintFooter();
?>