<?php
    $now = new DateTime();
?>
<main class="row">
    <div class="col s12 m5 l4 xl3 push-m7 push-l8 push-xl9">
        <?= $data['side-timetable'] ?>
    </div>
    <div class="col s12 m7 l8 xl9 pull-m5 pull-l4 pull-xl3">
        <p></p>
        <h4 class="header container">Tableau de bord</h4>
        <div class="row">
            <div class="col s12 l6">
                <div class="card grey lighten-5">
                    <div class="card-content">
                        <a href="<?= base_url('Absence') ?>" class="card-title">Absences</a>
                        <?php
                        if (!$data['absence']) {
                            ?>
                            <p>Vous n'avez pas été absent ce semestre</p>
                            <?php
                        } else {
                            $date = DateTime::createFromFormat('Y-m-d H:i:s', $data['absence']->beginDate);
                            $diff = $now->diff($date);
                            ?>
                            <div>
                                <p><?= $data['absenceCount']['unjustified'] ?> demi-journée(s) d'absence non-justifiée(s)</p>
                                <p><?= $data['absenceCount']['justified'] ?> demi-journée(s) d'absence justifiée(s)</p>
                            </div>
                            <div class="section no-pad-bot">
                                <p>Dernière absence : <?= readableTimeDifference($diff) ?></p>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
                <div class="card grey lighten-5">
                    <div class="card-content">
                        <?php
                        if ($data['project']) {
                            ?>
                            <a href="<?= base_url('Project') ?>" class="card-title"><?= $data['project']->projectName ?></a>
                            <?php
                            if ($data['appointment']) {
                                $dateAppointment = DateTime::createFromFormat(
                                    'Y-m-d H:i:s',
                                    $data['appointment']->finalDate
                                );
                                if ($dateAppointment === FALSE) {
                                    ?>
                                    <b>Proposition de rendez-vous</b>
                                    <?php
                                    if (!$data['hasDateProposal']) {
                                        $cardAction = '<a class="btn-flat" href="'
                                            . base_url('Project#proposeDate') . '">Proposer une date</a>';
                                        ?>
                                        <p>Pas de proposition de date</p>
                                        <?php
                                    } else {
                                        $cardAction = '<a class="btn-flat"
                                           href="' . base_url('Project') . '">Répondre</a>';
                                        ?>
                                        <p>Des propositions de date sont disponibles</p>
                                        <?php
                                    }
                                } else {
                                    $diff = $now->diff($dateAppointment);
                                    ?>
                                    <b>Prochain rendez-vous</b>
                                    <p>
                                        le <?= $dateAppointment->format('d/m/Y à H:i') ?>
                                        (<?= readableTimeDifference($diff) ?>)
                                    </p>
                                    <?php
                                }
                            } else {
                                $cardAction = '<a class="btn-flat"
                                    href="' . base_url('Process_Appointment/create/' . $data['project']->idProject) . '">
                                    Proposer un rendez-vous</a>';
                                ?>
                                <b>Pas de rendez-vous prévu</b>
                                <?php
                            }
                        } else {
                            ?>
                            <a href="<?= base_url('Project') ?>" class="card-title">Projets</a>
                            <p>Vous n'avez pas de groupe de projet</p>
                            <?php
                        } ?>
                    </div>
                    <?php
                    if (isset($cardAction)) {
                        ?>
                        <div class="card-action"><?= $cardAction ?></div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="col s12 l6">
                <div class="card grey lighten-5">
                    <div class="card-content">
                        <div>
                            <a href="<?= base_url('Mark') ?>" class="card-title">Notes</a>
                            <?php
                            if (empty($data['average'])) {
                                ?>
                                <p>Vous n'avez pas encore eu de note</p>
                                <?php
                            } else if ($data['mark']) {
                                $date = DateTime::createFromFormat('Y-m-d', $data['mark']->controlDate);
                                $diff = $now->diff($date);
                                ?>
                                <b><?= $data['mark']->controlName ?></b>
                                <div class="section">
                                    <span><?= $data['mark']->value ?></span>
                                    <small>/<?= $data['mark']->divisor ?></small>
                                    <p>Coefficient: <?= $data['mark']->coefficient ?></p>
                                </div>
                                <p><?= ucfirst(readableTimeDifference($diff)) ?></p>
                                <?php
                            } ?>
                        </div>
                        <div class="section no-pad-bot">
                            <?php
                            foreach ($data['average'] as $average) {
                                ?>
                                <b><?= $average->teachingUnitCode . ' ' . $average->teachingUnitName ?></b>
                                <p>Moyenne : <?= $average->average ?> /20</p>
                                <p>Coefficient : <?= (float) $average->coefficient ?></p>
                                <?php
                            } ?>
                        </div>
                    </div>
                </div>
                <div class="card grey lighten-5">
                    <?php
                    if (!$data['question']) {
                        ?>
                        <div class="card-content">
                            <a href="<?= base_url('Question') ?>" class="card-title">Questions</a>
                            <p>Vous n'avez pas posé de question ou pas reçu de réponse</p>
                        </div>
                        <?php
                    } else {
                        $date = DateTime::createFromFormat('Y-m-d H:i:s', $data['question']->answerDate);
                        $diff = $now->diff($date);

                        $teacher = $data['question']->teacher ? 'class="right-align"' : '';
                        ?>
                        <div class="card-content">
                            <a href="<?= base_url('Question') ?>" class="card-title">Questions</a>
                            <div class="section">
                                <b><?= $data['question']->title ?></b>
                                <p <?= $teacher ?>><?= $data['question']->answerContent ?></p>
                            </div>
                            <p><?= ucfirst(readableTimeDifference($diff)) ?></p>
                        </div>
                        <div class="card-action">
                            <a href="<?= base_url('Question/detail/' . $data['question']->idQuestion ) ?>">Répondre</a>
                        </div>
                        <?php
                    } ?>
                </div>
            </div>
        </div>
    </div>
</main>
