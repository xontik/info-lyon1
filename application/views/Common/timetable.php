<?php
    $days = array('Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi');

    $pageUrl = $data['pageUrl'];

    $empty = empty($data['timetable']);
    $datetime = $data['date'];
    $weekNum = $datetime->format('W');

    $minTime = $data['minTime'];
    $minFloat = timeToFloat($minTime);
    $maxTime = $data['maxTime'];
    $maxFloat = timeToFloat($maxTime);

    $maxHours = $maxFloat - $minFloat;
    if ($maxHours <= 3) {
        $pixelPerHour = 150;
    } else if ($maxHours <= 5) {
        $pixelPerHour = 115;
    } else {
        $pixelPerHour = 55;
    }


    $activeTime = null;
    $now = new DateTime();

    $loopDay = clone $datetime;
    $loopDay->modify('-' . ($datetime->format('N') - 1) . ' day');
?>
<main>
    <div class="container">
        <h4 class="header">Emploi du temps</h4>
    </div>
    <div id="timetable-wrapper" style="height: <?= $maxHours * $pixelPerHour ?>px;"
         class="section center-align <?= $empty ? 'empty' : '' ?>">
        <a href="<?= base_url($pageUrl . ($weekNum - 1)) ?>">
            <i class="material-icons medium">keyboard_arrow_left</i>
        </a>
        <a class="hide-on-large-only"
           href="<?= base_url($pageUrl) ?>">
            <i class="material-icons small">today</i>
        </a>
        <a class="hide-on-large-only"
           href="<?= base_url($pageUrl . ($weekNum + 1)) ?>">
            <i class="material-icons medium">keyboard_arrow_right</i>
        </a>
        <div class="hours <?= $empty ? 'hide' : '' ?> hide-on-med-and-down">
            <?php
            for ($i = $minFloat; $i <= $maxFloat; $i += 0.5) { ?>
                <?php
                if ($i - floor($i) == 0) {
                    ?>
                    <div><?= $i ?>h</div>
                    <?php
                } else {
                    ?>
                    <div>30</div>
                    <?php
                }
            } ?>
        </div>
        <div id="timetable">
            <?php
            for ($dayNum = 1; $dayNum <= 5; $dayNum++) {
                $emptyDay = $empty || empty($data['timetable'][$dayNum]);

                if (is_null($datetime)) {
                    if (!is_null($activeTime)) {
                        $activeTime = '00:00';
                    }
                } else if (!$now->diff($datetime)->invert && $dayNum >= $datetime->format('N')) {
                    $activeTime = $datetime->format('H:i');
                    $datetime = null;
                }
                ?>
                <div class="events <?= $emptyDay ? 'hide-on-med-and-down' : '' ?>">
                    <h5><?= $days[$dayNum-1] . ' ' . $loopDay->format('d/m') ?></h5>
                    <?php
                    if (!$emptyDay) {
                        $day = $data['timetable'][$dayNum];

                        $lastTimeEnd = null;

                        foreach ($day as $event) {
                            // Fill time
                            if (is_null($lastTimeEnd)) {
                                // Fill if day doesn't begin at min time
                                if ($event['timeStart'] != $minTime) {
                                    fillTime($minTime, $event['timeStart'], $maxHours);
                                }
                            } else if ($lastTimeEnd != $event['timeStart']) {
                                fillTime($lastTimeEnd, $event['timeStart'], $maxHours);
                            }
                            $lastTimeEnd = $event['timeEnd'];

                            $active = '';
                            if (!is_null($activeTime) && $activeTime <= $event['timeEnd']) {
                                $active = 'class="active"';
                                $activeTime = null;
                            }

                            ?>
                            <div <?= $active ?>
                                style="height: <?= computeTimeToHeight($event['timeStart'], $event['timeEnd'], $maxHours) ?>;">
                                <div class="hide-on-large-only">
                                    <div><?= $event['timeStart'] ?></div>
                                    <div><?= $event['timeEnd'] ?></div>
                                </div>
                                <div>
                                    <h5 data-tooltip="<?= $event['name'] ?>" data-delay="750"
                                        class="truncate tooltipped"><?= $event['name'] ?></h5>
                                    <div class="truncate"><?= $event['teachers'] ?></div>
                                </div>
                                <div class="hide-on-med-and-down"><?= $event['groups'] ?></div>
                                <div><i class="tiny material-icons hide-on-med-and-down">location_on</i><?= $event['location'] ?></div>
                            </div>
                            <?php
                        }

                        // Add a fill if day doesn't end at max time
                        if (!is_null($lastTimeEnd)) {
                            $timeEnd = timeToFloat($lastTimeEnd);
                            if ($timeEnd != $maxFloat) {
                                fillTime($lastTimeEnd, $maxTime, $maxHours);
                            }
                        }
                    }
                    ?>
                </div>
                <?php
                $loopDay->modify('+1 day');
            } ?>
        </div>
        <?php
        if (!$empty) {
            ?>
            <a class="hide-on-large-only"
               href="<?= base_url($pageUrl . ($weekNum - 1)) ?>">
                <i class="material-icons medium">keyboard_arrow_left</i>
            </a>
            <a class="hide-on-large-only"
               href="<?= base_url($pageUrl) ?>">
                <i class="material-icons small">today</i>
            </a>
            <a href="<?= base_url($pageUrl . ($weekNum + 1)) ?>">
                <i class="material-icons medium">keyboard_arrow_right</i>
            </a>
            <?php
        } else {
            ?>
            <a class="hide-on-med-and-down" href="<?= base_url($pageUrl . ($weekNum + 1)) ?>">
                <i class="material-icons medium">keyboard_arrow_right</i>
            </a>
            <?php
        } ?>
    </div>
    <?php
    if ($empty) {
        ?>
        <div class="section center-align">
            <?php
            if ($data['timetable'] === FALSE) {
                ?>
                <p>Votre emploi du temps n'est pas configuré.</p>
                <p><a href="<?= base_url('Timetable/edit') ?>">Cliquez ici pour rentrer une ressource ADE</a></p>
                <?php
            }
            else {
                ?>
                <p><span class="flow-text">Pas de cours cette semaine</span></p>
                <?php
            } ?>
        </div>
        <?php
    } ?>
    <div class="section container row">
        <?php
        foreach ($data['menu'] as $name => $url) {
            ?>
            <p class="col">
                <a class="btn-flat" href="<?= base_url($url) ?>"><?= $name ?></a>
            </p>
            <?php
        } ?>
    </div>
</main>
