<main class="row section">
    <div class="section col s12 m6 offset-m1 l6 offset-l2">
        <?php
        if (!is_null($data['lesson'])) { ?>
            <div class="row valign-wrapper">
                <h4 class="col"><?= $data['lesson']['name'] ?></h4>
                <h5 class="col"><?= $data['lesson']['groups'] ?>,</h5>
                <h5 id="time" class="col"><?= $data['lesson']['timeStart'] . ' - ' . $data['lesson']['timeEnd'] ?></h5>
            </div>
            <?php
            if (!is_null($data['students'])) {
                ?>
                <div class="row small-caps">
                    <div class="col s8"><h5>élève</h5></div>
                    <div class="col s3 center-align"><h5>présent</h5></div>
                </div>
                <div id="studentList">
                    <?php
                    foreach ($data['students'] as $student) {
                        ?>
                        <div class="row no-margin">
                            <div class="col s8"><?= $student->surname . ' ' . $student->name ?></div>
                            <div class="col s3 center-align">
                                <input type="checkbox" class="filled-in"
                                       id="stud<?= $student->idStudent ?>"
                                       data-student-id="<?= $student->idStudent ?>"
                                       <?php
                                       if (isset($student->absence)) { ?>
                                           data-absence-id="<?= $student->absence->idAbsence?>"
                                           <?php
                                       } else { ?>
                                           checked
                                           <?php
                                       } ?>>
                                <label for="stud<?= $student->idStudent ?>"></label>
                            </div>
                        </div>
                        <?php
                    } ?>
                </div>
                <?php
            } else { ?>
              <div>
                <h5>Aucun élève à afficher</h5>
              </div>
              <?php
            }
        } ?>
    </div>
    <div class="col s12 m5 l4">
        <?= $data['side-timetable'] ?>
    </div>
</main>
