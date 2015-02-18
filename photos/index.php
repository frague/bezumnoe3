<?php
    
    $root = "../";
    require_once $root."server_references.php";
    require $root."inc/ui_parts/templates.php";

    $pg = new Page("����� ������");
    $pg->AddCss(array("photos.css"));
    $pg->AddJs("gallery.js");
    $pg->PrintHeader();

    $search = LookInRequest("search");

    $u = new User();
    $p = new Profile();
    $q = $p->GetPhotos($search);

    $results = $q->NumRows();

    $result = "";
    $photos = "";

    for ($i = 0; $i < $results; $i++) {
        $q->NextResult();
        $p->FillFromResult($q);
        $login = $q->Get(User::LOGIN);

        $link = User::InfoLink($p->UserId, $login);

        $photo = ProfilePhoto($p, $login);
        if ($photo) {
            $photos .= $photo."<div>".$link."</div>\n";
            $link = "<b>".$link."</b>";
        }
        $result .= "<li> ".$link;
    }

?>
<table class='users_list'>
    <tr>
        <td>
            <form action="/photos/">
                <h2>�����</h2>
                <input name="search" id="search" value="<?php echo $search; ?>" maxlength="20" />
                <input type="image" src="/img/search_button_inv.gif" border="0" align="absmiddle" />

                <h2>�� ������� �������</h2>
                <?php
            
                $symbols = "�����Ũ��������������������������ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

                for ($i = 0; $i < strlen($symbols); $i++) {
                    $char = substr($symbols, $i, 1);
                    echo "<a href='".rawurlencode($char)."'>".$char."</a> ";
                }

                ?>

                <h2>����������</h2>
                <ul class="photo_matches random">
                    <?php echo $result ?>
                </ul>
                <?php 
                if ($results > 20) {
                    echo "<div class='Error'>���������� ������ 20</div>";
                } 
                ?>
            </form>
        </td>
        <td valign="top" class="Centered">
            <div class="Divider Vertical">
                <?php echo $photos; ?>
            </div>
        </td>
    </tr>
</table>
    <?php


    $pg->PrintFooter();
?>