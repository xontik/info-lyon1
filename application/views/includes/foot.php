    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js"></script>
    <script type="text/javascript" src="<?= js_url('script') ?>"></script>
    <?php
    if (ENVIRONMENT === 'development') { ?>
        <script type="text/javascript" src="<?= js_url('debug') ?>"></script>
        <?php
    }

    if ( isset($js) ) {
        foreach ($js as $j) {
            echo '<script type="text/javascript" src="' . js_url($j) . '"></script>' . PHP_EOL;
        }
    }
    ?>
</body>
</html>
