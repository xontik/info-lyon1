<main class="container">


    <div class="card grey lighten-5">
        <div class="card-content">
            <span class="card-title">Gestion du semestre: <?= $data['semester']->courseType
                . ' - ' . $data['semester']->schoolYear
                . ' ' . ($data['semester']->delayed ? ' différé' : '') ?></span>
        <?php if ($groupCount = count($data['groups'])) { ?>
            <form action="<?= base_url('Process_Group/add_student/') . $data['semester']->idSemester ?>"
                  method="post">
                <table class="centered">
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
                                                        ><?= $student->idStudent . ' ' .$student->name . ' ' . $student->surname ?>
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
                                                            ><?= $student->idStudent . ' ' . $student->name . ' ' . $student->surname?>
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
                                    <a class="btn waves-effect" href="<?= base_url('Process_Group/delete'
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
        </div>
        <div class="card-action">
            <a href="<?= base_url('Administration') ?>" class="btn-flat waves-effect">Retour</a>
            <a href="<?= base_url('Process_Semester/delete/'
                . $data['semester']->idSemester) ?>" class="btn-flat waves-effect">Supprimer ce semestre</a>
            <a href="<?= base_url('Process_Administration/getSemesterCSV'
                . '/' . $data['semester']->idSemester) ?>" class="btn-flat waves-effect">
                Exporter groupes de ce semestre
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col s6">
            <div class="card grey lighten-5">
                <form action="<?= base_url('Process_Administration/importCSV/' . $data['semester']->idSemester) ?>"
                    method="post" enctype="multipart/form-data">
                    <div class="card-content">
                        <span class="card-title" >Importer un fichier .csv de groupe </span>
                        <div class="file-field input-field">
                            <div class="btn waves-effects">
                                <span>Fichier</span>
                                <input type="file" name="import" value="">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text">
                            </div>
                        </div>
                    </div>
                    <div class="card-action">
                        <button class="btn-flat waves-effect" type="submit">Importer</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col s6">
            <div class="card grey lighten-5">
                <form action="<?= base_url('Process_Group/add/' . $data['semester']->idSemester) ?>"
                      method="post">
                      <div class="card-content">
                          <span class="card-title" >Ajouter un groupe </span>
                          <div class="input-field">
                              <input type="text" name="groupName" id="groupName">
                              <label for="groupeName">Nom du groupe : </label>
                          </div>
                      </div>
                      <div class="card-action">
                          <button class="btn-flat waves-effect" type="submit">Ajouter</button>
                      </div>
                </form>
            </div>
        </div>
    </div>






    <section>
        <h2>Attribution professeurs a un couple Groupe-Matiere</h2>
        <p>Ici ajout manuel</p>
        <p>Ici export csv pour un smestre</p>
        <p>Ici import d'un csv</p>
    </section>

</main>
