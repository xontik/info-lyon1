    <main>
        <?php
        if( isset($data['marks'][0]) ) {

            $ens = null;
            foreach ($data['marks'] as $mark) {

                if($mark->idEnseignement != $ens) {
                    if ($ens !== null)
                        echo '</div></section>';
                    $ens = $mark->idEnseignement; ?>

                    <section>
                    <header>
                        <h1><?php echo $mark->codeMatiere; ?></h1>
                        <h2><?php echo $mark->nomMatiere; ?></h2>
                        <h3>Coefficient : <?php echo $mark->coefficientMatiere; ?></h3>
                    </header>
                    <div>
                <?php } ?>

                <article>
                    <h2><?php echo $mark->nomControle; ?></h2>
                    <div>
                        <p>Note : <?php echo $mark->valeur . "/" . $mark->diviseur; ?></p>
                        <p>Date : <?php echo $mark->dateControle; ?></p>
                        <p>Coefficient : <?php echo $mark->coefficient; ?></p>
                        <p>Moyenne : <?php echo !empty($mark->average) ? $mark->average : "Non calculée"; ?></p>
                        <p>Médiane : <?php echo !empty($mark->median) ? $mark->median : "Non calculée"; ?></p>
                    </div>
                </article>
                <?php
            }

            echo '</div></section>';
        } else {
            echo '<div class="empty">Pas de notes sur le semestre</div>';
        }
        ?>
    </main>
