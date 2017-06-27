<div id="edt_view">
    <div id="edt_time_container">
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
    <div id="edt_content">
        <p id="edt_day_title"><?php
            $days = array('Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
            $months = array(
                'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre','Décembre');

            echo $days[ date('w') ] . ' ' . date('j') . $months[ date('n') - 1 ];
        ?></p>

    </div>
</div>
