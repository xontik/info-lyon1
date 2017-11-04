<?php
    $now = new DateTime();
?>
<main>
    <h2 class="header">Projets tuteurés</h2>
    <h3><?= $data['project']->projectName ?></h3>

    <section>
        <h4>Membres</h4>
        <?php
        foreach($data['members'] as $member)
        { ?>
            <p><?= $member->name ?></p>
            <?php
        } ?>
    </section>
    <section>
        <h4>Rendez-vous</h4>
        <?php
        if (!is_null($data['lastAppointment']))
        {
            $date = new DateTime($data['lastAppointment']->finalDate);
            $diff = $date->diff($now);
            ?>
            <div>
                <h5>Dernier rendez-vous</h5>
                <p><?= readableTimeDifference($diff) ?></p>
            </div>
            <?php
        } ?>
        <div>
            <h5>Prochain rendez-vous</h5>
            <?php
            if (!is_null($data['nextAppointment'])
                && !is_null($data['nextAppointment']->finalDate))
            {
                $date = new DateTime($data['nextAppointment']->finalDate);
                $diff = $date->diff($now);
                ?>
                <p><?= readableTimeDifference($diff) ?></p>
                <p>Le <?= $date->format('d/m/Y') ?></p>
                <?php
            } else if (empty($data['proposals'])) {
                ?>
                <p>Pas de rendez-vous prévu</p>
                <?php
            } else {
                ?>
                <div>
                    <h5>Propositions de dates</h5>
                    <?php
                    foreach($data['proposals'] as $proposition)
                    { ?>
                        <p><?= (new DateTime($proposition->date))->format('d/m/Y') ?></p>
                        <form action="<?= base_url('Process_DateProposal/choose') ?>" method="POST">
                            <input type="hidden" name="proposalId" value="<?= $proposition->idDateProposal ?>">
                            <button type="submit" name="accept">Accepter</button>
                            <button type="submit" name="decline">Refuser</button>
                        </form>
                        <?php
                    }
                    ?>
                </div>
                <?php
            } ?>
            <div>
                <h6>Proposer un rendez-vous</h6>
                <form action="<?= base_url('Process_DateProposal/add') ?>" method="POST">
                    <input type="hidden" name="groupId" value="<?= $data['project']->idProject ?>">
                    <div>
                        <input type="date" name="date" id="date">
                        <label for="date">Date proposée</label>
                    </div>
                    <div>
                        <input type="time" name="time" id="time">
                        <label for="time">Heure</label>
                    </div>
                    <button type="submit">Proposer</button>
                </form>
            </div>
        </div>
    </section>
</main>
