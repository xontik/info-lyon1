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
    <div id="timetable" class="section center-align <?= $empty ? 'empty' : '' ?>">
        <a href="<?= base_url('Timetable/' . ($weekNum - 1)) ?>"><i class="material-icons medium">keyboard_arrow_left</i></a>
        <?php
        for ($dayNum = 1; $dayNum <= 5; $dayNum++) {
            ?>
            <div class="events">
                <h5 class="no-margin"><?= $days[$dayNum-1] ?></h5>
                <?php
                if (!$empty && !empty($data['timetable'][$dayNum])) {
                    usort($data['timetable'][$dayNum], 'sortTimetable');
                    $day = $data['timetable'][$dayNum];

                    if (is_null($datetime)) {
                        if (!is_null($activeTime)) {
                            $activeTime = '01:00';
                        }
                    } else if ($dayNum >= $datetime->format('N')) {
                        $activeTime = $datetime->format('H:i');
                        $datetime = null;
                    }

                    $lastTimeEnd = null;

                    foreach ($day as $event) {
                        // Fill time
                        if (is_null($lastTimeEnd)) {
                            // Fill if day doesn't begin at 08:00
                            if ($event['time_start'] !== '08:00') {
                                fillTime('08:00', $event['time_start']);
                            }
                        } else if ($lastTimeEnd !== $event['time_start']) {
                            fillTime($lastTimeEnd, $event['time_start']);
                        }
                        $lastTimeEnd = $event['time_end'];

                        $active = '';

                        if (!is_null($activeTime) && $activeTime <= $event['time_end']) {
                            $active = 'class="active"';
                            $activeTime = null;
                        }
                        ?>
                        <div <?= $active ?>
                            style="height: <?= computeTimeToHeight($event['time_start'], $event['time_end']) ?>; ">
                            <h5 title="<?= $event['name'] ?>" class="truncate"><?= $event['name'] ?></h5>
                            <div class="truncate"><?= $event['teachers'] ?></div>
                            <div><?= $event['groups'] ?></div>
                            <div><i class="tiny material-icons">location_on</i><?= $event['location'] ?></div>
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
        <a href="<?= base_url('Timetable/' . ($weekNum+1)) ?>"><i class="material-icons medium">keyboard_arrow_right</i></a>
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
    <div class="section container">
        <p><a href="<?= base_url('Timetable/edit') ?>" class="btn-flat">Modifier</a></p>
    </div>
</main>
