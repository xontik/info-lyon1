<main class="row section">
    <div class="section col s12 m6 offset-m1 l6 offset-l2">
        <?php
        if (!is_null($data['students'])) {
            ?>
            <div class="row small-caps">
                <div class="col s8"><h5>élève</h5></div>
                <div class="col s3 center-align"><h5>présent</h5></div>
            </div>
            <?php
            foreach ($data['students'] as $student) {
                ?>
                <div class="row">
                    <div class="col s8"><?= $student->surname . ' ' . $student->name ?></div>
                    <div class="col s3 center-align">
                        <input type="checkbox" class="filled-in" name="present" id="present" checked>
                        <label for="present"></label>
                    </div>
                </div>
                <?php
            }
        } ?>
    </div>
    <div class="col s12 m5 l4">
        <?= $data['side-timetable'] ?>
    </div>
</main>
