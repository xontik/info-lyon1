    <main>

    <?php
        if(!empty($var['ptuts'])){
            foreach($var['ptuts'] as $ptut){
                ?>
        <div>
            <p>Nom du groupe : <?=$ptut->nomGroupe ?></p>
                <?php

                ?>

        </div>
        <?php
            }
        echo '<div class="empty">Des ptut sur le semestre</div>';}






        else {echo 'test inverse';}?>



    </main>
