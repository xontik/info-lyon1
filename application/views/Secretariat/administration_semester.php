<main class="container">


    <div class="card grey lighten-5">
        <div class="card-content">
            <span class="card-title">Gestion du semestre: <?= $data['semester']->courseType
                . ' - ' . $data['semester']->schoolYear
                . ' ' . ($data['semester']->delayed ? ' différé' : '') ?></span>
        <?php if ($groupCount = count($data['groups'])) { ?>
            <form action="<?= base_url('Process_Group/add_student/') . $data['semester']->idSemester ?>"
                  method="post">
                <table class="table-scrollable">
                    <thead>
                      <tr>
                          <?php
                          $maxStudents = 0;
                          foreach ($data['groupsWithStudent'] as $group) {
                              ?>
                              <th class="center-align"><?= $group->groupName ?></th>
                              <?php
                              if (($studentCount = count($group->students)) > $maxStudents) {
                                  $maxStudents = $studentCount;
                              }
                          } ?>
                      </tr>
                    </thead>
                    <tbody>
                    <?php
                    for ($i = 0; $i < $maxStudents; $i++): ?>
                      <tr>
                          <?php foreach ($data['groupsWithStudent'] as $group) {
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
                                      <?= $group->students[$i]->surname ?>
                                      <?= $group->students[$i]->name ?>
                                  </td>
                                  <?php
                              } else {
                                  ?>
                                  <td></td>
                                  <?php
                              }

                          } ?>
                      </tr>
                      <?php endfor; ?>
                      <tr>
                          <?php foreach ($data['groupsWithStudent'] as $group) { ?>
                              <td class="center-align">
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
                                  <div class="input-field">
                                      <button type="submit" name="submit" class="btn-flat" value="<?= $group->idGroup ?>">
                                          Ajouter
                                      </button>
                                  </div>

                              </td>
                          <?php } ?>
                      </tr>
                      <tr>
                          <?php foreach ($data['groups'] as $group): ?>
                              <td>
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
        </div>
    </div>
    <div class="row">
        <div class="col s12 l6">
            <div class="card grey lighten-5">
                <form action="<?= base_url('Process_Administration/importCSVSemester/' . $data['semester']->idSemester) ?>"
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
                        <a href="<?= base_url('Process_Administration/getSemesterCSV'
                            . '/' . $data['semester']->idSemester) ?>" class="btn-flat waves-effect">
                            Exporter
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div class="col s12 l6">
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
    <div id="assoctiationCard" class="card grey lighten-5">
        <form class="" action="#" method="post">
            <div class="card-content row no-margin">
                <span class="card-title">Attribuer un professeur</span>
                <div class="input-field col s12">
                    <select  id="subjectId" name="subjectId">
                        <option value="" disabled selected
                            >Selectionner...
                        </option>
                        <?php
                        foreach ($data['subjects'] as $subject) {
                            $subjectDescription = $subject->subjectCode . ' - ' . ($subject->subjectName == "" ? $subject->moduleName : $subject->subjectName);
                            ?>
                            <option value="<?= $subject->idSubject ?>"
                                ><?= $subjectDescription ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                    <label for="subjectId">Matière</label>
                </div>
                <div class="input-field col s6">
                    <select id="groupId" name="groupId">
                        <option value="" disabled selected
                            >Selectionner...
                        </option>
                        <?php
                        foreach ($data['groups'] as $group) {
                            ?>
                            <option value="<?= $group->idGroup ?>"
                                ><?= $group->groupName ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                    <label for="groupId">Groupe</label>
                </div>

                <div class="input-field col s6">
                    <select  id="teacherId" name="teacherId">
                        <option value="" disabled selected
                            >Selectionner...
                        </option>
                        <?php
                        foreach ($data['teachers'] as $teacher) {
                            ?>
                            <option value="<?= $teacher->idTeacher ?>"
                                ><?= $teacher->name . ' ' . $teacher->surname ?>
                            </option>
                            <?php
                        }
                        ?>
                    </select>
                    <label for="teacherId">Professeur</label>
                </div>
            </div>
            <div class="card-action">
                <button class="btn-flat waves-effect" type="submit">Ajouter</button>
            </div>

        </form>
    </div>
    <div class="card grey lighten-5">
        <div class="card-content">
            <span class="card-title">Tableau des affectations</span>
        </div>
        <div class="card-action row no-margin">
            <table id="associationGroupTeacherSubject" class="bordered col s12">
                <thead>
                    <tr>
                        <th></th>
                        <?php foreach ($data['groupsWithStudent'] as $group) : ?>
                            <th><?= $group->groupName ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['subjects'] as $subject) :
                        $subjectDescription = $subject->subjectCode . ' ' . ($subject->subjectName == "" ? $subject->moduleName : $subject->subjectName);
                        ?>
                        <tr>
                            <td><?= $subjectDescription ?></td>
                            <?php foreach ($data['groupsWithStudent'] as $group) :
                                if(isset($data['educations'][$group->idGroup][$subject->idSubject])) {
                                ?>
                                    <td>
                                        <div class="tooltip">
                                            <i class="small material-icons" >person</i>
                                            <span class="tooltiptext">
                                                <?php $education = $data['educations'][$group->idGroup][$subject->idSubject];
                                                echo $education->name . ' ' . $education->surname; ?>
                                            </span>
                                        </div>
                                    </td>
                                <?php
                            } else { ?>
                                    <td>
                                        <div class="tooltip">
                                            <i class="small material-icons" data-group-id="<?= $group->idGroup ?>"
                                                data-subject-id="<?= $subject->idSubject ?>">error_outline</i>
                                                <span class="tooltiptext">
                                                    Cliquer pour ajouter
                                                </span>
                                        </div>

                                    </td>
                            <?php }
                            endforeach; ?>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <section>
        <h2>Attribution professeurs a un couple Groupe-Matiere</h2>
        <p>Ici ajout manuel</p>
        <p>Ici export csv pour un smestre</p>
        <p>Ici import d'un csv</p>
    </section>

</main>
