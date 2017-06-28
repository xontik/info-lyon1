<div id="edt-view">
    <div id="edt-time-container">
        <div class="column-content">
            <p>8h</p>
            <p>9h</p>
            <p>10h</p>
            <p>11h</p>
            <p>12h</p>
            <p>13h</p>
            <p>14h</p>
            <p>15h</p>
            <p>16h</p>
            <p>17h</p>
            <p>18h</p>
        </div>
    </div>
    <div id="edt-content">
        <div id="edt-day-title">
            <?php
            $days = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
            $months = array(
                'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre','Décembre');

            echo $days[ date('w') ] . ' ' . date('j') . $months[ date('n') - 1 ]; ?>
        </div>
        <div class="column-content">
            <?php
            if ( empty($calendar) ) { ?>
                <div class="error">Impossible de charger<br>l'emploi du temps d'aujourd'hui</div>
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
                    $item .= '<p>' . html_img('location', 'Salle : ') . $event['location'] . '</p>';
                    $item .= '<p>' . $event['time_start'] . ' → ' . $event['time_end'] . '</p>';
                    $item .= '</div>';

                    $items[ $event['time_start'] ] = $item;
                    $times[ $event['time_start'] ] = $event['time_end'];
                }

                ksort($items, SORT_STRING);

                $lastTimeEnd = '';
                foreach ($items as $time => $event) {
                    if ($lastTimeEnd !== '' && $lastTimeEnd !== $time) {
                        $items[$lastTimeEnd] = '<div class="fill" style="height: '
                            . computeTimeToHeight($lastTimeEnd, $time)
                            . ';"></div>';
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
