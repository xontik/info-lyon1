    <main>
        <section id="color-code">
            <ul>
                <li>Pas d'absence</li>
                <li>Absence justifiée</li>
                <li>Absence</li>
                <li>Retard</li>
                <li>Contrôle</li>
                <li>Infirmerie</li>
                <li>Plusieurs types</li>
            </ul>
        </section>
        <section id="absences-table">
            <div id="table-static">
                <h1>Étudiants</h1>
                <div class="wrapper">
                    <div id="table-group-list">
                        <?php
                        foreach($data['groups'] as $group => $students_number) {
                            $height = $students_number * 26 - 1;
                            echo "<p style=\"height: ${height}px;\">$group</p>";
                        }
                        ?>
                    </div>
                    <div id="table-stud-list">
                        <?php
                        $last_group = null;
                        foreach($data['absences'] as $student) {
                            $class = '';
                            if ($last_group !== $student['groupe']) {
                                if (!is_null($last_group)) {
                                    $class = ' class="group-change"';
                                }
                                $last_group = $student['groupe'];
                            }

                            $missCount = $student['absences']['total'];
                            $dayMissCount = $student['absences']['total_days'];
                            $justifiedMiss = $student['absences']['justified'];

                            echo '<div' . $class . '>'
                                . '<p>' . $student['nom'] . ' ' . $student['prenom'] . '</p>'
                                . html_img('info.png', 'infos');
                            ?>
                                <div>
                                    <p><?= $missCount ?> demi-journée(s)</p>
                                    <?= $missCount >= 2
                                        ? '<p>sur ' . $dayMissCount . ' jour(s)</p>'
                                        : '' ?>
                                    <?= $justifiedMiss > 0
                                        ? '<p>' . $justifiedMiss . ' absence(s) justifiée(s)</p>'
                                        : '' ?>
                                </div>
                            <?php
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div id="table-wrapper">
                <div id="new-absences-wrapper">
                    <div id="new-absences">
                        <header>
                            <h2 id="new-absences-name">Text nom</h2>
                            <h3 id="new-absences-date">Text date</h3>
                        </header>
                        <section>
                            <article>
                                <label for="add-beginTime">Heure de début</label>
                                <p><input type="time" min="07:00" max="21:00" step="1800" name="begin-time" id="add-beginTime"></p>
                            </article>
                            <article>
                                <label for="add-endTime">Heure de fin</label>
                                <p><input type="time" min="07:00" max="21:00" step="1800" name="end-time" id="add-endTime"></p>
                            </article>
                            <article>
                                <label for="add-justified">Justifiée</label>
                                <p><input type="checkbox" name="justified" id="add-justified"></p>
                            </article>
                            <article>
                                <label for="add-absenceType">Type d'absence</label>
                                <p>
                                    <select name="absenceType" id="add-absenceType">
                                        <option value="0" selected>Selectionner...</option>
                                        <?php
                                        foreach($data['absenceTypes'] as $option) {
                                            echo '<option value="' . $option->idTypeAbsence . '">'
                                                . $option->nomTypeAbsence
                                                . '</option>';
                                        }
                                        ?>
                                    </select>
                                </p>
                            </article>
                        </section>
                        <div>
                            <button id="new-absences-submit">Enregistrer</button>
                            <button id="new-absences-cancel">Annuler</button>
                        </div>
                    </div>
                </div>
                <table>
                    <thead id="absences-table-head">
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
                            $today = new DateTime('2017-04-10');
                            $last_month = null;
                            for ($i = 0; $i <= $data['day_number']; $i++) {
                                $class = '';
                                if ($last_month !== $curr_date->format('F')) {
                                    $class = ' class="first-month-day"';
                                    $last_month = $curr_date->format('F');
                                }

                                echo '<td'
                                    . ($curr_date->format('Y-m-d') == $today->format('Y-m-d') ? ' id="active-day"' : '')
                                    . $class . '>'
                                    . $curr_date->format('j')
                                    . '</td>';
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
                                $infos = array();
                                $classes = array();
                                $td_class = '';

                                if (isset($student['absences'][$i])) {
                                    $justified = 0;

                                    foreach($student['absences'][$i] as $curr_absence) {
                                        $abs_class = 'abs-' . strtolower($curr_absence->typeAbsence);
                                        $td_class = $td_class === ''
                                            ? $abs_class
                                            : ($td_class !== $abs_class
                                                ? 'abs-several'
                                                : $td_class);

                                        if ($curr_absence->justifiee) {
                                            $justified += 1;
                                        }

                                        $curr_infos['time_period'] = substr($curr_absence->dateDebut, -8, 5)
                                            . ' - '
                                            . substr($curr_absence->dateFin, -8, 5);
                                        $curr_infos['justify'] = $curr_absence->justifiee ? 'Oui' : 'Non';
                                        $curr_infos['absence_type'] = $curr_absence->typeAbsence;
                                        $infos[] = $curr_infos;
                                    }

                                    // td has absences
                                    $classes[] = 'abs';
                                    $classes[] = $td_class;
                                    // if all absences are justified
                                    if ($justified === count($student['absences'][$i]))
                                        $classes[] = 'abs-justifiee';

                                }

                                echo '<td ' . (!empty($classes)
                                        ? ' class="' . join(' ', $classes) . '"' : '')
                                    . '>';

                                if (!empty($infos)) {
                                    foreach($infos as $info) {
                                        ?>
                                        <div
                                        class="<?= 'abs-' . strtolower($info['absence_type']) ?>">
                                            <p>Horaires : <?= $info['time_period'] ?></p>
                                            <p>Justifiée : <?= $info['justify'] ?>  </p>
                                            <p><?= $info['absence_type'] ?></p>
                                        </div>
                                        <?php
                                    }
                                    if (count($infos) < 2) {
                                        echo '<button>Nouveau</button>';
                                    }
                                }
                                echo '</td>';

                            } // for each day

                            echo '</tr>';
                        } // for each student
                        ?>
                    </tbody>
                </table>
                <table id="header-fixed"></table>
            </div>
        </section>
    </main>
    <script>
        // Needed for absence_table script
        var FIRST_DATE = new Date('<?= $data['begin_date']->format('Y-m-d') ?>');
    </script>
