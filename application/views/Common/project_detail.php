<?php
$now = new DateTime();
?>
<main class="container">
    <h4 class="header">Projets tuteurés</h4>

    <div class="card grey lighten-5">
        <div class="card-content">
            <span class="card-title"><?= $data['project']->projectName ?></span>
            <p>Tuteur : <?= $data['tutor']->name ?></p>
            <p>
                <span>Membres : </span>
                <?php
                $count = count($data['members']);
                for ($i = 0; $i < $count; $i++)
                { ?>
                    <span>
                        <?= $data['members'][$i]->name . ($i === $count-1 ? '' : ',') ?>
                    </span>
                    <?php
                } ?>
            </p>
        </div>
    </div>
    <div class="card grey lighten-5">
        <div class="card-content">
            <span class="card-title">Rendez-vous</span>
            <div class="row">
                <?php
                if (!is_null($data['lastAppointment']))
                {
                    $date = new DateTime($data['lastAppointment']->finalDate);
                    $diff = $date->diff($now);
                    ?>
                    <div class="col s12 m6 l5 card grey lighten-4">
                        <div class="card-content">
                            <span class="card-title">Dernier rendez-vous</span>
                            <p><?= readableTimeDifference($diff) ?></p>
                        </div>
                    </div>
                    <?php
                } ?>
                <div class="col s12 m5 offset-m1 card grey lighten-4">
                    <div class="card-content">
                        <?php if (is_null($data['nextAppointment'])) { ?>

                            <span class="card-title">Pas de rendez-vous prévu</span>
                            <a href="<?= base_url('Process_Appointment/create/'.$data['project']->idProject) ?>" class="btn">Créer un rendez-vous</a>

                        <?php } else if (is_null($data['nextAppointment']->finalDate)) {
                            if (empty($data['proposals'])) {
                            ?>
                                <span class="card-title">Aucune proposition de dates pour le prochain rendez vous</span>
                            <?php } else {
                                ?>
                                <span class="card-title">Propositions de dates</span>
                                <?php
                                foreach($data['proposals'] as $proposition)
                                { ?>
                                    <div class="divider"></div>
                                    <div class="center-align">
                                        <p><?= (new DateTime($proposition->date))->format('d/m/Y H:i') ?></p>
                                        <?php
                                        $accepted = $proposition->dateAccepts[$_SESSION['userId']]->accepted;
                                        if (is_null($accepted)) { ?>
                                                <form method="POST" action="<?= base_url('Process_DateProposal/choose'
                                                    . '/' . $proposition->idDateProposal) ?>">
                                                    <button class="btn-flat waves-effect waves-green"
                                                        type="submit" name="accept"> Accepter </button>
                                                    <button class="btn-flat waves-effect waves-red"
                                                        type="submit" name="decline"> Refuser </button>
                                                </form>
                                        <?php
                                        } else {
                                            if ($accepted) { ?>
                                                <div class="orange-text">En attente</div>
                                        <?php
                                            } else { ?>
                                                <div class="red-text">Refusée</div>
                                        <?php }
                                        } ?>
                                    </div>
                            <?php }
                            }
                        }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if (is_null($data['nextAppointment']) || is_null($data['nextAppointment']->finalDate)) { ?>
        <div class="card grey lighten-5">
            <form method="post"
            action="<?= base_url('Process_DateProposal/add/' . $data['project']->idProject) ?>">
                <div class="card-content">
                    <span class="card-title">Proposer une date de rendez-vous</span>
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input type="text" class="datepicker" name="date" id="date">
                            <label for="date">Date proposée</label>
                        </div>
                        <div class="input-field col s12 m6">
                            <input type="text" class="timepicker" name="time" id="time">
                            <label for="time">Heure</label>
                        </div>
                    </div>
                </div>
                <div class="card-action">
                    <button class="btn-flat" type="submit">Proposer</button>
                </div>
            </form>
        </div>
    <?php } ?>
</main>
