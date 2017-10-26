<?php
/**
 * DATAS NEEDED
 * DateTime $date - Date displayed, should be date of the displayed timetable
 * array $calendar - The timetable
 * array $links - The links for each event (optionnal)
 *
 * USAGE:
 * In controller:
 * $data['side-edt'] = $this->load->view(
 *      'includes/side-edt',
 *      array('date' => [...], 'timetable' => [...], 'links' = [...]),
 *      TRUE
 * );
 *
 * In view
 * <?= $data['side-edt'] ?>
 */
$edt_url = explode('/', current_url());

$edt_url = array_splice($edt_url, 0, 4);
$edt_url[] = 'EDT';

$edt_url = join('/', $edt_url);

usort($timetable, 'sortTimetable');

?>
    <div id="side-edt-large" class="hide-on-small-and-down card center-align">
        <div class="card-content">
            <!-- href="/Timetable" -->
            <a href="#!" class="card-title"><?= translateAndFormat($date) ?></a>
            <div class="row">
                <div class="hours col s2">
                    <?php for($i = 8; $i <= 17; $i++) { ?>
                        <div><?= $i ?>h</div>
                        <div>30</div>
                        <?php
                    } ?>
                    <div>18h</div>
                </div>
                <div class="content col s10">
                    <?php
                    if ( empty($timetable) ) { ?>
                        <div>Pas de cours</div>
                    <?php
                    } else {
                        $timeAtDate = $date->format('H:i');

                        function fill_time($from, $to) {
                            ?><div class="fill" style="height: <?= computeTimeToHeight($from, $to) ?>"></div>
                            <?php
                        }

                        $lastTimeEnd = null;
                        $linksPointer = 0;

                        foreach ($timetable as $event) {
                            // Fill time
                            if (is_null($lastTimeEnd)) {
                                // Fill if day doesn't begin at 08:00
                                if ($event['time_start'] !== '08:00') {
                                    fill_time('08:00', $event['time_start']);
                                }
                            } else if ($lastTimeEnd !== $event['time_start']) {
                                fill_time($lastTimeEnd, $event['time_start']);
                            }
                            $lastTimeEnd = $event['time_end'];

                            $classes = array();
                            if (isset($timeAtDate) && $timeAtDate < $event['time_end']) {
                                $classes[] = ' z-depth-2';
                                unset($timeAtDate);
                            }

                            if (isset($links[$linksPointer]) && $links[$linksPointer] !== null) {
                                $classes[] = 'hoverable';
                            }

                            ?>
                                <div class="valign-wrapper <?= join(' ', $classes) ?>"
                                    style="height: <?= computeTimeToHeight($event['time_start'], $event['time_end']) ?>; ">
                                    <?php
                                        if (isset($links[$linksPointer]) && $links[$linksPointer] !== null) {
                                            $endtag = '</a>';
                                            echo '<a href="' . $links[$linksPointer] . '" class="black-text">';
                                        } else {
                                            $endtag = '</div>';
                                            echo '<div>';
                                        }
                                    ?>
                                        <h5 title="<?= $event['name'] ?>" class="truncate"><?= $event['name'] ?></h5>
                                        <div class="truncate"><?= $event['teachers'] ?></div>
                                        <div><?= $event['groups'] ?></div>
                                        <div><i class="material-icons">location_on</i><?= $event['location'] ?></div>
                                    <?= $endtag ?>
                                </div>
                                <?php
                            $linksPointer++;
                        }

                        // Add a fill if day doesn't end at 18:00
                        if (!is_null($lastTimeEnd) && $lastTimeEnd !== '18:00') {
                            fill_time($lastTimeEnd, '18:00');
                        }

                    } ?>
                </div>
            </div>
        </div>
    </div>
    <div id="side-edt-small" class="hide-on-med-and-up center-align">
        <?php
        if (empty($timetable)) { ?>
            <div class="card">
                <div class="card-content">
                    <span class="card-title">Pas de cours</span>
                </div>
            </div>
        <?php
        } else {
            $currentEvent = NULL;
            $nextEvent = NULL;

            $now = new DateTime();
            if ($date->diff($now)->d > 0) {
                $nextEvent = $timetable[0];
            } else {
                $timeAtDate = $date->format('H:i');

                foreach ($timetable as $event) {
                    if (isset($timeAtDate) && $event['time_start'] <= $timeAtDate && $timeAtDate < $event['time_end']) {
                        $currentEvent = $event;
                    } else if ($event['time_start'] > $timeAtDate) {
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
                        <a href="<?= $edt_url ?>" class="black-text">
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
