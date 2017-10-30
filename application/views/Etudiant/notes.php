<main class="container">
    <h4 class="header">Notes</h4>
    <div class="section">
        <?php
        if (!empty($data['marks'])) {
            $last_subject = null;

            foreach ($data['marks'] as $mark) {
                // If new subject, put header
                if ($mark->codeMatiere !== $last_subject) {
                    if (!is_null($last_subject)) {
                        // Close card-content then card
                        echo '</div></div></div>';
                    }
                    $last_subject = $mark->codeMatiere;
                    ?><div class="card grey lighten-5">
                        <div class="card-content">
                            <div class="card-title row">
                                <div class="col s12 m6">
                                    <i class="material-icons small left">school</i>
                                    <h5><?= $mark->codeMatiere . ' - ' . $mark->nomMatiere ?></h5>
                                </div>
                                <div class="col s12 m6 right-align">
                                    <span>Coefficient : <?= floatval($mark->coefficientMatiere) ?></span>
                                </div>
                            </div>
                            <div class="divider row"></div>
                            <div class="row center-align">
                                <?php } ?>
                                <div class="col s12 m6 l4 xl3">
                                    <div class="card card-content grey lighten-4">
                                        <span class="card-title"><?= $mark->nomControle; ?></span>
                                        <p>Note : <?= floatval($mark->valeur) . ' / ' . floatval($mark->diviseur) ?></p>
                                        <p>Date : <?= (new DateTime($mark->dateControle))->format('d/m/Y') ?></p>
                                        <p>Coefficient : <?= floatval($mark->coefficient) ?></p>
                                        <p>Moyenne
                                            : <?= isset($mark->average) ? $mark->average : 'Non calculée' ?></p>
                                        <p>Médiane
                                            : <?= isset($mark->median) ? $mark->median : 'Non calculée' ?></p>
                                    </div>
                                </div>
                                <?php
                            } ?>
                            </div>
                        </div>
                    </div>
        <?php
        } else { ?>
            <h5>Pas de notes sur le semestre</h5>
        <?php } ?>
    </div>
</main>

