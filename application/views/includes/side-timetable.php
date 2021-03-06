<?php
/**
 * DATAS NEEDED
 * DateTime $date       Date displayed, should be date of the displayed timetable
 * array    $calendar   The timetable
 * string   $minTime    First hour
 * string   $maxTime    Last hour
 */
define('MIN_HOURS', 5);

$empty = empty($timetable);

$minFloat = timeToFloat($minTime);
$maxFloat = timeToFloat($maxTime);
$hours = $maxFloat - $minFloat;

if ($hours < MIN_HOURS) {
    $padding = (MIN_HOURS - $hours) / 2;

    $minFloat -= $padding;
    $minTime = floatToTime($minFloat);

    $maxFloat += $padding;
    $maxTime = floatToTime($maxFloat);

    $hours = $maxFloat - $minFloat;
    unset($padding);
}
?>
    <div id="side-edt-large" class="hide-on-small-and-down card center-align">
        <div class="card-content">
            <span class="card-title"><?= translateAndFormat($date) ?></span>
            <div class="row">
                <div class="hours col s2">
                    <?php
                    for ($i = $minFloat; $i <= $maxFloat; $i += 0.5) {
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
                <div class="content col s10">
                    <?php
                    if ($empty) {
                        ?>
                        <section class="section">
                            <?php
                            if ($timetable === false) {
                                ?>
                                <p>Votre emploi du temps n'est pas configuré.</p>
                                <p><a href="<?= base_url('Timetable/edit') ?>">Cliquez ici pour rentrer une ressource ADE</a></p>
                                <?php
                            } else {
                                ?>
                                <p>Pas de cours</p>
                                <?php
                            } ?>
                        </section>
                        <?php
                    } else {
                        $timeAtDate = $date->format('H:i');

                        $lastTimeEnd = null;

                        foreach ($timetable as $event) {
                            // Fill time
                            if (is_null($lastTimeEnd)) {
                                // Fill if day doesn't begin at 08:00
                                if ($event['timeStart'] !== $minTime) {
                                    fillTime($minTime, $event['timeStart'], $hours);
                                }
                            } else if ($lastTimeEnd !== $event['timeStart']) {
                                fillTime($lastTimeEnd, $event['timeStart'], $hours);
                            }
                            $lastTimeEnd = $event['timeEnd'];

                            $classes = array();
                            if (isset($timeAtDate) && $timeAtDate < $event['timeEnd']) {
                                $classes[] = 'z-depth-2';
                                unset($timeAtDate);
                            }

                            $link = isset($event['link']) ? $event['link'] : false;
                            if ($link) {
                                $classes[] = 'hoverable';
                            }

                            ?>
                            <div class="valign-wrapper <?= join(' ', $classes) ?>"
                                style="height: <?= computeTimeToHeight($event['timeStart'], $event['timeEnd'], $hours) ?>; ">
                                <?php
                                    if ($link) { ?>
                                        <a href="<?= base_url($link) ?>" class="black-text">
                                        <?php
                                        $endtag = '</a>';
                                    } else { ?>
                                        <div>
                                        <?php
                                        $endtag = '</div>';
                                    }
                                ?>
                                    <h5 title="<?= $event['name'] ?>" class="truncate"><?= $event['name'] ?></h5>
                                    <div class="truncate"><?= $event['teachers'] ?></div>
                                    <div><?= $event['groups'] ?></div>
                                    <div><i class="tiny material-icons">location_on</i><?= $event['location'] ?></div>
                                <?= $endtag ?>
                            </div>
                            <?php
                        }

                        // Add a fill if day doesn't end at 18:00
                        if (!is_null($lastTimeEnd) && $lastTimeEnd !== '18:00') {
                            fillTime($lastTimeEnd, $maxTime, $hours);
                        }

                    } ?>
                </div>
            </div>
        </div>
    </div>
    <div id="side-edt-small" class="hide-on-med-and-up center-align">
        <?php
        if ($empty) { ?>
            <div class="card">
                <?php
                if ($timetable === false) {
                    ?>
                    <div class="card-content">
                        <span class="card-title">Votre emploi du temps n'est pas configuré</span>
                    </div>
                    <div class="card-action">
                        <a class="btn-flat" href="<?= base_url('Timetable/edit') ?>">Configurer</a>
                    </div>
                    <?php
                } else {
                    ?>
                    <div class="card-content">
                        <a href="<?= base_url('Timetable') ?>" class="card-title">Pas de cours</a>
                    </div>
                    <?php
                } ?>
            </div>
        <?php
        } else {
            $currentEvent = null;
            $nextEvent = null;

            $now = new DateTime();
            if ($date->diff($now)->d > 0) {
                $nextEvent = $timetable[0];
            } else {
                $timeAtDate = $date->format('H:i');

                foreach ($timetable as $event) {
                    if (isset($timeAtDate) && $event['timeStart'] <= $timeAtDate && $timeAtDate < $event['timeEnd']) {
                        $currentEvent = $event;
                    } else if ($event['timeStart'] > $timeAtDate) {
                        $nextEvent = $event;
                        break;
                    }
                }
            }

            $events = array(
                'Actuellement' => $currentEvent,
                'Prochain cours' => $nextEvent
            );

            foreach ($events as $title => $event) {
                if (!is_null($event)) { ?>
                    <div class="card">
                        <a href="<?= base_url('Timetable') ?>" class="black-text">
                            <div class="card-content flow-text">
                                <span class="card-title"><?= $title ?></span>
                                <h5><?= $event['name'] ?></h5>
                                <p class="truncate"><?= $event['teachers'] ?></p>
                                <p><i class="material-icons">group</i> <?= $event['groups'] ?></p>
                                <p>
                                    <i class="material-icons">location_on</i> <?= $event['location'] ?>
                                </p>
                            </div>
                        </a>
                    </div>
                    <?php
                }
            }
        } ?>
    </div>
