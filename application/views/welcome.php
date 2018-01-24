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
        <main class="container">
            <div class="row">
                <section class="col s12 l6 push-l6 valign-wrapper center-align" id="connect_block">
                    <div>
                        <img class="responsive-img center-block" src="<?= img_url('logo/logo_800.png') ?>" alt="TECKMEB">
                        <form action="<?= base_url('user/connect') ?>" method="post">
                            <div class="input-field">
                                <i class="material-icons prefix">account_circle</i>
                                <input type="text" id="login" name="login"
                                       value="<?= isset($_SESSION['form']['id']) ? $_SESSION['form']['id'] : ''?>"
                                       class="<?= isset($_SESSION['form_errors']['id']) ? 'invalid' : '' ?>"/>
                                <label for="login">Identifiant</label>
                            </div>
                            <div class="input-field">
                                <i class="material-icons prefix">lock</i>
                                <input type="password" id="password" name="password"
                                       class="<?= isset($_SESSION['form_errors']['password']) ? 'invalid' : '' ?>"/>
                                <label for="password">Mot de passe</label>
                            </div>
                            <div>
                                <input type="checkbox" name="stayConnected" id="stayConnected" />
                                <label for="stayConnected">Rester connecté</label>
                            </div>
                            <p class="center-align">
                                <button class="btn yellow accent-4 waves-effect waves-light" type="submit">Se connecter</button>
                            </p>
                        </form>
                    </div>
                </section>
                <section id="projects_block" class="col s12 l6 pull-l6 valign-wrapper">
                    <div>
                        <div>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A, ad aut deserunt,
                            dolore dolorem, eius eligendi esse eum exercitationem molestias mollitia
                            nesciunt perferendis quaerat qui repellendus sunt tempore ullam velit.
                        </div>
                        <div>Ad autem beatae commodi consequatur debitis dicta dignissimos earum eius
                            eligendi eos facilis fuga, id inventore magni maiores necessitatibus nobis
                            officia perspiciatis porro quam recusandae, rerum sit soluta, tempora unde.
                        </div>
                        <p>
                            <a href="<?= base_url('Gallery') ?>">Accéder à la galerie des projets</a>
                        </p>
                    </div>
                </section>
            </div>
        </main>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
    </body>
</html>
