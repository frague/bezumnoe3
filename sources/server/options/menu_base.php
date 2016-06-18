<?

    header("Content-type: text/html");

    $root = "../";
    require_once $root."server_references.php";

    $user = GetAuthorizedUser(true);

?>
