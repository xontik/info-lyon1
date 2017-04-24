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
        $debug = false;
        if(isset($js)){
            foreach ($js as $j) {
                if($j == "debug"){
                    $debug = true;
                }
                echo '<script src="' . js_url($j) . '"></script>';

            }
        }
        ?>

    </head>
    <body>
        <?php if(isset($data) && $debug){ ?>
        <div id="debug">
            <?php print_r($data);?>
        </div>
        <?php }
        if(isset($_SESSION["notif"])){
            foreach ($_SESSION["notif"] as $notif){
                echo "<p>".$notif."</p>";
            }
        }?>
        <nav>
            <p>Menu here</p>
        </nav>
<!-- content start here -->
