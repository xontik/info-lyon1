    <main>
        <div id="absences_table">
            <header>
                <?php
                $curr_date = $data['begin_date'];
                $last_month = null;
                for ($i = 0; $i < $data['day_number']; $i++) {
                    $curr_month = $curr_date->format('F');
                    if ($last_month !== $curr_month) {
                        echo (!is_null($last_month) ? '</div>' : '') . '<div><h2>' . $curr_month . '</h2>';
                    }
                    echo('<p>' . $curr_date->format('j') . '</p>');
                    $last_month = $curr_month;

                    $curr_date->modify('+1 day');
                }
                echo '</div>';
                ?>
            </header>
            <div id="absence_table_body">

            </div>
        </div>
    </main>
