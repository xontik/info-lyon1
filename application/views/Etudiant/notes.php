    <main>
        <?php
        if (!empty($data['marks'])) {
            $mat = null;

            foreach ($data['marks'] as $mark) {
                if ($mark->codeMatiere !== $mat) {
                    if (!is_null($mat)) {
                        echo '</div></section>';
                    }
                    $mat = $mark->codeMatiere; ?>

                    <section>
                        <header>
                            <h1><?= $mark->codeMatiere ?></h1>
                            <h2><?= $mark->nomMatiere ?></h2>
                            <h3>Coefficient : <?= $mark->coefficientMatiere ?></h3>
                        </header>
                        <div>
                <?php } ?>
                            <article <?= $mark->idDSPromo == null ? '' : 'class="dspromo"' ?>>
                                <h2><?= $mark->nomControle; ?></h2>
                                <div>
                                    <p>Note : <?= $mark->valeur . '/' . $mark->diviseur ?></p>
                                    <p>Date : <?= $mark->dateControle ?></p>
                                    <p>Coefficient : <?= $mark->coefficient ?></p>
                                    <p>Moyenne
                                        : <?= isset($mark->average) ? $mark->average : 'Non calculée' ?></p>
                                    <p>Médiane
                                        : <?= isset($mark->median) ? $mark->median : 'Non calculée' ?></p>
                                </div>
                            </article>
            <?php }
            echo '</div></section>';
        } else {
            echo '<div class="empty">Pas de notes sur le semestre</div>';
        }
        ?>
    </main>

