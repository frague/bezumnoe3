<?php
    $root = "../../../";
    require $root."server_references.php";

    $img = preg_replace("/[^0-9a-zA-Z_\.]/", "", LookInRequest("img"));
    if (!$img) {
        DieWith404();
    }

    $maxWidth = 100;
    $maxHeight = 200;


    // Image resizing
    $image = new SimpleImage();
    $image->Load("../".$img);

    if ($image->GetWidth() > $maxWidth) {
        $image->ResizeToWidth($maxWidth);
    }
    if ($image->GetHeight() > $maxHeight) {
        $image->ResizeToHeight($maxHeight);
    }

    header("Content-Type: image/jpeg");
    AddLastModified(filemtime("../".$img));

    $image->Output();

?>
