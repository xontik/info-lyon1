<!doctype html>
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
        ?>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <?php
        $debug = false;
        if (isset($js)) {
            foreach ($js as $j) {
                if ($j == "debug") {
                    $debug = true;
                }
                echo '<script src="' . js_url($j) . '"></script>';
            }
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
            <a href="/"> <!-- TODO href to dashboard if connected, welcome page otherwise -->
                <?php echo html_img('teckmeb_logo', 'Logo Teckmeb') ?>
                <h1>Teckmeb</h1>
            </a>
        </header>
        <nav>
            <p>Menu here</p>
        </nav>
        <main>
<!-- content start here -->
