<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8" />
        <title>TECKMEB - Page d'acceuil</title>
		<link href='style.css' rel='stylesheet' type='text/css'>
		
    </head>


    <body>
		
		<div id = "conteneur">
		
			<div id = "rectangleGauche">
		

			
			</div>
		
			<div id = "rectangleDroit">
			
				<img id = "logo" src = "images/LogoTECKMEB.png">
				
				<form id = "formulaire" action = "index.php" method = "post">
					
					<img class = "icone" src = "images/Id.png"> 
					<input class = "texte" type="text" name="identifiant" />
	
					<div id = "password">
					
						<img class = "icone" src = "images/Mdp.png"> 
						<input class = "texte" type="password" name="mdp" />
						
					</div>
					
					<input id = "seConnecter" type="submit" value="Se Connecter" />
		
				</form>
			
			</div>
			
		</div>
		
    </body>

</html>