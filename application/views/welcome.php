<?php
defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html>
    <head>
        <title>TECKMEB - Page d'acceuil</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link href="<?= css_url('materialize') ?>" rel="stylesheet">
        <link href="<?= css_url('welcome_page')?>" rel="stylesheet" type="text/css">
    </head>
    <body>
        <main class="container">
            <section id="projects_block">
                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Distinctio hic, illo ipsa iste itaque neque numquam quia quibusdam, quidem quod sed tempore. Aut illo necessitatibus praesentium, quasi rem velit veritatis! Accusantium aliquam amet at aut commodi, dolore doloremque eligendi, ex exercitationem ipsam magnam maxime nobis odit quae reiciendis repudiandae saepe tempore unde vero voluptate. Animi architecto, assumenda cupiditate debitis deserunt ducimus eius exercitationem incidunt, ipsum laudantium magnam minima minus necessitatibus numquam perferendis perspiciatis possimus quam qui quia quis ratione repellendus reprehenderit sapiente similique soluta velit voluptatem voluptatibus. Consequuntur exercitationem explicabo illo mollitia voluptatem! Accusantium adipisci aliquid architecto asperiores blanditiis commodi debitis facere fugiat harum magnam maxime nemo nisi pariatur quaerat quis sapiente, temporibus totam voluptatibus! Eos esse laudantium libero minus molestias necessitatibus neque saepe sint sunt tempore. Aliquam at autem dolor dolore eaque eveniet excepturi facilis labore laudantium molestiae molestias, non odio omnis, quisquam sint sit tempora unde vero. A alias aliquid animi autem debitis doloribus error esse eum eveniet impedit, iure magnam mollitia necessitatibus nobis nostrum omnis quod reiciendis sint suscipit totam, ullam velit voluptate voluptates! Blanditiis delectus deserunt dolore dolorum enim
            </section>
            <section id="connect_block">
                <?php if ( !empty($_SESSION['notif']) ) { ?>
                <div id="notifications">
                    <?php
                    foreach ($_SESSION['notif'] as $notif) {
                        echo '<div class="notif">' . $notif . '</div>';
                    } ?>
                    <i class="material-icons">close</i>
                </div>
                <?php } ?>
                <img class="responsive-img" src="<?= img_url('logo/logo_800.png') ?>" alt="TECKMEB">
                <form action="/user/connect" method="post">
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
                               class="<?= isset($_SESSION['form_errors']['pwd']) ? 'invalid' : '' ?>"/>
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
        <script type="text/javascript" src="<?= js_url('materialize.min') ?>"></script>
    </body>
</html>
