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
                <?php
                $days = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
                $months = array(
                    'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre','Décembre');

                echo $days[ date('w') ] . ' ' . date('j') . ' ' . $months[ date('n') - 1 ]; ?>
            </div>
            <div class="column-content">
                <?php
                if ( empty($calendar) ) { ?>
                    <div class="error">Pas de cours</div>
                <?php
                } else {
                    $items = array();
                    $times = array();

                    foreach($calendar as $event) {
                        $item = '';
                        $item = '<div class="edt-item" style="height: '
                            . computeTimeToHeight($event['time_start'], $event['time_end'])
                            . '; ">';
                        $item .= '<h2>' . $event['name'] . '</h2>';
                        $item .= '<p>' . $event['groups'] . '</p>';

                        if ( strpos($event['teachers'], ',') === FALSE) {
                            $item .= '<p>' . $event['teachers'] . '</p>';
                        } else {
                            $firstTeacher = explode(', ', $event['teachers'])[0];
                            $item .= '<p title="' . $event['teachers'] . '">' . $firstTeacher . ', ...</p>';
                        }
                        $item .= '<p>' . $event['time_start'] . ' → ' . $event['time_end'] . '</p>';
                        $item .= '<p>' . html_img('location.png', 'salle') . $event['location'] . '</p>';
                        $item .= '</div>' . PHP_EOL;

                        $items[ $event['time_start'] ] = $item;
                        $times[ $event['time_start'] ] = $event['time_end'];
                    }

                    ksort($items, SORT_STRING);

                    $lastTimeEnd = '';

                    foreach ($items as $time => $event) {
                        if ($lastTimeEnd === '') {
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
