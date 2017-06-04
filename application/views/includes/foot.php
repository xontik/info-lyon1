        <footer>
            <p> Footer here</p>
        </footer>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="<?php echo js_url('head'); ?>"></script>
        <?php
        if ( isset($js) ) {
            foreach ($js as $j) {
                echo '<script src="' . js_url($j) . '"></script>';
            }
        }
        ?>
    </body>
</html>
