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
        ?><nav class="nav-extended">
            <div class="nav-wrapper">
                <a class="brand-logo small-caps" href="/">Teckmeb</a>
                <!-- computer nav -->
                <ul class="right hide-on-med-and-down">
                    <?php foreach ($nav[$_SESSION['user_type']] as $item => $url) {
                        $active = (isset($page) ? $page : '') === $item
                            ? ' active' : '';
                        echo '<li class="small-caps ' . $active . '"><a href="' . $url . '">' . $item . '</a></li>';
                    } ?>
                    <li>
                        <a class="dropdown-button"
                           data-activates="nav-notifications" data-constrainwidth="false">
                            <i class="material-icons notifications-icon">
                                <?= empty($notifications) ? 'notifications_none' : 'notifications' ?>
                            </i>
                        </a>
                        <ul id="nav-notifications" class="dropdown-content">
                            <?php
                            if (empty($notifications)) {
                                ?>
                                <li><p>Pas de notifications</p></li>
                                <?php
                            } else {
                                foreach ($notifications as $notif) { ?>
                                    <li data-notif-id="<?= $notif['id'] ?>" data-notif-link="<?= $notif['link'] ?>"
                                        class="notif notif-<?= $notif['type'] ?> notif-<?= $notif['storage'] ?>">
                                        <div class="valign-wrapper">
                                            <i class="material-icons left"><?= $notif['icon'] ?></i>
                                            <span><?= $notif['content'] ?></span>
                                        </div>
                                    </li>
                                    <?php
                                }
                            } ?>
                        </ul>
                    </li>
                    <li>
                        <a class="dropdown-button"
                           data-activates="nav-user" data-constrainwidth="false">
                            <i class="material-icons">account_circle</i>
                        </a>
                        <ul id="nav-user" class="dropdown-content">
                            <li>
                                <div><?= $_SESSION['surname'] ?></div>
                                <div><?= $_SESSION['name'] ?></div>
                            </li>
                            <li class="divider"></li>
                            <li class="small-caps"><a href="/user/disconnect">Déconnexion</a></li>
                        </ul>
                    </li>
                </ul>

                <!-- mobile nav -->
                <a class="button-collapse hide-on-large-only" href="#" data-activates="nav-mobile">
                    <i class="material-icons">menu</i>
                </a>
                <ul class="right hide-on-large-only">
                    <li>
                        <a href="#m-notifications" class="modal-trigger">
                            <i class="material-icons right notifications-icon">
                                <?= empty($notifications) ? 'notifications_none' : 'notifications' ?>
                            </i>
                        </a>
                    </li>
                </ul>
                <ul class="side-nav" id="nav-mobile">
                    <?php
                    foreach ($nav[$_SESSION['user_type']] as $item => $url)
                    {
                        $active = (isset($page) ? $page : '') === $item
                            ? ' active' : '';
                        echo '<li class="small-caps' . $active . '"><a href="' . $url . '">' . $item . '</a></li>';
                    } ?>
                    <li class="divider"></li>
                    <li>
                        <a class="dropdown-button" data-activates="m-nav-user">
                            <?= $_SESSION['surname'] . ' ' . $_SESSION['name'] ?>
                            <i class="material-icons right">keyboard_arrow_down</i>
                        </a>
                        <ul id="m-nav-user" class="dropdown-content">
                            <li class="small-caps"><a href="/user/disconnect">Déconnexion</a></li>
                        </ul>
                    </li>
                </ul>
                <div id="m-notifications" class="modal modal-fixed-footer black-text">
                    <div class="modal-content">
                        <h4 class="center-align">Notifications</h4>
                        <div class="collection">
                            <?php
                            if (empty($notifications)) {
                                ?>
                                <div class="collection-item">Pas de notifications</div>
                                <?php
                            } else {
                                foreach ($notifications as $notif) { ?>
                                    <div data-notif-id="<?= $notif['id'] ?>" data-notif-link="<?= $notif['link'] ?>"
                                        class="collection-item notif notif-<?= $notif['type'] ?> notif-<?= $notif['storage'] ?>">
                                        <div class="valign-wrapper">
                                            <i class="material-icons left"><?= $notif['icon'] ?></i>
                                            <span><?= $notif['content'] ?></span>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <div class="modal-footer btn-footer">
                        <button class="btn waves-effect waves-light modal-action modal-close">Fermer</button>
                    </div>
                </div>
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
