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
?>
    <div id="side-edt-computer" class="hide-on-small-and-down section z-depth-3">
        <div class="header">
            <h5><?= translateAndFormat($date) ?></h5>
        </div>
        <div class="divider"></div>
        <div class="row">
            <div class="hours col l2">
                <?php for($i = 8; $i <= 17; $i++) { ?>
                    <div><?= $i ?>h</div>
                    <div>30</div>
                    <?php
                } ?>
                <div>18h</div>
            </div>
            <div class="content col l10">
                <?php
                if ( empty($timetable) ) { ?>
                    <div class="error">Pas de cours</div>
                <?php
                } else {
                    $timeAtDate = $date->format('H:i');
                    $events = array();
                    $events_times = array();

                    foreach($timetable as $event) {
                        $classes = array('edt-item', 'hoverable');
                        if (isset($timeAtDate) && $timeAtDate >= $event['time_start'] && $timeAtDate < $event['time_end']) {
                            $classes[] = 'current-item';
                            unset($timeAtDate);
                        }

                        $event_dom = '<div class="' . join(' ', $classes). '" '
                            . 'style="height: '
                            . computeTimeToHeight($event['time_start'], $event['time_end'])
                            . '; ">' . PHP_EOL;

                        $event_dom .= '<h5>' . $event['name'] . '</h5>' . PHP_EOL;
                        $event_dom .= '<div>' . $event['groups'] . '</div>' . PHP_EOL;
                        $event_dom .= '<div>' . $event['teachers'] . '</div>' . PHP_EOL;

                        $event_dom .= '<div><i class="material-icons">schedule</i>'
                                . $event['time_start']
                                . ' <i class="material-icons">arrow_forward</i> '
                                . $event['time_end']
                            . '</div>' . PHP_EOL;

                        $event_dom .= '<div><i class="material-icons">location_on</i>'
                            . $event['location']
                            . '</div>' . PHP_EOL;

                        $event_dom .= '</div>' . PHP_EOL;

                        $events[ $event['time_start'] ] = $event_dom;
                        $events_times[ $event['time_start'] ] = $event['time_end'];
                    }

                    ksort($events, SORT_STRING);

                    // If time in not in an edt-item
                    // Select next event
                    if (isset($timeAtDate)) {
                        // Reset internal array pointer
                        $value = reset($events);

                        while ($value !== FALSE) {
                            $time = key($events);

                            if ($time > $timeAtDate) {
                                $value = substr_replace($value, 'id="current-item" ', 5, 0);
                                $items[$time] = $value;
                                break;
                            }
                            $value = next($events);
                        }
                    }

                    $lastTimeEnd = null;

                    foreach ($events as $time => $event) {
                        if (is_null($lastTimeEnd)) {
                            // Fill if day doesn't begin at 08:00
                            if ($time !== '08:00') {
                                $events['08:00'] = '<div class="fill" '
                                . 'style="height: ' . computeTimeToHeight('08:00', $time) . ';">'
                                . '</div>' . PHP_EOL;
                            }
                        }
                        else if ($lastTimeEnd !== $time) {
                            $items[$lastTimeEnd] = '<div class="fill" '
                                . 'style="height: ' . computeTimeToHeight($lastTimeEnd, $time) . ';">'
                                . '</div>' . PHP_EOL;
                        }
                        $lastTimeEnd = $events_times[$time];
                    }

                    // Add a fill if day doesn't end at 18:00
                    if (!is_null($lastTimeEnd) && $lastTimeEnd !== '18:00') {
                        $events[$lastTimeEnd] = '<div class="fill" style="height: '
                            . computeTimeToHeight($lastTimeEnd, '18:00')
                            . ';"></div>';
                    }

                    ksort($events, SORT_STRING);
                    foreach ($events as $event) {
                        echo($event);
                    }
                } ?>
            </div>
        </div>
    </div>
    <div id="side-edt-mobile" class="hide-on-med-and-up container z-depth-3">
        <div class="edt-content">
            <div class="edt-day-title">
                <?= translateAndFormat($date) ?>
            </div>
            <div class="column-content">
                <?php
                if (empty($timetable)) { ?>
                    <div class="error">Pas de cours</div>
                <?php
                } else {
                    usort($timetable, function($item1, $item2) {
                        // There shouldn't be any equal terms
                        return $item1['time_start'] < $item2['time_start'] ? -1 : 1;
                    });

                    $timeAtDate = $date->format('H:i');

                    $currentEvent = NULL;
                    $nextEvent = NULL;

                    foreach ($timetable as $event) {
                        if (isset($timeAtDate) && $event['time_start'] <= $timeAtDate && $timeAtDate < $event['time_end']) {
                            $currentEvent = $event;
                        } else if ($event['time_start'] > $timeAtDate) {
                            $nextEvent = $event;
                            break;
                        }
                    }

                    function echoEvent($event, $title) {
                        if ($event !== NULL) {
                            $item = '<div class="edt-item">';

                            $item .= '<h1>' . $title . '</h1>';
                            $item .= '<h2>' . $event['name'] . '</h2>';
                            $item .= '<p class="groups">' . $event['groups'] . '</p>';
                            $item .= '<p class="teachers">' . $event['teachers'] . '</p>';
                            $item .= '<p class="time">' . $event['time_start'] . ' → ' . $event['time_end'] . '</p>';
                            $item .= '<p class="location"><i class="material-icons">location_on</i>' . $event['location'] . '</p>';

                            $item .= '</div>' . PHP_EOL;
                            echo $item;
                        }
                    }

                    echoEvent($currentEvent, 'Actuellement');
                    echoEvent($nextEvent,'Prochain cours');

                } ?>
            </div>
        </div>
    </div>
