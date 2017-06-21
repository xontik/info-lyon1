<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8" />
        <title>TECKMEB - Page d'acceuil</title>
		<link href='../../assets/css/welcome_pageTEST.css' rel='stylesheet' type='text/css'>
		
    </head>


    <body>
		
		<div id = "conteneur">
			
			<div id = "blocGauche">
		

			
			</div>
		
			<div id = "blocDroit">
			
				<img id = "logo" src = "../../assets/images/teckmeb_logo.png">
				
				<form action = "index.php" method = "post" style = "margin-top: 7%">
					
					<img class = "icone" src = "../../assets/images/id.png"> 
					<input class = "texte" type="text" name="identifiant" placeholder="Identifiant"/>
	
					<div style = "margin-top: 3em">
					
						<img class = "icone" src = "../../assets/images/mdp.png"> 
						<input class = "texte" type="password" name="mdp" placeholder="Mot de passe"/>
						
					</div>

					<input id = "seConnecter" type="submit" value="Se Connecter" />
		
				</form>
			
			</div>
			
		</div>
		
    </body>

</html>
