<main>
    <?php
    if(isset($data["marks"][0])){
        echo "<div>";
        $ens = null;
        foreach ($data["marks"] as $mark){
            if($mark->idEnseignement != $ens){
                echo "</div><div>";
                echo "<h2>".$mark->nomMatiere." - ".$mark->codeMatiere."</h2>";
                $ens = $mark->idEnseignement;
            }
            ?>
            <div>
                <h3><? echo $mark->nomControle." le ".$mark->dateControle; ?></h3>
                <p><? echo $mark->valeur."/".$mark->diviseur." coeff : ".$mark->coefficient."  moyenne : "
                        .$mark->average." median : ".$mark->median;?></p>
            </div>
            <?
        }

        echo "</div>";
    }else{
        echo "Pas de Notes";
    }
    ?>
</main>
