<?php
/**
* Facebook Access
* Author: evilnapsis
**/

session_start();
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/FacebookAPI/fbsdk4-5.1.2/src/Facebook/autoload.php");
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .  "/FacebookAPI/Credenciales_Facebook.php");

$fb = new Facebook\Facebook([
  'app_id' => $app_id,
  'app_secret' => $app_secret,
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();
$_SESSION['FBRLH_state']=$_GET['state'];

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
//  header("Location: Tablero");
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
//  header("Location: Tablero");
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
  if ($helper->getError()) {
//    header("Location: Tablero");
     header('HTTP/1.0 401 Unauthorized');
     echo "Error: " . $helper->getError() . "\n";
     echo "Error Code: " . $helper->getErrorCode() . "\n";
     echo "Error Reason: " . $helper->getErrorReason() . "\n";
     echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {
    header("Location: Tablero");
     header('HTTP/1.0 400 Bad Request');
     echo 'Bad request';
  }
  exit;
}

$oAuth2Client = $fb->getOAuth2Client();
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
$tokenMetadata->validateAppId($app_id);
$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
//    header("Location: Tablero");
    echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
    exit;
  }
}

header("Location: Token_Verificado.php?access_token=".$accessToken."&app_id=".$app_id."&uri=".$_GET['uri']);
?>