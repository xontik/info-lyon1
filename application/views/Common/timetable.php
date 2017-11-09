<?php
    $days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi');

    $empty = empty($data['timetable']);
    $weekNum = $data['date']->format('W');
    $datetime = $data['date'];
    $activeTime = null;
?>
<main>
    <div class="container">
        <h4 class="header">Emploi du temps</h4>
    </div>
    <div id="timetable-wrapper" class="section center-align <?= $empty ? 'empty' : '' ?>">
        <a href="<?= base_url('Timetable/' . ($weekNum - 1)) ?>">
            <i class="material-icons medium">keyboard_arrow_left</i>
        </a>
        <a class="hide-on-large-only"
           href="<?= base_url('Timetable') ?>">
            <i class="material-icons small">today</i>
        </a>
        <a class="hide-on-large-only"
           href="<?= base_url('Timetable/' . ($weekNum + 1)) ?>">
            <i class="material-icons medium">keyboard_arrow_right</i>
        </a>
        <div class="hours <?= $empty ? 'empty' : '' ?> hide-on-med-and-down">
            <?php for($i = 8; $i <= 17; $i++) { ?>
                <div><?= $i ?>h</div>
                <div>30</div>
                <?php
            } ?>
            <div>18h</div>
        </div>
        <div id="timetable">
            <?php
            for ($dayNum = 1; $dayNum <= 5; $dayNum++) {
                $emptyDay = $empty || empty($data['timetable'][$dayNum]);

                if (is_null($datetime)) {
                    if (!is_null($activeTime)) {
                        $activeTime = '01:00';
                    }
                } else if ($dayNum >= $datetime->format('N')) {
                    $activeTime = $datetime->format('H:i');
                    $datetime = null;
                }
                ?>
                <div class="events <?= $emptyDay ? 'hide-on-med-and-down' : '' ?>">
                    <h5><?= $days[$dayNum-1] ?></h5>
                    <?php
                    if (!$emptyDay) {
                        usort($data['timetable'][$dayNum], 'sortTimetable');
                        $day = $data['timetable'][$dayNum];

                        $lastTimeEnd = null;

                        foreach ($day as $event) {
                            // Fill time
                            if (is_null($lastTimeEnd)) {
                                // Fill if day doesn't begin at 08:00
                                if ($event['timeStart'] !== '08:00') {
                                    fillTime('08:00', $event['timeStart']);
                                }
                            } else if ($lastTimeEnd !== $event['timeStart']) {
                                fillTime($lastTimeEnd, $event['timeStart']);
                            }
                            $lastTimeEnd = $event['timeEnd'];

                            $active = '';
                            if (!is_null($activeTime) && $activeTime <= $event['timeEnd']) {
                                $active = 'class="active"';
                                $activeTime = null;
                            }
                            ?>
                            <div <?= $active ?>
                                style="height: <?= computeTimeToHeight($event['timeStart'], $event['timeEnd']) ?>; ">
                                <div class="hide-on-large-only">
                                    <div><?= $event['timeStart'] ?></div>
                                    <div><?= $event['timeEnd'] ?></div>
                                </div>
                                <div>
                                    <h5 title="<?= $event['name'] ?>" class="truncate"><?= $event['name'] ?></h5>
                                    <div class="truncate"><?= $event['teachers'] ?></div>
                                </div>
                                <div class="hide-on-med-and-down"><?= $event['groups'] ?></div>
                                <div><i class="tiny material-icons hide-on-med-and-down">location_on</i><?= $event['location'] ?></div>
                            </div>
                            <?php
                        }

                        // Add a fill if day doesn't end at 18:00
                        if (!is_null($lastTimeEnd) && $lastTimeEnd !== '18:00') {
                            fillTime($lastTimeEnd, '18:00');
                        }
                    }
                    ?>
                </div>
                <?php
            } ?>
        </div>
        <?php
        if (!$empty) {
            ?>
            <a class="hide-on-large-only"
               href="<?= base_url('Timetable/' . ($weekNum - 1)) ?>">
                <i class="material-icons medium">keyboard_arrow_left</i>
            </a>
            <a class="hide-on-large-only"
               href="<?= base_url('Timetable') ?>">
                <i class="material-icons small">today</i>
            </a>
            <a href="<?= base_url('Timetable/' . ($weekNum + 1)) ?>">
                <i class="material-icons medium">keyboard_arrow_right</i>
            </a>
            <?php
        } else {
            ?>
            <a class="hide-on-med-and-down" href="<?= base_url('Timetable/' . ($weekNum + 1)) ?>">
                <i class="material-icons medium">keyboard_arrow_right</i>
            </a>
            <?php
        } ?>
    </div>
    <div class="section center-align">
        <?php
        if ($empty) {
            if ($data['timetable'] === FALSE) {
                ?>
                <p>Votre emploi du temps n'est pas configuré.</p>
                <p><a href="<?= base_url('Timetable/edit') ?>">Cliquez ici pour rentrer une ressource ADE</a></p>
                <?php
            }
            else {
                ?>
                <span class="flow-text">Pas de cours cette semaine</span>
                <?php
            }
        } ?>
    </div>
    <div class="section container row">
        <?php
        if ($data['timetable'] !== false) { ?>
            <p class="col hide-on-med-and-down">
                <a href="<?= base_url('Timetable') ?>" class="btn-flat">Revenir à aujourd'hui</a>
            </p>
            <p class="col">
                <a class="btn-flat"
                    href="<?= base_url('Process_Timetable/update/' . $data['resource']) ?>">Mettre à jour
                </a>
            </p>
        <?php } ?>
        <p class="col"><a class="btn-flat" href="<?= base_url('Timetable/edit') ?>">Modifier</a></p>
    </div>
</main>
