<?php
    $days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi');
?>
<main>
    <div class="container">
        <h4 class="header">Emploi du temps</h4>
    </div>
    <div id="timetable" class="section center-align">
        <?php
        $empty = empty($data['timetable']);

        for ($dayNum = 1; $dayNum <= 5; $dayNum++) {
            ?>
            <div class="events">
                <h5 class="no-margin"><?= $days[$dayNum-1] ?></h5>
                <?php
                if (!$empty && !empty($data['timetable'][$dayNum])) {
                    usort($data['timetable'][$dayNum], 'sortTimetable');
                    $day = $data['timetable'][$dayNum];

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

                        ?>
                        <div
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
        }

        if ($data['timetable'] === FALSE) {
            ?>
            <p>Votre emploi du temps n'est pas configuré.</p>
            <p><a href="<?= base_url('Timetable/edit') ?>">Cliquez ici pour rentrer une ressource ADE</a></p>
            <?php
        } else if ($empty) {
            ?>
            <p>Pas de cours cette semaine</p>
            <?php
        }
        ?>
    </div>
</main>
