    <main class="container">
        <div class="section">
            <h4>Contrôle <?= $data['control']->controlName ?></h4>
            <h5><?= $data['subject']->subjectCode . ' - ' . $data['subject']->subjectName ?></h5>
            <h5>le <?= (new DateTime($data['control']->controlDate))->format('d/m/Y') ?></h5>
        </div>
        <div class="divider"></div>
        <form method="post" action="<?= base_url('Process_Mark/add/'. ($data['control']->idControl)) ?>">
            <div class="section container">
                <div class="row">
                    <div class="col s5 right-align">
                        <h5>Élève</h5>
                    </div>
                    <div class="col s2 offset-s1">
                        <h5>Note /<?= (float) $data['control']->divisor ?></h5>
                    </div>
                </div>
                <?php
                foreach ($data['marks'] as $mark)
                { ?>
                    <div class="row valign-wrapper no-margin">
                        <div class="col s5  pull-s4 right-align">
                            <span><?= $mark->surname . ' ' . $mark->name ?></span>
                        </div>
                        <div class="col s2 offset-s1 pull-s4">
                            <input type="number" name="<?= $mark->idStudent ?>" id="<?= $mark->idStudent ?>"
                                   value="<?= !is_null($mark->value) ? $mark->value : '' ?>"
                                    min="0" step="0.25"/>
                        </div>
                    </div>
                    <?php
                } ?>
            </div>
            <div class="divider"></div>
            <div class="section btn-footer">
                <button type="submit" class="btn waves-effect">Envoyer</button>
                <a class="btn-flat" href="<?= base_url('Control')?>">Retour</a>
            </div>
        </form>
    </main>
