    <main>
        <section id="color-code">

        </section>
        <section id="absences_table">
            <div>
                <p>Étudiants</p>
                <?php
                foreach($data['absences'] as $student) {
                    echo '<p>' . $student['nom'] . ' ' . $student['prenom'] . '</p>';
                }
                ?>
            </div>
            <div id="absence_table_wrapper">
                <table id="absences_table_body">
                    <thead id="absences_table_head">
                        <tr>
                            <?php
                            $curr_date = clone $data['begin_date'];
                            $last_month = null;
                            for ($i = 0; $i <= $data['day_number']; $i++) {
                                $curr_month = $curr_date->format('F');
                                if ($last_month !== $curr_month) {
                                    $colspan = days_in_month($curr_date->format('n'), $curr_date->format('Y'));
                                    echo '<td colspan="' . $colspan . '">' . $curr_month . '</td>';
                                }

                                $last_month = $curr_month;
                                $curr_date->modify('+1 day');
                            }
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
                        foreach ($data['absences'] as $student) {
                            echo '<tr>';

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
