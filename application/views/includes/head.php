<!doctype html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php
        if(isset($css)) {
            foreach ($css as $c) {
                echo '<link rel="stylesheet" type="text/css" href="'.css_url($c) . '">';
            }
        }

        if(isset($js)){
            foreach ($js as $j) {
                echo '<script src="' . js_url($j) . '.js"></script>';

            }
        }
        ?>

    </head>
    <body>
        <nav>
            <p>Menu here</p>
        </nav>
<!-- content start here -->

