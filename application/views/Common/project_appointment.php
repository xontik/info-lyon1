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
                <span>Membres :</span>
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
                <div class="col s12 m6 l5 card grey lighten-4">
                    <div class="card-content">
                        <?php
                        if (!is_null($data['lastAppointment']))
                        {
                            $date = new DateTime($data['lastAppointment']->finalDate);
                            $diff = $now->diff($date);
                            ?>
                            <span class="card-title">Dernier rendez-vous</span>
                            <p><?= ucfirst(readableTimeDifference($diff)) ?></p>

                            <?php
                        } else { ?>
                            <span class="card-title">Aucun ancien rendez-vous</span>
                            <?php
                        } ?>
                    </div>
                </div>
                <div class="col s12 m5 offset-m1 card grey lighten-4">
                    <div class="card-content">
                        <?php
                        if (is_null($data['nextAppointment'])) {
                            ?>
                            <span class="card-title">Pas de rendez-vous prévu</span>
                            <a href="<?= base_url('Process_Appointment/create/'.$data['project']->idProject) ?>"
                               class="btn">Créer un rendez-vous</a>
                            <?php
                        } else if (is_null($data['nextAppointment']->finalDate)) {
                            if (empty($data['proposals'])) {
                                ?>
                                <span class="card-title">Un rendez vous est demandé !</span>
                                <p>Aucune proposition de dates pour le prochain rendez vous</p>
                                <?php
                            } else {
                                ?>
                                <span class="card-title">Propositions de dates</span>
                                <?php
                                foreach($data['proposals'] as $proposition) {
                                    ?>
                                    <div class="divider"></div>
                                    <div class="center-align">
                                        <p><?= (new DateTime($proposition['proposal']->date))->format('d/m/Y H:i') ?></p>
                                        <?php
                                        $accepted = $proposition['proposal']->dateAccepts[$_SESSION['userId']]->accepted; // = 1,0,null
                                        $refused = $proposition['refused']; // = true, false
                                        if ($refused) {
                                            ?>
                                            <div class="red-text">Cette proposition a été refusé</div>
                                            <?php
                                        } else {
                                            if (is_null($accepted)) {
                                                ?>
                                                <form method="POST"
                                                      action="<?= base_url('Process_DateProposal/choose'
                                                          . '/' . $proposition['proposal']->idDateProposal) ?>">
                                                    <button class="btn-flat waves-effect waves-green"
                                                        type="submit" name="accept">Accepter</button>
                                                    <button class="btn-flat waves-effect waves-red"
                                                        type="submit" name="decline">Refuser</button>
                                                </form>
                                                <?php
                                            } else {
                                                if ($accepted) { ?>
                                                    <div class="orange-text">En attente</div>
                                                    <?php
                                                } else { ?>
                                                    <div class="red-text">Vous avez refusé cette date</div>
                                                    <?php
                                                }
                                            }
                                        } ?>
                                    </div>
                                    <?php
                                }
                            }
                        } else {
                            $date = new DateTime($data['nextAppointment']->finalDate);
                            $diff = $now->diff($date);
                            ?>
                            <span class="card-title">Prochain rendez-vous
                                <a class="right" href="<?= base_url('/Process_Appointment/Delete/' . $data['nextAppointment']->idAppointment) ?>" data-confirm="Etes-vous sur de vouloir annuler ce rendez-vous ?">
                                    <i class="material-icons">delete</i>
                                </a>
                            </span>
                            <p><?= ucfirst(readableTimeDifference($diff)) ?></p>
                            <p>Le <?= $date->format('d/m/Y à h:i') ?></p>
                            <?php
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    if (!is_null($data['nextAppointment']) && is_null($data['nextAppointment']->finalDate)) {
        ?>
        <div id="proposeDate" class="card grey lighten-5">
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
        <?php
    } ?>
</main>
