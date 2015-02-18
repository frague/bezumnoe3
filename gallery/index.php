<?php
    
    $root = "../";
    require_once $root."server_references.php";
    require_once "gallery.template.php";

    $meta_description = "Фото галерея саратовского чата Безумное Чаепитие у Мартовского Зайца. Фотографии самых интересных событий чата. Фотки чатлан. Интересные события Саратова.";

    $p = new Page("Фотогалерея");
    $p->AddCss("forum.css");
    $p->PrintHeader();

    require_once $root."references.php";

?>
<style>
    h1 .char2 {
        margin-left: -1px;
    }
    h1 .char3 {
        margin-left: -1px;
    }
    h1 .char4 {
        margin-left: -1px;
    }
    h1 .char6 {
        margin-left: -2px;
    }
    h1 .char7 {
        margin-left: 1px;
    }
    h1 .char8 {
        margin-left: 1px;
    }
    h1 .char9 {
        margin-left: -1px;
    }
</style>

<table class="columns">
    <tr>
        <td class="preview">
            <h2>Новые комментарии</h2>
            <?php include $root."/inc/ui_parts/gallery.comments.php" ?>
        </td>
        <td>
            <h2>События и коллекции фотографий</h2>
            <?php include $root."/inc/ui_parts/galleries.php" ?>
        </td>
    </tr>
</table>
<?php

    $p->PrintFooter();
?>