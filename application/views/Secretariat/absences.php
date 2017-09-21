    <main>
        <section id="color-code">
            <ul>
                <li>Pas d'absence</li>
                <li>Absence justifiée</li>
                <li>Absence</li>
                <li>Retard</li>
                <li>Contrôle</li>
                <li>Infirmerie</li>
            </ul>
        </section>
        <section id="absences_table">
            <div id="table_static">
                <h1>Étudiants</h1>
                <div class="wrapper">
                    <div id="table_group_list">
                        <?php
                        foreach($data['groups'] as $group => $students_number) {
                            echo '<p style="height: ' . ($students_number * 26 - 1) . 'px; ">'
                                . $group
                                . '</p>';
                        }
                        ?>
                    </div>
                    <div id="table_stud_list">
                        <?php
                        $last_group = null;
                        foreach($data['absences'] as $student) {
                            echo '<p';
                            if ($last_group !== $student['groupe']) {
                                if (!is_null($last_group)) {
                                    echo ' class="group-change"';
                                }
                                $last_group = $student['groupe'];
                            }
                            echo '>' . $student['nom'] . ' ' . $student['prenom'] . '</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="wrapper">
                <table>
                    <thead>
                        <tr>
                            <?php
                            $curr_date = clone $data['begin_date'];
                            $last_month = null;
                            for ($i = 0; $i <= $data['day_number']; $i++) {
                                $curr_month = $curr_date->format('F');
                                if ($last_month !== $curr_month) {
                                    $colspan = days_in_month($curr_date->format('n'), $curr_date->format('Y'));
                                    echo '<td colspan="' . $colspan . '">' . $curr_month . '</td>';
                                    $last_month = $curr_month;
                                }

                                $curr_date->modify('+1 day');
                            }
                            unset($curr_month);
                            unset($last_month);
                            ?>
                        </tr>
                        <tr>
                            <?php
                            // table head
                            $curr_date = clone $data['begin_date'];
                            for ($i = 0; $i <= $data['day_number']; $i++) {
                                echo('<td>' . $curr_date->format('j') . '</td>');
                                $curr_date->modify('+1 day');
                            }
                            unset($curr_date);
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // table content
                        $last_group = null;
                        foreach ($data['absences'] as $student) {
                            echo '<tr';
                            if ($last_group !== $student['groupe']) {
                                if (!is_null($last_group)) {
                                    echo ' class="group-change"';
                                }
                                $last_group = $student['groupe'];
                            }
                            echo '>';

                            for ($i = 0; $i <= $data['day_number']; $i++) {
                                $classes = array();
                                $curr_absence = null;
                                if (isset($student['absences'][$i])) {
                                    $curr_absence = $student['absences'][$i];
                                    $classes[] = 'abs-' . strtolower($curr_absence->typeAbsence);
                                    if ($curr_absence->justifiee) {
                                        $classes[] = 'abs-justifiee';
                                    }

                                    $time_period = substr($curr_absence->dateDebut, -8, 5)
                                        . ' - '
                                        . substr($curr_absence->dateFin, -8, 5);
                                    $justify = $curr_absence->justifiee ? 'Oui' : 'Non';
                                    $absence_type = $curr_absence->typeAbsence;
                                }

                                echo '<td'
                                    . (!empty($classes)
                                        ? ' class="' . join(' ', $classes) . '"': '')
                                    . '>';
                                if (!is_null($curr_absence)) {
                                    ?><div>
                                        <p>Horaire : <?= $time_period ?></p>
                                        <p>Justifiée : <?= $justify ?></p>
                                        <p><?= $absence_type ?></p>
                                    </div>
                                <?php
                                }
                                echo '</td>';
                            } // for each day

                            echo '</tr>';
                        } // for each student
                        ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
