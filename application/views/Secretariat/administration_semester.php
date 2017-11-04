<main class="container">
    <!-- TODO Ajouter un lien pour revenir en arrière (page Administration) -->
    <h4>
        Gestion du semestre: <?= $data['semester']->courseType
        . ' - ' . $data['semester']->schoolYear
        . ' ' . ($data['semester']->delayed ? ' différé' : '') ?>
    </h4>
    <section>
        <a href="<?= base_url('Process_Semester/delete/' . $data['semester']->idSemester) ?>">Supprimer ce semestre</a>

        <?php if ($groupCount = count($data['groups'])) { ?>
            <form action="<?= base_url('Process_Group/add_student/') . $data['semester']->idSemester ?>"
                  method="post">
                <table>
                    <thead>
                        <tr>
                            <?php
                            $maxStudents = 0;
                            foreach ($data['groups'] as $group) {
                                ?>
                                <th colspan="3"><?= $group->groupName ?></th>
                                <?php
                                if (($studentCount = count($group->students)) > $maxStudents) {
                                    $maxStudents = $studentCount;
                                }
                            } ?>
                        </tr>
                        <tr>
                            <?php
                            for ($i = 0; $i < $groupCount; $i++) {
                                ?>
                                <th>N°Etudiant</th>
                                <th>Nom</th>
                                <th>Prenom</th>
                                <?php
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($i = 0; $i < $maxStudents; $i++): ?>
                        <tr>
                            <?php foreach ($data['groups'] as $group) {
                                if (isset($group->students[$i])) {
                                    ?>
                                    <td>
                                        <a class="deleter"
                                           href="<?= base_url('Process_Group/delete_student'
                                               . '/' . $group->idGroup
                                               . '/' . $group->students[$i]->idStudent
                                               . '/' . $data['semester']->idSemester) ?>">
                                            <i class="material-icons">delete</i>
                                        </a>
                                        <?= $group->students[$i]->idStudent ?>
                                    </td>
                                    <td>
                                        <?= $group->students[$i]->surname ?>
                                    </td>
                                    <td>
                                        <?= $group->students[$i]->name ?>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td colspan="3"></td>
                                    <?php
                                }

                            } ?>
                        </tr>
                        <?php endfor; ?>
                        <tr>
                            <?php foreach ($data['groups'] as $group) { ?>
                                <td colspan="2">
                                    <div class="input-field">
                                        <!-- TODO id -> data-group-id -->
                                        <select id="group<?= $group->idGroup ?>" name="group<?= $group->idGroup ?>">
                                            <optgroup label="Sans groupe">
                                                <?php
                                                foreach ($data['freeStudents'] as $student) { ?>
                                                    <option value="<?= $student->idStudent ?>"
                                                        ><?= $student->name . ' ' . $student->surname ?>
                                                    </option>
                                                    <?php
                                                } ?>
                                            </optgroup>
                                            <?php
                                            foreach ($data['groups'] as $otherGroup):
                                                if ($otherGroup->idGroup === $group->idGroup) {
                                                    continue;
                                                }
                                                ?>
                                                <optgroup label="<?= $otherGroup->groupName ?>">
                                                    <?php
                                                    foreach ($otherGroup->students as $student) { ?>
                                                        <option value="<?= $student->idStudent ?>"
                                                            ><?= $student->name . ' ' . $student->surname?>
                                                        </option>
                                                        <?php
                                                    } ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="group<?= $group->idGroup ?>">Ajout étudiant :</label>
                                    </div>
                                </td>
                                <td>
                                    <button type="submit" name="submit" class="btn-flat" value="<?= $group->idGroup ?>">
                                        Ajouter
                                    </button>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <?php foreach ($data['groups'] as $group): ?>
                                <td colspan="3">
                                    <a href="<?= base_url('Process_Group/delete'
                                                . '/' . $group->idGroup
                                                . '/' . $data['semester']->idSemester) ?>">
                                        Supprimer ce groupe
                                    </a>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </form>
        <?php } ?>
    </section>
    <section>
        <h2>Actions Groupe</h2>
        <ul>
            <li>
                <a href="<?= base_url('Process_Administration/getSemesterCSV'
                    . '/' . $data['semester']->idSemester) ?>">
                    Exporter groupes de ce semestre
                </a>
            </li>
            <li>
                <form action="<?= base_url('Process_Administration/importCSV/' . $data['semester']->idSemester) ?>"
                      method="post" enctype="multipart/form-data">
                    <input type="hidden" name="MAX_FILE_SIZE" value="30000"/>
                    <input type="file" name="import" value="">
                    <button type="submit">Importer</button>
                </form>
            </li>
            <li>
                <form action="<?= base_url('Process_Group/add/' . $data['semester']->idSemester) ?>"
                      method="post">
                    <label for="groupeName">Nom du groupe : </label>
                    <input type="text" name="groupName" id="groupName">
                    <button type="submit">Ajouter Groupe</button>
                </form>
            </li>
        </ul>

    </section>
    <section>
        <h2>Attribution professeurs a un couple Groupe-Matiere</h2>
        <p>Ici ajout manuel</p>
        <p>Ici export csv pour un smestre</p>
        <p>Ici import d'un csv</p>
    </section>

</main>
