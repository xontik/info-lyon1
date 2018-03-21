<main>
    <?php
    if ($data['loaded']) {
        ?>
        <div id="color-code" class="section">
            <ul class="row">
                <li>Absence justifiée</li>
                <li>Absence</li>
                <li>Retard</li>
                <li>Contrôle</li>
                <li>Infirmerie</li>
                <li>Plusieurs types</li>
            </ul>
        </div>
        <div id="absences-table" class="section row">
            <div id="table-static" class="col l3 xl2 no-padding">
                <h5 class="center-align">Étudiants</h5>
                <div class="row">
                    <div id="table-group-list" class="col l1 no-padding">
                        <?php
                        foreach ($data['groups'] as $group => $students_number) {
                            $height = $students_number * 22 - 1;
                            echo "<p style=\"height: ${height}px;\">$group</p>";
                        }
                        ?>
                    </div>
                    <div id="table-stud-list" class="col l11 no-padding">
                        <?php
                        $last_group = null;
                        foreach ($data['absences'] as $student) {
                            $class = '';
                            if ($last_group !== $student->groupName) {
                                if (!is_null($last_group)) {
                                    $class = ' class="group-change"';
                                }
                                $last_group = $student->groupName;
                            }

                            $missCount = $student->absences['total'];
                            $dayMissCount = $student->absences['totalDays'];
                            $justifiedMiss = $student->absences['justified'];

                            ?>
                            <div id="<?= $student->idStudent ?>" <?= $class ?>>
                                <p>
                                  <a class="black-text" href="<?= base_url('Student/profile/' . $student->idStudent) ?>">
                                    <?= $student->name ?>
                                  </a>
                                </p>
                                <i class="material-icons">info</i>
                                <div>
                                    <p><?= $missCount ?>
                                        demi-journée<?= $missCount > 1 ? 's' : '' ?></p>
                                    <?= $missCount > 1
                                        ? '<p>' . $dayMissCount . ' jour' . ($dayMissCount > 1 ? 's' : '') . '</p>'
                                        : '' ?>
                                    <?= $justifiedMiss > 0
                                        ? '<p>' . $justifiedMiss
                                        . ' absence' . ($justifiedMiss > 1 ? 's' : '')
                                        . ' justifiée' . ($justifiedMiss > 1 ? 's' : '') . '</p>'
                                        : '' ?>
                                </div>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
            </div>
            <div id="table-wrapper" class="col l9 xl10 no-padding">
                <table class="striped">
                    <thead id="absences-table-head">
                        <tr>
                            <?php
                            $monthes = array('Janvier', 'Février',
                                'Mars', 'Avril', 'Mai', 'Juin', 'Juillet',
                                'Août', 'Septembre', 'Octobre',
                                'Novembre', 'Décembre'
                            );

                            $curr_date = clone $data['beginDate'];
                            $last_month = null;
                            for ($i = 0; $i <= $data['dayNumber']; $i++) {
                                $curr_month = $curr_date->format('n');
                                if ($last_month !== $curr_month) {
                                    $colspan = days_in_month($curr_date->format('n'), $curr_date->format('Y'));
                                    ?>
                                    <td colspan="<?= $colspan ?>"><?= $monthes[$curr_month - 1] ?></td>
                                    <?php
                                    $last_month = $curr_month;
                                }

                                $curr_date->modify('+1 day');
                            }

                            unset($curr_month);
                            unset($last_month);
                            unset($monthes);
                            ?>
                        </tr>
                        <tr>
                            <?php
                            // table head
                            $curr_date = clone $data['beginDate'];
                            $now = new DateTime();

                            $last_month = null;
                            for ($i = 0; $i <= $data['dayNumber']; $i++) {
                                $activeDay = $curr_date->format('Y-m-d') == $now->format('Y-m-d')
                                    ? ' id="active-day"' : ''
                                ?>
                                <td <?= $activeDay ?>><?= $curr_date->format('j') ?></td>
                                <?php
                                $curr_date->modify('+1 day');
                            }
                            unset($last_month);
                            ?>
                        </tr>
                        <tr>
                            <?php
                            $days = array('D', 'L', 'M', 'M', 'J', 'V', 'S');
                            $curr_date = $data['beginDate']->format('w');

                            for ($i = 0; $i <= $data['dayNumber']; $i++) {
                                ?>
                                <td><?= $days[$curr_date] ?></td>
                                <?php
                                $curr_date = ($curr_date + 1) % 7;
                            }

                            unset($days);
                            unset($curr_date);
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // table content
                        $last_group = null;
                        foreach ($data['absences'] as $student) {
                            $groupChange = '';
                            if ($last_group !== $student->groupName) {
                                if (!is_null($last_group)) {
                                    $groupChange = 'class="group-change"';
                                }
                                $last_group = $student->groupName;
                            }
                            ?>
                            <tr <?= $groupChange ?>>
                                <?php
                                for ($i = 0; $i <= $data['dayNumber']; $i++) {
                                    $infos = array();
                                    $classes = array();
                                    $eventClass = '';

                                    if (isset($student->absences[$i])) {
                                        $justified = 0;

                                        foreach ($student->absences[$i] as $curr_absence) {
                                            $absenceClass = 'abs-' . strtolower($curr_absence->absenceTypeName);
                                            $eventClass = $eventClass === ''
                                                ? $absenceClass
                                                : ($eventClass !== $absenceClass
                                                    ? 'abs-several'
                                                    : $eventClass);

                                            if ($curr_absence->justified) {
                                                $justified += 1;
                                            }

                                            $curr_infos['absenceId'] = $curr_absence->idAbsence;
                                            $curr_infos['timePeriod'] = substr($curr_absence->beginDate, -8, 5)
                                                . ' - '
                                                . substr($curr_absence->endDate, -8, 5);
                                            $curr_infos['justify'] = $curr_absence->justified ? 'Oui' : 'Non';
                                            $curr_infos['absenceType'] = $curr_absence->absenceTypeName;
                                            $infos[] = $curr_infos;
                                        }

                                        // td has absences
                                        $classes[] = 'abs';
                                        $classes[] = $eventClass;
                                        if ($justified === count($student->absences[$i])) {
                                            $classes[] = 'abs-justifiee';
                                        }
                                    }

                                    ?>
                                    <td <?= (!empty($classes) ? ' class="' . join(' ', $classes) . '"' : '') ?>>
                                        <?php
                                        if (!empty($infos)) {
                                            foreach ($infos as $info) {
                                                ?>
                                                <div id="absn<?= $info['absenceId'] ?>"
                                                     class="<?= 'abs-' . strtolower($info['absenceType']) ?>">
                                                    <p>Horaires : <?= $info['timePeriod'] ?></p>
                                                    <p>Justifiée : <?= $info['justify'] ?>  </p>
                                                    <p><?= $info['absenceType'] ?></p>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                    <?php
                                } // for each day ?>
                            </tr>
                            <?php
                        } // for each student ?>
                    </tbody>
                </table>
                <table id="header-fixed"></table>
            </div>
            <div id="edition" class="modal">
                <div class="modal-content">
                    <div>
                        <h4 id="edition-name">Text nom</h4>
                        <h5 id="edition-date">Text date</h5>
                    </div>
                    <div class="row">
                        <section id="edition-morning" class="col s12 m10 l6">
                            <div class="header col l10 offset-l1">
                                <h4>Matinée</h4>
                                <i id="am-delete" class="material-icons scale-transition scale-out">delete</i>
                            </div>
                            <div class="col l10 offset-l1">
                                <div id="am-time" class="center-block">
                                    <p>08:00 - 12:00</p>
                                    <p>08:00 - 10:00</p>
                                    <p>10:00 - 12:00</p>
                                </div>
                                <div class="input-field">
                                    <select id="am-absenceType">
                                        <option value="0" disabled selected>Selectionner...</option>
                                        <?php
                                        foreach ($data['absenceTypes'] as $option) {
                                            echo '<option value="' . $option->idAbsenceType . '">'
                                                . $option->absenceTypeName
                                                . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label for="am-absenceType">Type d'absence</label>
                                </div>
                                <div>
                                    <input type="checkbox" id="am-justified">
                                    <label for="am-justified">Justifiée</label>
                                </div>
                            </div>
                        </section>
                        <section id="edition-afternoon" class="col s12 m10 l6">
                            <div class="header col l10 offset-l1">
                                <h4>Après-midi</h4>
                                <i id="pm-delete" class="material-icons scale-transition scale-out">delete</i>
                            </div>
                            <div class="col l10 offset-l1">
                                <div id="pm-time" class="center-block">
                                    <p></p>
                                    <p></p>
                                    <p></p>
                                </div>
                                <div class="input-field">
                                    <select id="pm-absenceType">
                                        <option value="0" disabled selected>Selectionner...</option>
                                        <?php
                                        foreach ($data['absenceTypes'] as $option) {
                                            echo '<option value="' . $option->idAbsenceType . '">'
                                                . $option->absenceTypeName
                                                . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <label for="pm-absenceType">Type d'absence</label>
                                </div>
                                <div>
                                    <input type="checkbox" id="pm-justified">
                                    <label for="pm-justified">Justifiée</label>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="edition-submit" class="btn">Enregistrer</button>
                    <a class="btn modal-action modal-close">Annuler</a>
                </div>
            </div>
        </div>
        <?php
    } else {
        ?>
        <h5 class="section center-align">Pas de semestre en cours</h5>
        <?php
    }
    ?>
</main>
<script>
    <?php
    if ($data['loaded']) {
        ?>
        // Needed for script
        var FIRST_DATE = new Date('<?= $data['beginDate']->format('Y-m-d') ?>');
        <?php
    } ?>
</script>
