    <main>

    <?php
    $ptutActu = '';
        if(!empty($var['ptuts'])){
            foreach($var['ptuts'] as $ptut){
                if ($ptut->nomGroupe != $ptutActu){
                ?>
        <div>
            <p>Nom du groupe : <?=$ptut->nomGroupe ?></p>
            <p>Membres du groupe : </p><?php } ?><p> <?=$ptut->prenom ?></p>
        </div>
        <?php
                $ptutActu = $ptut->nomGroupe;
            }}
    else { ?> <p> test inverse</p> <?php } ?>



    </main>
