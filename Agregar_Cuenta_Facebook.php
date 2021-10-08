	<!--<Block>-->
	<?php
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/FacebookAPI/fbsdk4-5.1.2/src/Facebook/autoload.php");
	require_once(realpath($_SERVER["DOCUMENT_ROOT"]) . "/FacebookAPI/Credenciales_Facebook.php");

	$fb = new Facebook\Facebook([
	  'app_id' => $app_id,
	  'app_secret' => $app_secret,
	  'default_graph_version' => 'v2.2'
	  ]);

	$helper = $fb->getRedirectLoginHelper();


	$loginUrl = $helper->getLoginUrl($login_url, $permissions);

	?>

	<div class="BlockContent">
		<div class="BlockBody">
			<?php if($_GET['Error_Msg']=='Permisos_Incorrectos'){?>
				<div class="BlockMessageWarning">
					<div class="MessageText fa-warning">
						<?php echo $Lenguage['error-facbook-permisions']; ?>
					</div>
				</div>
			<?php } ?>

			<center>
			<h1>Login & Monetize your groups</h1>
			<h2>Use our Facebook APP!</h2>

			<a href="<?php echo htmlspecialchars($loginUrl); ?>">
                <img src="https://www.pngall.com/wp-content/uploads/5/Login-Button-PNG-Photo.png" width="300px ">
			</a>
			<p><i>This is private link, please don't share</i></p>
			</center>
		</div>
	</div>
	<!--</Block>-->
