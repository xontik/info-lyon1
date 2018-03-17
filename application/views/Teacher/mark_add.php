<main class="container">
    <h4 class="header">Contrôle <?= $data['control']->controlName ?></h4>
    <form method="post" action="<?= base_url('Process_Mark/add/'. ($data['control']->idControl)) ?>">
        <div class="card grey lighten-5">
            <div class="card-content">
                <span class="card-title"><?= $data['subject']->subjectCode . ' - ' . $data['subject']->subjectName ?></span>
                <span class="card-title">le <?= (new DateTime($data['control']->controlDate))->format('d/m/Y') ?></span>
                <div class="container">
                    <div class="row">
                        <div class="col s6 m5 right-align"><h5>Élève</h5></div>
                        <div class="col s6 m5 offset-m1">
                            <h5>Note /<?= (float) $data['control']->divisor ?></h5>
                        </div>
                    </div>
                    <?php
                    foreach ($data['marks'] as $mark)
                    { ?>
                        <div class="row valign-wrapper no-margin">
                            <div class="col s6 m5 pull-m2 right-align">
                                <span><?= $mark->surname . ' ' . $mark->name ?></span>
                            </div>
                            <div class="col s6 m3 pull-m3">
                                <input type="number" name="<?= $mark->idStudent ?>" id="<?= $mark->idStudent ?>"
                                       value="<?= !is_null($mark->value) ? $mark->value : '' ?>"
                                        min="0" step="0.01"
                                        max="<?= $data['control']->divisor ?>" />
                            </div>
                        </div>
                        <?php
                    } ?>
                </div>
            </div>
            <div class="card-action">
                <button type="submit" class="btn-flat waves-effect">Envoyer</button>
                <a class="btn-flat waves-effect" href="<?= base_url('Control')?>">Retour</a>
            </div>
        </div>
    </form>
</main>
