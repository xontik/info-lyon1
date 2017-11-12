<main class="row">
    <div class="col s12 m5 l4 xl3 push-m7 push-l8 push-xl9">
        <?= $data['side-timetable'] ?>
    </div>
    <div class="col s12 m7 l8 xl9 pull-m5 pull-l4 pull-xl3">
        <p></p>
        <h4 class="header container">Tableau de bord</h4>
        <div class="row">
            <div class="section card grey lighten-5 col s12 l5 offset-l1">
                <div class="card-content">
                    <a href="<?= base_url('Absence') ?>" class="card-title">Absences</a>

                    <?php
                    if (is_null($data['absence'])) {
                        ?>
                        <p>Vous n'avez pas été absent ce semestre</p>
                        <?php
                    } else {
                        ?>
                        <pre><?= print_r($data['absence'], true) ?></pre>
                        <?php
                    } ?>
                </div>
                <div class="card-action">
                    <a href="<?= base_url('Absence') ?>">Consulter mes absences</a>
                </div>
            </div>
            <div class="card grey lighten-5 col s12 l5 offset-l1">
                <?php
                if (is_null($data['mark'])) {
                    ?>
                    <div class="card-content">
                        <a href="<?= base_url('Mark') ?>" class="card-title">Notes</a>
                        <p>Vous n'avez pas encore eu de note</p>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="card-content">
                        <a href="<?= base_url('Mark') ?>" class="card-title">Notes</a>
                        <pre><?= print_r($data['mark'], true) ?></pre>
                    </div>
                    <?php
                } ?>
                <div class="card-action">
                    <a href="<?= base_url('Mark') ?>">Consulter mes notes</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="card grey lighten-5 col s12 l5 offset-l1">
                <div class="card-content">
                    <a href="<?= base_url('Project') ?>" class="card-title">Projet tuteuré</a>
                    <pre><?= print_r($data['appointment'], true)
                        . print_r($data['nextDateProposal'], true) ?>
                    </pre>
                </div>
            </div>
            <div class="card grey lighten-5 col s12 l5 offset-l1">
                <?php
                if (is_null($data['question'])) {
                    ?>
                    <div class="card-content">
                        <a href="<?= base_url('Question') ?>" class="card-title">Questions</a>
                        <p>Vous n'avez pas posé de question ou pas reçu de réponse</p>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="card-content">
                        <a href="<?= base_url('Question') ?>" class="card-title">Questions</a>
                        <pre><?= print_r($data['question'], true) ?></pre>
                    </div>
                    <div class="card-action">
                        <a href="<?= base_url('Question/' . $data['question']->idQuestion ) ?>">Répondre</a>
                    </div>
                    <?php
                } ?>
            </div>
        </div>
    </div>
</main>
