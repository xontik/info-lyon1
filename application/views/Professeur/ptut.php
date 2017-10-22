<main>
    <?php
    $ptutActu = '';
        if(!empty($data['ptuts'])){
            foreach($data['ptuts'] as $ptut){
                if ($ptut->nomGroupe != $ptutActu){?>
                    <p>- </p>
                    <p>Nom du groupe : <?=$ptut->nomGroupe ?></p>
                    <p>Nombre de proposition de Rendez-vous : <?=$ptut->nbProp?></p>
                    <p>Membres du groupe : </p>
            <?php } ?>

                <p><?=$ptut->prenom ?></p>


        <?php
                $ptutActu = $ptut->nomGroupe;
            }}
    else { ?> <p> Vous n'avez pas de groupe de Ptut</p> <?php } ?>
</main>