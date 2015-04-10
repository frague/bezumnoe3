<?php

    $root = "../";
    require_once $root."server_references.php";
    require_once $root."references.php";

    $user = GetAuthorizedUser(true);
    if (!$user || $user->IsEmpty() || !$user->IsAdmin()) {
        die("User is not authorized");
    }

    $id = round(LookInRequest("jid"));
    if (!$id) {
        die("No id is specified!");
    }

    $j = new Journal($id);
    $j->Retrieve();

    if ($j->IsEmpty() || !$j->IsJournal()) {
        die("Error fetching journal data!");
    }

    $settings = new JournalSettings();
    $settings->GetByForumId($j->Id);

    $settings->OwnMarkupAllowed = 1 - $settings->OwnMarkupAllowed;
    $settings->Save();

    echo "Own markup for journal \"".$j->Title."\" <b>".($settings->OwnMarkupAllowed ? "Allowed" : "Disabled")."</b>";

?>
