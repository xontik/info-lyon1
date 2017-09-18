    <main>
        <section id="color-code">

        </section>
        <section id="absences_table_wrapper">
            <table id="absences_table">
                <thead>
                    <tr>
                        <th rowspan="2">Etudiants</th>
                        <?php
                        $curr_date = clone $data['begin_date'];
                        $last_month = null;
                        for ($i = 0; $i < $data['day_number']; $i++) {
                            $curr_month = $curr_date->format('F');
                            if ($last_month !== $curr_month) {
                                $colspan = cal_days_in_month(
                                    CAL_GREGORIAN,
                                    $curr_date->format('n'),
                                    $curr_date->format('Y')
                                );
                                echo '<td colspan="' . $colspan . '">' . $curr_month . '</td>';
                            }

                            $last_month = $curr_month;
                            $curr_date->modify('+1 day');
                        }
                        ?>
                    </tr>
                    <tr>
                        <?php
                        // table head
                        $curr_date = clone $data['begin_date'];
                        for ($i = 0; $i < $data['day_number']; $i++) {
                            echo('<td>' . $curr_date->format('j') . '</td>');
                            $curr_date->modify('+1 day');
                        }
                        ?>
                    </tr>
                </thead>
                <?php
                    // table content
                    foreach ($data['absences'] as $student) {
                        echo '<tr>';
                        echo '<th>' . $student['nom'] . ' ' . $student['prenom'] . '</th>';
                        for($i = 0; $i < $data['day_number']; $i++) {
                            $classes = array();
                            if (isset($student['absences'][$i])) {
                                $classes[] = 'abs-' . strtolower($student['absences'][$i]->typeAbsence);
                                if ($student['absences'][$i]->justifiee) {
                                    $classes[] = 'abs-justifiee';
                                }
                            }

                            echo '<td'
                                . (!empty($classes) ? ' class="' . join(' ', $classes) . '"' : '')
                                . '></td>';
                        }

                        echo '</tr>';
                    }
                ?>
            </table>
        </section>
    </main>
