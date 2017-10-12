    <main>
        <section id="color-code">
            <ul>
                <li>Absence justifiée</li>
                <li>Absence</li>
                <li>Retard</li>
                <li>Contrôle</li>
                <li>Infirmerie</li>
                <li>Plusieurs types</li>
            </ul>
        </section>
        <section id="absences-table" class="row">
            <div id="table-static" class="col l3">
                <h5 class="yellow-text text-accent-4 center-align">Étudiants</h5>
                <div class="row">
                    <div id="table-group-list" class="col l2">
                        <?php
                        foreach($data['groups'] as $group => $students_number) {
                            $height = $students_number * 21 - 1;
                            echo "<p style=\"height: ${height}px;\">$group</p>";
                        }
                        ?>
                    </div>
                    <div id="table-stud-list" class="col l10">
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

                            echo '<div id="' . $student['numEtudiant'] . '"' . $class . '>'
                                . '<p>' . $student['nom'] . ' ' . $student['prenom'] . '</p>'
                                . '<i class="material-icons">info</i>';
                            ?>
                                <div>
                                    <p><?= $missCount ?> demi-journée<?= $missCount > 1 ? 's' : ''?></p>
                                    <?= $missCount > 1
                                        ? '<p>' . $dayMissCount . ' jour' . ($dayMissCount > 1 ? 's' : '') . '</p>'
                                        : '' ?>
                                    <?= $justifiedMiss > 0
                                        ? '<p>' . $justifiedMiss
                                        . ' absence' . ($justifiedMiss > 1 ? 's' : '')
                                        . ' justifiée' . ($justifiedMiss > 1 ? 's' : '') . '</p>'
                                        : '' ?>
                                </div>
                            <?php
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div id="table-wrapper" class="col l9">
                <div id="edition-wrapper" class="hide">
                    <div id="edition">
                        <header>
                            <h2 id="edition-name">Text nom</h2>
                            <h3 id="edition-date">Text date</h3>
                        </header>
                        <div>
                            <section id="edition-morning">
                                <h2>Matinée <i class="material-icons">delete</i></h2>
                                <article>
                                    <div id="am-time">
                                        <p>08h00 - 12h00</p>
                                        <p>08h00 - 10h00</p>
                                        <p>10h00 - 12h00</p>
                                    </div>
                                </article>
                                <article>
                                    <label for="am-justified">Justifiée</label>
                                    <p><input type="checkbox" id="am-justified"></p>
                                </article>
                                <article>
                                    <label for="am-absenceType">Type d'absence</label>
                                    <p>
                                        <select id="am-absenceType">
                                            <option value="0" disabled selected>Selectionner...</option>
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
                            <section id="edition-afternoon">
                                <h2>Après-midi <i class="material-icons">delete</i></h2>
                                <article>
                                    <div id="pm-time">
                                        <p>14h00 - 18h00</p>
                                        <p>14h00 - 16h00</p>
                                        <p>16h00 - 18h00</p>
                                    </div>
                                </article>
                                <article>
                                    <label for="pm-justified">Justifiée</label>
                                    <p><input type="checkbox" id="pm-justified"></p>
                                </article>
                                <article>
                                    <label for="pm-absenceType">Type d'absence</label>
                                    <p>
                                        <select id="pm-absenceType">
                                            <option value="0" disabled selected>Selectionner...</option>
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
                        </div>
                        <footer>
                            <button id="edition-submit">Enregistrer</button>
                            <button id="edition-cancel">Annuler</button>
                        </footer>
                    </div>
                </div>
                <table class="stripped">
                    <thead id="absences-table-head">
                        <tr>
                            <?php
                            $monthes = array('Janvier', 'Février',
                                'Mars', 'Avril', 'Juin', 'Juillet',
                                'Août', 'Septembre', 'Octobre',
                                'Novembre', 'Décembre'
                            );

                            $curr_date = clone $data['begin_date'];
                            $last_month = null;
                            for ($i = 0; $i <= $data['day_number']; $i++) {
                                $curr_month = $curr_date->format('n');
                                if ($last_month !== $curr_month) {
                                    $colspan = days_in_month($curr_date->format('n'), $curr_date->format('Y'));
                                    echo '<td colspan="' . $colspan . '">' . $monthes[$curr_month - 1] . '</td>';
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
                            $today = new DateTime();
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

                                        $curr_infos['absence_id'] = $curr_absence->idAbsence;
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
                                    if ($justified === count($student['absences'][$i])) {
                                        $classes[] = 'abs-justifiee';
                                    }
                                }

                                echo '<td ' . (!empty($classes)
                                        ? ' class="' . join(' ', $classes) . '"' : '')
                                    . '>';

                                if (!empty($infos)) {
                                    foreach($infos as $info) {
                                        ?>
                                        <div id="absn<?= $info['absence_id'] ?>"
                                        class="<?= 'abs-' . strtolower($info['absence_type']) ?>">
                                            <p>Horaires : <?= $info['time_period'] ?></p>
                                            <p>Justifiée : <?= $info['justify'] ?>  </p>
                                            <p><?= $info['absence_type'] ?></p>
                                        </div>
                                        <?php
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
