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
            <section id="blocGauche">

                <!-- Bloc de rapide présentation des ptuts, + lien -->

            </section>
            <section id="blocDroit">
                <?php echo html_img('teckmeb_logo.png', 'TECKMEB', 'logo'); ?>
                <form action="/user/connect" method="post">
                    <div>
                        <?php echo html_img('id.png', "id"); ?>
                        <input type="text" name="id" placeholder="Identifiant"/>
                        <?php if ( isset($_SESSION['form_errors']['id']) )
                            echo '<p class="form_error">' . $_SESSION['form_errors']['id'] . '</p>'; ?>
                    </div>
                    <div>
                        <?php echo html_img('mdp.png', "pass"); ?>
                        <input type="password" name="password" placeholder="Mot de passe"/>
                        <?php if ( isset($_SESSION['form_errors']['password']) )
                            echo '<p class="form_error">' . $_SESSION['form_errors']['password'] . '</p>'; ?>
                    </div>
                    <div>
                        <input type="checkbox" name="stayConnected" id="stayConnected" />
                        <label for="stayConnected">Rester connecté</label>
                    </div>
                    <button type="submit">Se Connecter</button>
                </form>
            </section>
        </main>
    </body>
</html>
