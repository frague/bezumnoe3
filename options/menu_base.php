<?

    header("Content-type: text/html;charset=utf-8");

    $root = "../";
    require_once $root."server_references.php";

    $user = GetAuthorizedUser(true);

?>