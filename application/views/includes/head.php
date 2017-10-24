<!DOCTYPE html>
<html>
<head>
    <title><?= isset($title) ? $title : 'Teckmeb' ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
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
    <header>
        <?php
        if ($debug) {

            function makeReceivedDataPrintable(&$value) {
                if (is_string($value)) {
                    $value = '"""' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"""';
                }
            }

            $data_print = $data;
            array_walk($data_print, 'makeReceivedDataPrintable');
            ?>
            <pre id="debug">
                <?php print_r($data_print); ?>
            </pre>
            <div id="debug-toolbar" class="row no-margin">
                <a href="/user/session" class="btn-flat">session</a>
                <a href="/user/fillnotif" class="btn-flat">fill notifs</a>
            </div>
            <?php
        } ?>

        <?php
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
        ?><nav class="nav-extended small-caps">
            <div class="nav-wrapper">
                <a class="brand-logo" href="/">Teckmeb</a>
                <a href="#" data-activates="nav-mobile" class="button-collapse">
                    <i class="material-icons">&#xE5D2;<!--menu--></i></a>
                <ul class="right hide-on-med-and-down">
                    <?php foreach ($nav[$_SESSION['user_type']] as $item => $url) {
                        $class = (isset($page) ? $page : '') === $item
                            ? ' class="active"' : '';
                        echo '<li' . $class . '><a href="' . $url . '">' . $item . '</a></li>';
                    } ?>
                    <li>
                        <a id="nav-user-button" class="dropdown-button" href="#!" data-activates="nav-user-menu">
                            <i class="material-icons">&#xE853;<!--account_circle--></i>
                        </a>
                        <ul id="nav-user-menu" class="dropdown-content">
                            <li>
                                <div><?= $_SESSION['surname'] ?></div>
                                <div><?= $_SESSION['name'] ?></div>
                            </li>
                            <li class="divider"></li>
                            <li><a href="/user/disconnect">Déconnexion</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="side-nav" id="nav-mobile">
                    <?php
                    foreach ($nav[$_SESSION['user_type']] as $item => $url)
                    { ?>
                        <li><a href="<?= $url ?>"><?= $item ?></a></li>
                        <?php
                    } ?>
                    <li class="divider"></li>
                    <li>
                        <a class="dropdown-button" id="m-nav-user-button" href="#!" data-activates="m-nav-user-menu">
                            <?= $_SESSION['surname'] . ' ' . $_SESSION['name'] ?>
                            <i class="material-icons right">&#xE5C5;<!--arrow_drop_down--></i>
                        </a>
                        <ul id="m-nav-user-menu" class="dropdown-content">
                            <li><a href="/user/disconnect">Déconnexion</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <?php if ($_SESSION['user_type'] === 'student'):
                $curr_url = explode('/', current_url());
                $len_url = count($curr_url);

                // If url ends with '/Sx'
                if (preg_match('/^S\d$/', $curr_url[$len_url - 1])) {
                    $curr_url = array_slice($curr_url, 0, $len_url - 1);
                }
                $curr_url = join('/', $curr_url);

                $active = isset($data['semester']) ? $data['semester'] : '';
                $class = 'class="active"';
                ?>
                <div class="nav-content">
                    <ul class="tabs tabs-transparent">
                    <?php
                    if (!isset($data['max_semester'])) {
                        $data['max_semester'] = 4;
                    }

                    for ($i = 1; $i <= $data['max_semester']; $i++)
                    { ?>
                        <li class="tab"><a target="_self" href="<?= $curr_url . "/S$i" ?>"
                                <?= $active == "S$i" ? $class : '' ?>>Semestre <?= $i ?></a></li>
                        <?php
                    }
                    ?>
                    </ul>
                </div>
            <?php endif; ?>
        </nav>
    </header>
