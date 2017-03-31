<!doctype html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <?php
        if(isset($css)) {
            foreach ($css as $c) {
                echo '<link rel="stylesheet" type="text/css" href="'.css_url($c) . '">';
            }
        }

        if(isset($js)){
            foreach ($js as $j) {
                echo '<script src="' . js_url($j) . '"></script>';

            }
        }
        ?>

    </head>
    <body>
        <div>
            <?php if(isset($data)){print_r($data);}?>
        </div>
        <nav>
            <p>Menu here</p>
        </nav>
<!-- content start here -->

