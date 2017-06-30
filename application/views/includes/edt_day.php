<!--
DATAS NEEDED
$date Date displayed, should date of the displayed timetable
$calendar The timetable
-->
<div id="edt-view">
    <div id="edt-view-computer">
        <div id="edt-time-container">
            <div class="column-content">
                <div class="edt-item full-hour">8h</div>
                <div class="edt-item half-hour">30</div>
                <div class="edt-item full-hour">9h</div>
                <div class="edt-item half-hour">30</div>
                <div class="edt-item full-hour">10h</div>
                <div class="edt-item half-hour">30</div>
                <div class="edt-item full-hour">11h</div>
                <div class="edt-item half-hour">30</div>
                <div class="edt-item full-hour">12h</div>
                <div class="edt-item half-hour">30</div>
                <div class="edt-item full-hour">13h</div>
                <div class="edt-item half-hour">30</div>
                <div class="edt-item full-hour">14h</div>
                <div class="edt-item half-hour">30</div>
                <div class="edt-item full-hour">15h</div>
                <div class="edt-item half-hour">30</div>
                <div class="edt-item full-hour">16h</div>
                <div class="edt-item half-hour">30</div>
                <div class="edt-item full-hour">17h</div>
                <div class="edt-item half-hour">30</div>
                <div class="edt-item full-hour">18h</div>
            </div>
        </div>
        <div id="edt-content">
            <div id="edt-day-title">
                <?php echo translateAndFormat($date) ?>
            </div>
            <div class="column-content">
                <?php
                if ( empty($calendar) ) { ?>
                    <div class="error">Pas de cours</div>
                <?php
                } else {
                    $timeAtDate = $date->format('H:i');
                    $items = array();
                    $times = array();

                    foreach($calendar as $event) {
                        $item = '<div class="edt-item" ';
                        if (isset($timeAtDate) && $timeAtDate >= $event['time_start'] && $timeAtDate <= $event['time_end']) {
                            $item .= 'id="current-item" ';
                            unset($timeAtDate);
                        }
                        $item .= 'style="height: '
                            . computeTimeToHeight($event['time_start'], $event['time_end'])
                            . '; ">';

                        $item .= '<h2>' . $event['name'] . '</h2>';
                        $item .= '<p class="groups">' . $event['groups'] . '</p>';

                        if ( strpos($event['teachers'], ',') === FALSE) {
                            $item .= '<p class="teachers">' . $event['teachers'] . '</p>';
                        } else {
                            $firstTeacher = explode(', ', $event['teachers'])[0];
                            $item .= '<p class="teachers" title="' . $event['teachers'] . '">' . $firstTeacher . ', ...</p>';
                        }
                        $item .= '<p class="time">' . $event['time_start'] . ' → ' . $event['time_end'] . '</p>';
                        $item .= '<p class="location">' . html_img('location.png', 'salle') . $event['location'] . '</p>';
                        $item .= '</div>' . PHP_EOL;

                        $items[ $event['time_start'] ] = $item;
                        $times[ $event['time_start'] ] = $event['time_end'];
                    }

                    ksort($items, SORT_STRING);

                    // If time in not in an edt-item
                    // Select next event
                    if ( isset($timeAtDate) ) {
                        $value = reset($items);

                        while ($value !== FALSE) {

                            $time = key($items);

                            if ($time > $timeAtDate) {
                                $value = substr_replace($value, 'id="current-item" ', 5, 0);
                                $items[$time] = $value;
                                break;
                            }
                            $value = next($items);
                        }
                    }

                    $lastTimeEnd = '';

                    foreach ($items as $time => $event) {
                        if ($lastTimeEnd === '') {
                            // Fill if day doesn't begin at 08:00
                            if ($time !== '08:00') {
                                $items['08:00'] = '<div class="fill" style="height: '
                                    . computeTimeToHeight('08:00', $time)
                                    . ';"></div>' . PHP_EOL;
                            }
                        }
                        else if ($lastTimeEnd !== $time) {
                            $items[$lastTimeEnd] = '<div class="fill" style="height: '
                                . computeTimeToHeight($lastTimeEnd, $time)
                                . ';"></div>' . PHP_EOL;
                        }
                        $lastTimeEnd = $times[$time];
                    }

                    // Add a fill if day doesn't end at 18:00
                    if ($lastTimeEnd !== '' && $lastTimeEnd !== '18:00') {
                        $items[$lastTimeEnd] = '<div class="fill" style="height: '
                            . computeTimeToHeight($lastTimeEnd, '18:00')
                            . ';"></div>';
                    }

                    ksort($items, SORT_STRING);
                    foreach ($items as $item)
                        echo($item);
                } ?>

            </div>

        </div>
    </div>
    <div id="edt-view-mobile">
        <h1>Truc bidule machin</h1>
    </div>
</div>
