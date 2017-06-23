<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>

<html>

    <head>

        <meta charset="utf-8" />
        <title>TECKMEB - Page d'acceuil</title>
        <link href='<?php echo css_url('welcome_page')?>' rel='stylesheet' type='text/css'>
		
    </head>

    <body>

        <main>

            <section id = "blocGauche">

                <!-- Bloc de rapide présentation des ptuts, + lien -->

            </section>

            <section id = "blocDroit">

                <?php echo html_img('teckmeb_logo.png', 'TECKMEB', 'logo'); ?>

                <form action = "/" method = "post">

                    <div>

                        <?php echo html_img('id.png', "id"); ?>
                        <input type="text" name="id" placeholder="Identifiant"/>

                    </div>
                    <div>

                        <?php echo html_img('mdp.png', "pass"); ?>
                        <input type="password" name="password" placeholder="Mot de passe"/>

                    </div>
                    <div>

                        <input id="stayConnected" type="checkbox" name="stayConnected"/>
                        <label for="stayConnected">Rester connecté</label>

                    </div>

                    <input id="connect" type="submit" value="Se Connecter" />

                </form>

            </section>

        </main>

    </body>

</html>
