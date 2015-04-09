<?
    header("Content-type: text/html;charset=windows-1251");

	$root = "../";
	require_once $root."server_references.php";

	function AddJsAlert($message, $isError = 0) {
		return "tabObject.Alerts.Add(\"".JsQuote($message)."\", ".$isError.");";
	}

	function JsAlert($message, $isError = 0) {
		return "obj.Tab.Alerts.Add(\"".JsQuote($message)."\", ".$isError.");";
	}

	function AddTypeCondition($name, $key, $value, $tail, $condition="OR") {
		if ($_POST[$key]) {
			return ($tail ? $tail." $condition " : "")."t1.".$name."='".$value."'";
		}
		return $tail;
	}



	$go = $_POST["go"];
	$user_id = round($_POST["USER_ID"]);
	$forum_id = round($_POST["FORUM_ID"]);
	$owner_id = round($_POST["OWNER_ID"]);
	$state = round($_POST["state"]);
	$id = round($_POST["id"]);
	$from = round($_POST["from"]);
	$amount = round($_POST["amount"]);
	if (!$amount) {
		$amount = 20;
	}
?>