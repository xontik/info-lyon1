<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html>
    <head>
        <title>TECKMEB - Page d'acceuil</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="<?= css_url('welcome_page')?>" rel="stylesheet" type="text/css">
    </head>
    <body>
        <main class="container row valign-wrapper">
            <section id="projects_block" class="hide-on-med-and-down">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio hic, illo ipsa iste itaque neque numquam quia quibusdam, quidem quod sed tempore. Aut illo necessitatibus praesentium, quasi rem velit veritatis! Accusantium aliquam amet at aut commodi, dolore doloremque eligendi, ex exercitationem ipsam magnam maxime nobis odit quae reiciendis repudiandae saepe tempore unde vero voluptate. Animi architecto, assumenda cupiditate debitis deserunt ducimus eius exercitationem incidunt, ipsum laudantium magnam minima minus necessitatibus numquam perferendis perspiciatis possimus quam qui quia quis ratione repellendus reprehenderit sapiente similique soluta velit voluptatem voluptatibus. Consequuntur exercitationem explicabo illo mollitia voluptatem! Accusantium adipisci aliquid
            </section>
            <section id="connect_block">
                <img class="responsive-img center-block" src="<?= img_url('logo/logo_800.png') ?>" alt="TECKMEB">
                <form action="<?= base_url('user/connect') ?>" method="post">
                    <div class="input-field">
                        <i class="material-icons prefix">account_circle</i>
                        <input type="text" id="form-id" name="id"
                               value="<?= isset($_SESSION['form']['id']) ? $_SESSION['form']['id'] : ''?>"
                               class="<?= isset($_SESSION['form_errors']['id']) ? 'invalid' : '' ?>"/>
                        <label for="form-id">Identifiant</label>
                    </div>
                    <div class="input-field">
                        <i class="material-icons prefix">lock</i>
                        <input type="password" id="form-password" name="password"
                               class="<?= isset($_SESSION['form_errors']['password']) ? 'invalid' : '' ?>"/>
                        <label for="form-password">Mot de passe</label>
                    </div>
                    <div>
                        <input type="checkbox" name="stayConnected" id="stayConnected" />
                        <label for="stayConnected">Rester connecté</label>
                    </div>
                    <button class="btn waves-effect waves-light" type="submit">Se connecter</button>
                </form>
            </section>
        </main>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
    </body>
</html>
