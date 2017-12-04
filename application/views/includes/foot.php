    <script type="text/javascript" src="<?= js_url('jquery/jquery-3.2.1') ?>"></script>
    <script type="text/javascript" src="<?= js_url('materialize/materialize') ?>"></script>
    <script type="text/javascript" src="<?= js_url('script') ?>"></script>
    <?php
    if (ENVIRONMENT === 'development') { ?>
        <script type="text/javascript" src="<?= js_url('debug') ?>"></script>
        <?php
    }

    if (isset($js)) {
        foreach ($js as $file) { ?>
            <script type="text/javascript" src="<?= js_url($file) ?>"></script>
            <?php
        }
    }
    ?>
</body>
</html>
