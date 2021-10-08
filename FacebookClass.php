<?php
////require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/includes_sistema/BaseClass.php");
//
class FacebookClass{
    public function cURL_Facebook($Parametros, $Token){
        $ch = curl_init('https://graph.facebook.com/v12.0/'.$Parametros.$Token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);

    }

    public function cURL_Facebook_post($Parametros, $link = null){
        $post = [
            'message' => 'Hello Group'
//            'link' => $link
        ];

        $ch = curl_init('https://graph.facebook.com/v12.0/'.$Parametros);
//        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response);

    }
}
?>