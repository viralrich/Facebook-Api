<?
session_start();

require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/includes_sistema/BaseClass.php");
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/includes_sistema/HELPER.php");
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/FacebookAPI/FacebookClass.php");

$FacebookClass = new FacebookClass(0);




$Facebook_Paginas_Activas = $FacebookClass->getFacebook_Paginas_Activas();

foreach ($Facebook_Paginas_Activas AS $Pagina) {
	
$me = $FacebookClass->cURL_Facebook('/me?fields=is_verified&access_token=', $Pagina['Fanpage_Token']);

if($me->is_verified){
	$me->id.'<br>';
}


}

#Cuenta Data
//$me = $FacebookClass->cURL_Facebook('/me?fields=id,name,picture,email,verified,permissions&access_token=', $_GET['access_token']);