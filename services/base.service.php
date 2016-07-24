<?php
    header("Content-type: text/html");

    $root = "../";
    require_once $root."server_references.php";

    function AddJsAlert($message, $isError = 0) {
        return "tabObject.Alerts.Add(\"".JsQuote($message)."\", ".$isError.");";
    }

    function JsAlert($message, $isError = 0) {
        return "obj.Tab.Alerts.Add(\"".JsQuote($message)."\", ".$isError.");";
    }

    function AddTypeCondition($name, $key, $value, $tail, $condition="OR") {
        if (LookInRequest($key)) {
            return ($tail ? $tail." $condition " : "")."t1.".$name."='".$value."'";
        }
        return $tail;
    }



    $go = LookInRequest("go");
    $user_id = round(LookInRequest("USER_ID"));
    $forum_id = round(LookInRequest("FORUM_ID"));
    $owner_id = round(LookInRequest("OWNER_ID"));
    $state = round(LookInRequest("state"));
    $id = round(LookInRequest("id"));
    $from = round(LookInRequest("from"));
    $amount = round(LookInRequest("amount"));
    if (!$amount) {
        $amount = 20;
    }
?>