<?php

    $root = "../";
    require_once $root."server_references.php";
    require_once "journal.template.php";

    $id = round(LookInRequest("id"));

/*  $settings = new JournalSettings();
    if ($id) {
        $settings->GetByForumId($id);
    }

    if ($settings->IsEmpty()) {
        DieWith404();
    }*/

    $template = new JournalTemplate($id);
    $template->Retrieve();
    if ($template->IsEmpty()) {
        DieWith404();
    }

    header("Content-type: text/css");
    AddEtagHeader(strtotime($template->Updated));

    // Pre-processing (?)
    echo $template->Css;

?>