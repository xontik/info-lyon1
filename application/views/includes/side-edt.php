<?php
/**
 * DATAS NEEDED
 * DateTime $date - Date displayed, should be date of the displayed timetable
 * array $calendar - The timetable
 *
 * USAGE:
 * In controller:
 * $data['side-edt'] = $this->load->view(
 *      'includes/side-edt',
 *      array('date' => [...], 'timetable' => [...]),
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

ksort($timetable);

?>
    <div id="side-edt-large" class="hide-on-small-and-down card center-align">
        <div class="card-content">
            <span class="card-title"><?= translateAndFormat($date) ?></span>
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
                        // TODO $timetable is sorted, insert fill in one time
                        $timeAtDate = $date->format('H:i');
                        $events = array();
                        $events_times = array();

                        foreach ($timetable as $event) {
                            $classes = array('hoverable', 'valign-wrapper');
                            if (isset($timeAtDate) && $timeAtDate >= $event['time_start'] && $timeAtDate < $event['time_end']) {
                                $classes[] = 'z-depth-2';
                                unset($timeAtDate);
                            }

                            $event_dom = '<div class="' . join(' ', $classes) . '" '
                                . 'style="height: '
                                . computeTimeToHeight($event['time_start'], $event['time_end'])
                                . '; ">' . PHP_EOL;
                            $event_dom .= '<a href="' . $edt_url . '" class="black-text">' . PHP_EOL;

                            $event_dom .= '<h5 title="' . $event['name'] .'" class="truncate">' . $event['name'] . '</h5>' . PHP_EOL;
                            $event_dom .= '<div class="truncate">' . $event['teachers'] . '</div>' . PHP_EOL;
                            $event_dom .= '<div>' . $event['groups'] . '</div>' . PHP_EOL;

                            $event_dom .= '<div><i class="material-icons">location_on</i>'
                                . $event['location']
                                . '</div>' . PHP_EOL;

                            $event_dom .= '</a>' . PHP_EOL;
                            $event_dom .= '</div>' . PHP_EOL;

                            $events[$event['time_start']] = $event_dom;
                            $events_times[$event['time_start']] = $event['time_end'];
                        }

                        ksort($events, SORT_STRING);

                        // If time is not during an event
                        // Select next event
                        if (isset($timeAtDate)) {
                            // Reset internal array pointer
                            $value = reset($events);

                            while ($value !== FALSE) {
                                $time = key($events);

                                if ($time > $timeAtDate) {
                                    // add class current-event
                                    $value = substr_replace($value, 'current-event', 12, 0);
                                    $items[$time] = $value;
                                    break;
                                }
                                $value = next($events);
                            }
                        }

                        $lastTimeEnd = null;

                        // Fill the time
                        foreach ($events as $time => $event) {
                            if (is_null($lastTimeEnd)) {
                                // Fill if day doesn't begin at 08:00
                                if ($time !== '08:00') {
                                    $events['08:00'] = '<div class="fill" '
                                        . 'style="height: ' . computeTimeToHeight('08:00', $time) . ';">'
                                        . '</div>' . PHP_EOL;
                                }
                            } else if ($lastTimeEnd !== $time) {
                                $events[$lastTimeEnd] = '<div class="fill" '
                                    . 'style="height: ' . computeTimeToHeight($lastTimeEnd, $time) . ';">'
                                    . '</div>' . PHP_EOL;
                            }
                            $lastTimeEnd = $events_times[$time];
                        }

                        // Add a fill if day doesn't end at 18:00
                        if (!is_null($lastTimeEnd) && $lastTimeEnd !== '18:00') {
                            $events[$lastTimeEnd] = '<div class="fill" '
                                . 'style="height: ' . computeTimeToHeight($lastTimeEnd, '18:00') . ';">'
                            . '</div>' . PHP_EOL;
                        }

                        ksort($events, SORT_STRING);
                        foreach ($events as $event) {
                            echo($event);
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
