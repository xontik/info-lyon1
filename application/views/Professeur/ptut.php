<main>

    <?php
    $ptutActu = '';
        if(!empty($data['ptuts'])){
            ?>
            <table>
                <tr>
                    <th>Nom du groupe</th>
                    <th>Nombre de proposition</th>
                    <th>Membre du groupe</th>
                </tr>
                <?php
                    foreach($data['ptuts'] as $ptut){
                        if ($ptut->nomGroupe != $ptutActu){ ?>
                            <tr>
                                <td><?=$ptut->nomGroupe ?></td>
                                <td><?=$ptut->nbProp?></td>
                                <td><?php } ?><?=$ptut->prenom?> <?php


                                $ptutActu = $ptut->nomGroupe;}
            ?></td></tr></table><?php }
    else {
     ?> <p> Vous n'avez pas de groupe de Ptut</p> <?php } ?>

</main>