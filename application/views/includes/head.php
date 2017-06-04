<!DOCTYPE html>
<html>
    <head>
        <title><?php echo isset($title) ? $title : 'Teckmeb'; ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" type="text/css" href="<?php echo css_url('style') ?>">
        <link rel="stylesheet" type="text/css" href="<?php echo css_url('head') ?>">
        <?php
        if (isset($css)) {
            foreach ($css as $c) {
                echo '<link rel="stylesheet" type="text/css" href="'.css_url($c) . '">';
            }
        }

        $debug = false;
        if (isset($js)) {
            $debug = in_array('debug', $js);
        }
        ?>

    </head>
    <body>
        <?php if (isset($data) && $debug) { ?>
            <pre id="debug">
                <?php print_r($data); ?>
            </pre>
        <?php } ?>
        <header>
            <a id="header_title" href="/">
                <?php echo html_img('teckmeb_logo.png', 'Logo Teckmeb', '') ?>
            </a>
            <nav>
                <ul>
                    <?php
                        if ( isset($_SESSION['type_user']) )
                        {
                            $nav = array(
                                'student' => array( 'ABSENCES', 'NOTES', 'PTUT', 'QUESTIONS' ),
                                'teacher' => array( 'ABSENCES', 'NOTES', 'PTUT', 'QUESTIONS' ),
                                'secretariat' => array( 'ABSENCES', 'NOTES' )
                            );

                            if (in_array($_SESSION['type_user'], array('student', 'teacher', 'secretariat')))
                            {
                                // Display menu depending on the user
                                foreach ($nav[$_SESSION['type_user']] as $item)
                                    echo '<li><a href="' . strtolower($item) . '">' . $item . '</a></li>';

                            } else {
                                trigger_error('SESSION[\'type_user\'] value error', E_USER_NOTICE);
                            }
                        } else {
                    ?>
                        <li><a href="#">ABSENCES</a></li>
                        <li><a href="#">NOTES</a></li>
                        <li><a href="#">PTUT</a></li>
                        <li><a href="#">QUESTIONS</a></li>
                            <!--<li> Bienvenue sur Teckmeb !</li>-->
                    <?php } //TODO Public menu ?>
                </ul>
            </nav>
            <div id="header_profile">
                <?php echo html_img('header_account.png', 'account') ?>
                <ul>
                    <li>
                        <div>NOM</div>
                        <div>Prénom</div>
                    </li>
                    <li><a href="/user/disconnect">Déconnexion</a></li>
                </ul>
            </div>
        </header>
