<!DOCTYPE html>
<html>
    <head>
        <title><?= isset($title) ? $title : 'Teckmeb' ?></title>
        <meta charset="utf-8">
        <!-- Let browser know website is optimized for mobile -->
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Import Google Icon Font -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <!-- Import materialize.css -->
        <link rel="stylesheet" type="text/css" href="<?= css_url('materialize') ?>"  media="screen,projection">

        <?php
        if ( !empty($css) ) {
            foreach ($css as $c) {
                echo '<link rel="stylesheet" type="text/css" href="' . css_url($c) . '">';
            }
        } else {
            echo '<link rel="stylesheet" type="text/css" href="' . css_url('style') . '">';
        }

        $debug = isset($js) && in_array('debug', $js) && isset($data); ?>

    </head>
    <body>
    <?php
        if ($debug) {

            function makeReceivedDataPrintable(&$value) {
                if (is_string($value)) {
                    $value = '"""' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"""';
                }
            }

            echo '<pre id="debug">' . PHP_EOL;
            array_walk($data, 'makeReceivedDataPrintable');
            print_r($data);
            echo '</pre>' . PHP_EOL;
        }

        if ( !empty($_SESSION['notif']) ) { ?>
        <div id="notifications">
            <?php
            foreach ($_SESSION['notif'] as $notif) {
                echo '<div class="notif">' . $notif . '</div>';
            } ?>
            <i class="material-icons">&#xE5CD;</i>
        </div>
        <?php }

        $nav = array(
            'student' => array(
                'absences' => '/Etudiant/Absence',
                'notes' => '/Etudiant/Note',
                'ptut' => '/Etudiant/PTUT',
                'questions' => '/Etudiant/Question'
            ),
            'teacher' => array(
                'absences' => '/Professeur/Absence',
                'controles' => '/Professeur/Controle',
                'ptut' => '/Professeur/PTUT',
                'questions' => '/Professeur/Question'
            ),
            'secretariat' => array(
                'absences' => '/Secretariat/Absence'
            )
        );
        ?>
        <nav class="nav-extended">
            <div class="nav-wrapper">
                <a class="brand-logo" href="/">Teckmeb</a>
                <a href="#" data-activates="nav-mobile" class="button-collapse">
                    <i class="material-icons">&#xE5D2;</i></a>
                <ul class="right hide-on-med-and-down">
                    <?php foreach ($nav[$_SESSION['user_type']] as $item => $url) {
                        echo '<li><a href="' . $url . '">' . $item . '</a></li>';
                    } ?>
                    <li>
                        <a class="dropdown-button" href="#!" data-activates="nav-user-menu">
                            <i class="material-icons">&#xE853;</i></a>
                    </li>
                </ul>
                <ul class="side-nav" id="nav-mobile">
                    <li><h2><?= $_SESSION['surname'] . ' ' . $_SESSION['name'] ?></h2></li>
                    <?php foreach ($nav[$_SESSION['user_type']] as $item => $url) {
                        echo '<li><a href="' . $url . '">' . $item . '</a></li>';
                    } ?>
                </ul>

                <!-- dropdown content -->
                <ul id="nav-user-menu" class="dropdown-content">
                    <li><?= $_SESSION['surname'] . ' ' . $_SESSION['name'] ?></li>
                    <li class="divider"></li>
                    <li><a href="/user/disconnect">Déconnexion</a></li>
                </ul>
            </div>
            <div class="nav-content">
                <ul class="tabs tabs-transparent">
                    <li class="tab"><a href="#">Semestre 1</a></li>
                    <li class="tab"><a href="#">Semestre 2</a></li>
                    <li class="tab"><a href="#">Semestre 3</a></li>
                    <li class="tab"><a href="#">Semestre 4</a></li>
                </ul>
            </div>
        </nav>

