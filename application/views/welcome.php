<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8"/>
	<title>TECKMEB - Page d'acceuil</title>
	<link rel="stylesheet" type="text/css" href="<?php echo css_url("welcome_page") ?>"/>
	
</head>
<body>
	<main>
	
		<section id="rectangleGauche">


		
		</section>
		<section id="rectangleDroit">
		
			<img id="logo" src="<?php echo img_url("teckmeb_logo.png") ?>">
			
			<form action="index.php" method="post">

                <div>
                    <label for="id"><?php echo html_img("id.png", "Identifiant"); ?></label>
                    <input id="id" type="text" name="id" />
                </div>
                <div>
                    <label for="password"><?php echo html_img("mdp.png", "Mot de passe"); ?></label>
                    <input id="password" type="password" name="password" />
                </div>
                <div>
                    <input id="stay_connected" type="checkbox" name="stay_connected">
                    <label for="stay_connected">Rester connecté</label>
                </div>

				<input type="submit" value="Se connecter" />
				
			</form>
			
		</section>
		
	</main>
</body>
</html>
