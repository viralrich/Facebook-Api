<?php
session_start();

//require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/includes_sistema/BaseClass.php");
//require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/includes_sistema/HELPER.php");
require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/FacebookAPI/FacebookClass.php");
//
$FacebookClass = new FacebookClass();
//
//#Cuenta Data
$me = $FacebookClass->cURL_Facebook('/me?fields=id,name,picture,email,verified,permissions&access_token=', $_GET['access_token']);

$link = 'https://www.facebook.com/BadabunOficial/videos/371381828062265/';


    echo '<center>';
    echo '<img src="'.$me->picture->data->url.'""> ';
    echo '<h2>Hello <u>'.$me->name .'</u> this is your groups list:</h2>';
    echo '<br><b>Permissions:</b>';
    echo '<br>';
    foreach ($me->permissions->data  as $key => $permission) {
		echo '<b>'. $permission->permission .'</b> -> '. $permission->status.'<br>';
		}
    echo '<br><b>Link to share:</b> '.$link;

    echo '</center>';



$groups = $FacebookClass->cURL_Facebook('/me/groups?fields=id,name,picture,member_count,permissions&access_token=', $_GET['access_token']);
$members_group = $FacebookClass->cURL_Facebook('/344280469696853_1508897122814623?fields=sharedposts,reactions.limit(0).summary(total_count),name,message,full_picture,created_time,shares,comments.limit(1).summary(true),insights&access_token=', $_GET['access_token']);

echo '<img src="'.$members_group->full_picture.'" width="300px"> <br>';

echo '<b>Likes</b>: '.$members_group->reactions->summary->total_count . ' <b style="color:green">($'.number_format(($members_group->reactions->summary->total_count * 0.10),3).')</b><br>';
echo '<b>Shares:</b> '.$members_group->shares->count . ' <b style="color:green">($'.number_format(($members_group->shares->count * 0.35),3).')</b><br>';
echo '<b>Comments:</b> '.$members_group->comments->summary->total_count . ' <b style="color:green">($'.number_format(($members_group->comments->summary->total_count * 0.05),3).')</b><br>';


exit;


echo '<br>';
echo '<br>';
echo '<br>';
echo '<h2>Pages that you ADMIN/OWNER by can post:</h2>';
echo '<br>';



foreach ($groups->data as $key => $group) {
    if(isset($group->member_count)){
    echo '<img src="'.$group->picture->data->url.'""> ';
    echo '<br>';
    echo '<b>-ID</b> <a href="https://www.facebook.com/groups/'. $group->id.'" target="_blank">'. $group->id.'</a>';
    echo '<br>';
    echo '<b>-Group</b> '. $group->name;
    echo '<br>';
    echo '<b>-Group Members:</b> '. (isset($group->member_count) ? '<b>'.$group->member_count.'</b>' : '<small>*IS NOT ADMIN*</small>');
    echo '<br>';
//    echo '<b>-Permissions:</b> '; print_r($group->permissions);


       $check_link = $FacebookClass->cURL_Facebook('/'.$group->id.'/feed?fields=message,link&access_token=', $_GET['access_token']);

       foreach($check_link->data as $check){


           similar_text($link, $check->link, $percent);

            if($percent > 98){
                echo '<h3 style="color:green">VIDEO PUBLISHED HERE</h3>';
            }else{
                echo '<h3 style="color:orange">VIDEO IS NOT PUBLISHED</h3>';
            }

       }

        echo '<br>';
        echo '<br>';
        echo '<br>';
    }



}



echo '<br>';
echo '<br>';
echo '<br>';
echo '<h2>Pages that you arent admin by can post:</h2>';
echo '<br>';




foreach ($groups->data as $key => $group) {
    if(!isset($group->member_count)){
    echo '<img src="'.$group->picture->data->url.'""> ';
    echo '<br>';
    echo '<b>-ID</b> <a href="https://www.facebook.com/groups/'. $group->id.'" target="_blank">'. $group->id.'</a>';
    echo '<br>';
    echo '<b>-Group</b> '. $group->name;
    echo '<br>';
    echo '<b>-Group Members:</b> '. (isset($group->member_count) ? '<b>'.$group->member_count.'</b>' : '<small>*IS NOT ADMIN*</small>');
    echo '<br>';


        echo '<br>';
        echo '<br>';
        echo '<br>';
    }



}























return;

 #Verificar token
if($me->id){





	if(!$FacebookClass->verificarPermisos($me->permissions->data)){
		header("Location: ../Tablero?Error_Msg=Permisos_Incorrectos");
	}else{
		#Paginas
		$pages = $FacebookClass->cURL_Facebook('me/accounts?fields=id,name,picture,category,access_token,fan_count,can_post&access_token=', $_GET['access_token']);



		#Foreach de cada pagina
		foreach ($pages->data as $value) {

			$data =  array(
			    'Facebook_Cuenta_ID' => $Facebook_Cuenta_ID,
			    'Fanpage_ID' => $value->id,
			    'Fanpage_Nombre' => $value->name,
			    'Fanpage_Foto' => $value->picture->data->url,
			    'Fanpage_Token' => $value->access_token,
			    'Fanpage_Fans' => $value->fan_count
			);

//print_r($data);print_r($value->perms);

			#Verifica si tiene permisos de admin o creador de contenido - perms, - $has_permission
			$has_permission = 0;
			foreach ($value->perms as $perm) {
				if($perm == "ADMINISTER" || $perm == "CREATE_CONTENT" || $perm == "BASIC_ADMIN" || $perm == "MODERATE_CONTENT" || $perm == "EDIT_PROFILE"){
					$has_permission = 1;
				}
			}


			#Verificador de insercion y actualiacion de Fanpages
			if(/*$value->can_post && */empty($FacebookClass->verificarPaginaFacebookExiste($data)) && $value->fan_count > 10000){
			 	$FacebookClass->insertarPaginaFacebook($data);
			 	// if($_GET['uri']){
			 	// 	header("Location: ".HELPER::decrypt($_GET['uri']));
			 	// }else{
			 	// 	header("Location: ../Lista_Paginas-".HELPER::encrypt($Facebook_Cuenta_ID));
			 	// }
			}elseif(/*$value->can_post && */!empty($FacebookClass->verificarPaginaFacebookExiste($data))){
				$FacebookClass->actualizarPaginaFacebook($data);

				#Actualizar las publicaciones sin permisos a monetizadas
				if ($i == 0) {

					$Facebook_Enlace_Publicado_ID = $FacebookClass->getFacebook_Enlaces_Publicados_Sin_Permisos($data['Facebook_Cuenta_ID']);


    				foreach($Facebook_Enlace_Publicado_ID AS $Facebook_Enlace_Publicado_ID_Sin_Permisos){
    					$FacebookClass->updateFacebook_Enlaces_Publicados_Sin_Permisos($Facebook_Enlace_Publicado_ID_Sin_Permisos['Facebook_Enlace_Publicado_ID']);
    				}
    			}


			}else{
				#Sino existen fanpages, redirige al index
				header("Location: ../Lista_Paginas-".HELPER::encrypt($Facebook_Cuenta_ID));
			}




		#Suma al count del foreach
		$i++;
		}

		#Redireccional despues de procesar todo
		if($_GET['uri']){
	 		header("Location: ".HELPER::decrypt($_GET['uri']));
	 	}else{
			header("Location: ../Lista_Paginas-".HELPER::encrypt($Facebook_Cuenta_ID));
		}


	}

}else{
	#Sino existe el token redirige al index
	header("Location: ../Tablero?Error_Msg=Permisos_Incorrectos");
}


?>