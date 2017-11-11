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
?>
    <div id="side-edt-large" class="hide-on-small-and-down card center-align">
        <div class="card-content">
            <a href="<?= base_url('Timetable') ?>" class="card-title"><?= translateAndFormat($date) ?></a>
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
                    if (empty($timetable)) {
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
                        usort($timetable, 'sortTimetable');
                        $timeAtDate = $date->format('H:i');

                        $lastTimeEnd = null;
                        $linksPointer = 0;

                        foreach ($timetable as $event) {
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

                            $classes = array();
                            if (isset($timeAtDate) && $timeAtDate < $event['timeEnd']) {
                                $classes[] = 'z-depth-2';
                                unset($timeAtDate);
                            }

                            if (isset($links[$linksPointer])) {
                                $classes[] = 'hoverable';
                            }

                            ?>
                                <div class="valign-wrapper <?= join(' ', $classes) ?>"
                                    style="height: <?= computeTimeToHeight($event['timeStart'], $event['timeEnd']) ?>; ">
                                    <?php
                                        if (isset($links[$linksPointer])) {
                                            $endtag = '</a>';
                                            echo '<a href="' . base_url($links[$linksPointer]) . '" class="black-text">';
                                        } else {
                                            $endtag = '</div>';
                                            echo '<div>';
                                        }
                                    ?>
                                        <h5 title="<?= $event['name'] ?>" class="truncate"><?= $event['name'] ?></h5>
                                        <div class="truncate"><?= $event['teachers'] ?></div>
                                        <div><?= $event['groups'] ?></div>
                                        <div><i class="tiny material-icons">location_on</i><?= $event['location'] ?></div>
                                    <?= $endtag ?>
                                </div>
                                <?php
                            $linksPointer++;
                        }

                        // Add a fill if day doesn't end at 18:00
                        if (!is_null($lastTimeEnd) && $lastTimeEnd !== '18:00') {
                            fillTime($lastTimeEnd, '18:00');
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
            // TODO Proposer changer ressource
        } ?>
    </div>
