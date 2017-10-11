    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="<?= js_url('bin/materialize.min') ?>"></script>
    <script type="text/javascript" src="<?= js_url('head') ?>"></script>
    <?php
    if ( isset($js) ) {
        foreach ($js as $j) {
            echo '<script type="text/javascript" src="' . js_url($j) . '"></script>' . PHP_EOL;
        }
    }
    ?>
</body>
</html>
