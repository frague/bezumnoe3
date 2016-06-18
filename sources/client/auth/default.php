<?php

    $root = "../";
    require_once $root."server_references.php";
    require_once $root."inc/classes/openid.class.php";

    $result = "";
    $referer = $_POST["callback"];
    $referer = $referer ? $referer : ($_POST[REFERER_KEY] ? $_POST[REFERER_KEY] : $_SERVER["HTTP_REFERER"]);


    switch ($_POST["AUTH"]) {
        case "1":
            $user = GetAuthorizedUser(true);
            if (!$user->IsEmpty()) {
                SetUserSessionCookie($user->User);
            }

            header("Cache-Control: no-cache, must-revalidate");
            header("Pragma: no-cache");
            header("Location: ".$referer);
            break;

        case "2":
        default:
            $openid_login = $_POST[OPENID_LOGIN_KEY];
            $provider_id = round($_POST[OPENID_KEY]);

            if ($_POST["openid_action"] == "login") { // Get identity from user and redirect browser to OpenID Server

                if (!$openid_login || !$provider_id)  {
                    echo $openid_login;
                    echo $provider_id;
                    exit;
                }

                $op = new OpenIdProvider();
                $op->GetById($provider_id);
                if ($op->IsEmpty()) {
                    exit;
                }

                $openid_url = $op->MakeUrl($openid_login);

                $openid = new SimpleOpenID;
                $openid->SetIdentity($openid_url);
                $openid->SetTrustRoot("http://" . $_SERVER["HTTP_HOST"]);

// Info to be requested
//              $openid->SetRequiredFields(array("email","fullname"));
//              $openid->SetOptionalFields(array("dob","gender","postcode","country","language","timezone"));

                if ($openid->GetOpenIDServer()) {
                    $openid->SetApprovedURL("http://".$_SERVER["HTTP_HOST"]."/auth/?back=".urlencode($referer));      // Send Response from OpenID server to this script
                    $openid->Redirect();     // This will redirect user to OpenID Server
                    exit;
                } else {
                    $error = $openid->GetError();
                    $result = "ERROR CODE: ".$error["code"]."<br>";
                    $result .= "ERROR DESCRIPTION: ".$error["description"]."<br>";

                    header("Cache-Control: no-cache, must-revalidate");
                    header("Pragma: no-cache");

                    $back = preg_replace("/#.+$/", "", $referer);
                    header("Location: ".$back."#error");
                    exit;
                }
            } else if ($_GET["openid_mode"] == "id_res") {     // Perform HTTP Request to OpenID server to validate key

                $openid = new SimpleOpenID;
                $openid->SetIdentity($_GET["openid_identity"]);
                $openid_validation_result = $openid->ValidateWithServer();

                if ($openid_validation_result == true) {         // OK HERE KEY IS VALID
                    $result = "Confirmed";

                    $userId = GetUserByOpenId($_GET["openid_identity"]);
                    if ($userId) {
                        $user = new UserComplete($userId);
                        $user->Retrieve();

                        $user->User->CreateSession();
                        $user->User->Save();
                        SetUserSessionCookie($user->User, true);
                    }

                } else if ($openid->IsError() == true) {            // ON THE WAY, WE GOT SOME ERROR
                    $error = $openid->GetError();
                    $result = "ERROR CODE: ".$error["code"]."<br>";
                    $result .= "ERROR DESCRIPTION: ".$error["description"]."<br>";
                } else {                                            // Signature Verification Failed
                    $result = "Invalid Authorization";
                }
            } else if ($_GET["openid_mode"] == "cancel") { // User Canceled your Request
                $result = "User canceled request";
            }

            // TODO: Pass errors to back page

            $back = $_GET["back"];
            header("Location: ".$back);
            break;
    }
?>
