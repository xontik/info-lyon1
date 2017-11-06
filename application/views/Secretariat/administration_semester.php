<main class="container">
    <div class="header row valign-wrapper">
        <h4 class="header col">Gestion du semestre <?= $data['semester']->courseType
            . ' - ' . $data['semester']->schoolYear
            . ' ' . ($data['semester']->delayed ? ' différé' : '') ?></h4>
        <a href="<?= base_url('Administration') ?>" class="btn-flat waves-effect col">Retour</a>
    </div>

    <div class="card grey lighten-5">
        <div class="card-content">
            <span class="card-title">Groupes</span>
            <?php if ($groupCount = count($data['groups'])) {
                ?>
                <form action="<?= base_url('Process_Group/add_student/') . $data['semester']->idSemester ?>"
                      method="post">
                    <div class="wrapper">
                        <?php
                        foreach ($data['groups'] as $group) {
                            ?>
                            <div>
                                <h5><?= $group->groupName ?></h5>
                                <table class="centered striped">
                                    <thead>
                                        <tr>
                                            <th colspan="2">N°Etudiant</th>
                                            <th>Nom</th>
                                            <th>Prenom</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($group->students as $student) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <a class="deleter"
                                                       href="<?= base_url('Process_Group/delete_student'
                                                           . '/' . $group->idGroup
                                                           . '/' . $student->idStudent
                                                           . '/' . $data['semester']->idSemester) ?>">
                                                        <i class="material-icons">delete</i>
                                                    </a>
                                                </td>
                                                <td><?= $student->idStudent ?></td>
                                                <td><?= $student->surname ?></td>
                                                <td><?= $student->name ?></td>
                                            </tr>
                                            <?php
                                        }

                                        for ($i = count($group->students); $i < $data['maxStudents']; $i++) {
                                            ?>
                                            <tr><td colspan="4"></td></tr>
                                            <?php
                                        } ?>
                                    </tbody>
                                </table>
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
                                    <label for="group<?= $group->idGroup ?>">Ajout étudiant</label>
                                </div>
                                <button type="submit" name="submit" class="btn-flat"
                                        value="<?= $group->idGroup ?>">
                                    Ajouter l'élève
                                </button>
                                <a class="btn waves-effect waves-red col s12"
                                   href="<?= base_url('Process_Group/delete'
                                    . '/' . $group->idGroup
                                    . '/' . $data['semester']->idSemester) ?>">
                                    Supprimer le groupe
                                </a>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </form>
            <?php } ?>
        </div>
        <div class="card-action">
            <a href="<?= base_url('Process_Administration/getSemesterCSV'
                . '/' . $data['semester']->idSemester) ?>" class="btn-flat waves-effect">
                Exporter les groupes de ce semestre
            </a>
            <a href="<?= base_url('Process_Semester/delete/'
                . $data['semester']->idSemester) ?>" class="btn-flat waves-effect">Supprimer ce semestre</a>
        </div>
    </div>
    <div class="row">
        <div class="col s6">
            <div class="card grey lighten-5">
                <form action="<?= base_url('Process_Administration/importCSVSemester/' . $data['semester']->idSemester) ?>"
                    method="post" enctype="multipart/form-data">
                    <div class="card-content">
                        <span class="card-title" >Importer un groupe</span>
                        <div class="file-field input-field">
                            <div class="btn waves-effects">
                                <span>Fichier</span>
                                <input type="file" name="import">
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
                              <label for="groupName">Nom du groupe</label>
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
