<?php

	$root = "./";
	require_once $root."server_references.php";
	require_once $root."inc/classes/openid.class.php";

	$login = $_POST[LOGIN_KEY];
	$provider_id = round($_POST[OPENID_KEY]);
    $result = "";

	if ($_POST['openid_action'] == "login") { // Get identity from user and redirect browser to OpenID Server

		if (!$login || !$provider_id)  {
			exit;
		}	

		$op = new OpenIdProvider();
		$op->GetById($provider_id);
		if ($op->IsEmpty()) {
			exit;
		}

		$referer = $_POST[REFERER_KEY] ? $_POST[REFERER_KEY] : $_SERVER["HTTP_REFERER"];
    	$openid_url = $op->MakeUrl($login);
    
	    $openid = new SimpleOpenID;
	    $openid->SetIdentity($openid_url);
	    $openid->SetTrustRoot('http://' . $_SERVER["HTTP_HOST"]);
	    $openid->SetRequiredFields(array('email','fullname'));
	    $openid->SetOptionalFields(array('dob','gender','postcode','country','language','timezone'));
	    if ($openid->GetOpenIDServer()) {
	        $openid->SetApprovedURL('http://'.$_SERVER["HTTP_HOST"]."/auth.php?back=".urlencode($referer));      // Send Response from OpenID server to this script
	        $openid->Redirect();     // This will redirect user to OpenID Server
	    } else {
	        $error = $openid->GetError();
	        $result = "ERROR CODE: ".$error['code']."<br>";
	        $result .= "ERROR DESCRIPTION: ".$error['description']."<br>";

			header('Location: '.$referer);
			exit;
	    }
	} else if ($_GET['openid_mode'] == 'id_res') {     // Perform HTTP Request to OpenID server to validate key

	    $openid = new SimpleOpenID;
	    $openid->SetIdentity($_GET['openid_identity']);
	    $openid_validation_result = $openid->ValidateWithServer();

	    if ($openid_validation_result == true) {         // OK HERE KEY IS VALID
	        $result = "Confirmed";
	    } else if ($openid->IsError() == true) {            // ON THE WAY, WE GOT SOME ERROR
	        $error = $openid->GetError();
	        $result = "ERROR CODE: ".$error['code']."<br>";
	        $result .= "ERROR DESCRIPTION: ".$error['description']."<br>";
	    } else {                                            // Signature Verification Failed
	        $result = "Invalid Authorization";
	    }
	} else if ($_GET['openid_mode'] == 'cancel') { // User Canceled your Request
	    $result = "User canceled request";
	} 

	header('Location: '.$_GET["back"]);

?>