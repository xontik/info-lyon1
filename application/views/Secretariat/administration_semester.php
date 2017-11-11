<main class="container">


    <div id="group-semester" class="card grey lighten-5" data-semester-id="<?= $data['semester']->idSemester  ?>">
        <div class="card-content">
            <span class="card-title">Gestion du semestre: <?= $data['semester']->courseType
            . ' - ' . $data['semester']->schoolYear
            . ' ' . ($data['semester']->delayed ? ' différé' : '') ?>
            <?php if($data['deletable']){?>
                <a href="<?= base_url('Process_Semester/delete/'
                . $data['semester']->idSemester) ?>" class="right" data-confirm="Etês-vous sur de vouloir supprimer ce semestre ?" >
                <i class="material-icons small">delete</i>
            </a>
        <?php } ?>
    </span>
    <?php if ($groupCount = count($data['groups'])) { ?>
        <div class="horizontal-wrapper">
            <?php if($data['editable']){?>
                <ul class="collection with-header connectedSortable" data-group-id="0" >
                    <li class="collection-header" >
                        <div>
                            <h5>Elèves sans groupe</h5>
                        </div>
                    </li>
                    <?php if (count($data['freeStudents'])) { ?>
                        <?php foreach ($data['freeStudents'] as $student) { ?>
                            <li class="collection-item" data-group-id="0" data-student-id="<?= $student->idStudent ?>" >
                                <div>
                                    <?= $student->idStudent . ' ' . $student->surname . ' ' . $student->name ?>
                                </div>
                            </li>
                        <?php } ?>
                    <?php } else { ?>
                        <li class="collection-item no-student">Aucun élève</li>
                    <?php } ?>
                </ul>
            <?php } ?>
            <?php foreach ($data['groupsWithStudent'] as $group) { ?>

                <ul class="collection with-header <?= $data['editable'] ? 'connectedSortable' : ''  ?>"  data-group-id="<?= $group->idGroup ?>">
                    <li class="collection-header" >
                        <div>
                            <h5><?= $group->groupName ?>
                                <?php if ($data['deletable']) { ?>
                                    <a class="secondary-content" href="<?= base_url('Process_Group/delete'
                                    . '/' . $group->idGroup
                                    . '/' . $data['semester']->idSemester) ?>" data-confirm="Etês-vous sur de vouloir supprimer ce groupe ?" ><i class="material-icons small">delete</i></a>
                                <?php } ?>
                            </h5>
                        </div>
                    </li>
                    <?php if (count($group->students)) { ?>
                        <?php foreach ($group->students as $student) { ?>

                            <li class="collection-item" data-group-id="<?= $group->idGroup ?>" data-student-id="<?= $student->idStudent ?>" >
                                <div>
                                    <?php if ($data['editable']) { ?>
                                        <a href="<?= base_url('Process_Group/delete_student'
                                        . '/' . $group->idGroup
                                        . '/' . $student->idStudent
                                        . '/' . $data['semester']->idSemester) ?>"  ><i class="material-icons">delete</i></a>
                                    <?php } ?>

                                    <?= $student->idStudent . ' ' . $student->surname . ' ' . $student->name ?>
                                </div>
                            </li>
                        <?php } ?>
                    <?php } else { ?>
                        <li class="collection-item no-student">Aucun élève</li>
                    <?php } ?>
                </ul>
            <?php } ?>

        </div>
    <?php } ?>
</div>
<?php if($data['deletable']){?>
    <div class="card-action">
        <a href="<?= base_url('Process_Group/add/'
        . $data['semester']->idSemester) ?>" class="btn-flat waves-effect">Ajouter groupe</a>

    </div>
<?php } ?>
</div>

<?php if (count($data['subjects'])) { ?>
    <div class="card grey lighten-5">
        <div class="card-content">
            <span class="card-title">Tableau des affectations</span>
        </div>
        <div class="card-action row no-margin">
            <table id="association-group-teacher-subject" class="bordered col s12">
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
                        $subjectDescription = $subject->subjectCode . ' ' . ($subject->subjectName == "" ? $subject->moduleName : $subject->moduleName . ' : ' . $subject->subjectName);
                        ?>
                        <tr>
                            <td><?= $subjectDescription ?></td>
                            <?php foreach ($data['groupsWithStudent'] as $group) :
                                if(isset($data['educations'][$group->idGroup][$subject->idSubject])) {
                                    $education = $data['educations'][$group->idGroup][$subject->idSubject];
                                    ?>
                                    <td>
                                        <i class="small material-icons tooltipped" data-group-id="<?= $group->idGroup ?>"
                                            data-subject-id="<?= $subject->idSubject ?>" data-teacher-id="<?= $education->idTeacher ?>"
                                            data-tooltip="<?php echo $education->name . ' ' . $education->surname; ?>" data-delay="0">person</i>
                                        </td>
                                        <?php
                                    } else { ?>
                                        <td>
                                            <i class="small material-icons tooltipped" data-group-id="<?= $group->idGroup ?>"
                                                data-subject-id="<?= $subject->idSubject ?>" data-teacher-id="0"
                                                data-tooltip="Cliquer pour ajouter" data-delay="0">error_outline</i>
                                            </td>
                                        <?php }
                                    endforeach; ?>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
        <?php if ($data['editable']) { ?>

            <div id="assoctiationCard" class="card grey lighten-5">
                <form class="" action="<?= base_url('Process_Education/add_teacher/'.$data['semester']->idSemester) ?>" method="post">
                    <div class="card-content row no-margin">
                        <span class="card-title">Attribuer un professeur</span>
                        <div class="input-field col s12">
                            <select  id="subjectId" name="subjectId">
                                <option value="-1" disabled selected> Selectionner...</option>
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
                                <option value="-1" disabled selected
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
                            <option value="-1" disabled selected >Selectionner...</option>
                            <option value="0">Aucun</option>

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
                    <button id=''class="btn-flat waves-effect" type="submit">Editer</button>
                </div>

            </form>
        </div>
    <?php } ?>
    <?php if($data['editable']){?>

        <div class="row">
            <div class="col s12 l6">
                <div class="card grey lighten-5">
                    <form action="<?= base_url('Process_Administration/importGroups/' . $data['semester']->idSemester) ?>"
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
                            <a href="<?= base_url('Process_Administration/exportGroups'
                            . '/' . $data['semester']->idSemester) ?>" class="btn-flat waves-effect">
                            Exporter</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col s12 l6">
                <div class="card grey lighten-5">
                    <form action="<?= base_url('Process_Administration/importCSVSemester/' . $data['semester']->idSemester) ?>"
                        method="post" enctype="multipart/form-data">
                        <div class="card-content">
                            <span class="card-title" >Importer un fichier .csv de matieres </span>
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
                            Exporter</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <?php } ?>

</main>
