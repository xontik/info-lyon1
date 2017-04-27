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
            <a id="header_title" href="<?php
                // If connect, link to dashboard, else to welcome page
                echo ( isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === TRUE ? 'dashboard' : '/' );
            ?>">
                <?php echo html_img('teckmeb_logo.png', 'Logo Teckmeb', '') ?>
                <h1>
                    <div id="teck">Teck</div>
                    <div id="meb">meb</div>
                </h1>
            </a>
            <nav>
                <ul>
                    <?php
                        if ( isset($_SESSION['type_user']) )
                        {
                            $nav = array(
                                'student' => array( 'Absences', 'Notes', 'PTUT', 'Questions' ),
                                'teacher' => array( 'Absences', 'Notes', 'PTUT', 'Questions' ),
                                'secretariat' => array( 'Absences', 'Notes' )
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
                        <li><a href="#">Absences</a></li>
                        <li><a href="#">Notes</a></li>
                        <li><a href="#">PTUT</a></li>
                        <li><a href="#">Questions</a></li>
                    <?php } //TODO Public menu ?>
                </ul>
            </nav>
            <div id="header_profile">
                <?php
                    if ( isset($_SESSION['logged_in']) &&
                    $_SESSION['logged_in'] === TRUE ) {
                ?>
                <a href="">
                    <p><?php echo html_img('header_account.png', 'account'); ?></p>
                    <div class="dropdown">
                        <?php
                        if ( isset($_SESSION['user_code']) )
                            echo $_SESSION['user_code'];
                        else
                            trigger_error('User logged in but user_code not set');
                        ?>
                    </div>
                </a>
                <ul>
                    <li>NOM Prénom</li>
                    <li>Déconnexion</li>
                </ul>
                <?php } else { ?>
                <a href="/connect">
                    <p><?php echo html_img('header_account.png', 'account') ?></p>
                    <div>Connexion</div>
                </a>
                <?php } ?>
            </div>
        </header>
