    <main class="container">
        <div class="section">
            <h4>Contrôle <?= $data['control']->nomControle ?></h4>
            <h5><?= $data['matiere']->codeMatiere . ' - ' . $data['matiere']->nomMatiere ?></h5>
            <h5>le <?= (new DateTime($data['control']->dateControle))->format("d/m/Y") ?></h5>
        </div>
        <div class="divider"></div>
        <form method="post" action="<?php echo base_url("process_professeur/addmarks/" . ($data['control']->idControle)); ?>">
            <div class="section">
                <div class="row">
                    <div class="col s6 m4">
                        <h5>Élève</h5>
                    </div>
                    <div class="col s6 m4">
                        <h5>Note /<?= floatval($data['control']->diviseur) ?></h5>
                    </div>
                </div>
                <?php
                foreach ($data['marks'] as $mark)
                { ?>
                    <div class="row valign-wrapper">
                        <div class="col s6 m4 pull-m2">
                            <span class="flow-text"><?= $mark->nom . ' ' . $mark->prenom ?></span>
                        </div>
                        <div class="col s6 m4 pull-m4">
                            <input type="number" name="<?= $mark->numEtudiant ?>" id="<?= $mark->numEtudiant ?>"
                                   value="<?= !is_null($mark->valeur) ? $mark->valeur : '' ?>"/>
                        </div>
                    </div>
                    <?php
                } ?>
            </div>
            <div class="divider"></div>
            <div class="section">
                <button type="submit" class="btn waves-effect">Envoyer</button>
                <a class="btn-flat" href="<?= base_url('Professeur/controle')?>">Retour</a>
            </div>
        </form>
    </main>
