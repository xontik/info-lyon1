<main>
    <div class="container">
        <h4 class="header">
            Gestion du semestre: <?= $data['semester']->courseType
            . ' - ' . $data['semester']->schoolYear
            . ' ' . ($data['semester']->delayed ? ' différé' : '') ?>
            <?php
            if ($data['deletable']) {
                ?>
                <a href="<?= base_url('Process_Semester/delete/' . $data['semester']->idSemester) ?>"
                   class="right" data-confirm="Êtes-vous sûr de vouloir supprimer ce semestre ?">
                    <i class="material-icons small">delete</i>
                </a>
                <?php
            } ?>
        </h4>
        <div id="group-semester" class="card grey lighten-5" data-semester-id="<?= $data['semester']->idSemester  ?>">
            <div class="card-content">
                <span class="card-title">Groupes</span>
                <?php
                if ($groupCount = count($data['groups'])) {
                    ?>
                    <div class="horizontal-wrapper">
                        <?php
                        if ($data['editable']) {
                            ?>
                            <ul class="collection with-header connectedSortable" data-group-id="0">
                                <li class="collection-header" >
                                    <h5>Elèves sans groupe</h5>
                                </li>
                                <?php
                                if (count($data['freeStudents'])) {
                                    foreach ($data['freeStudents'] as $student) {
                                        ?>
                                        <li class="collection-item"
                                            data-group-id="0"
                                            data-student-id="<?= $student->idStudent ?>">
                                            <?= $student->idStudent . ' ' . $student->surname . ' ' . $student->name ?>
                                        </li>
                                        <?php
                                    }
                                } else {
                                    ?><li class="collection-item no-student">Aucun élève</li>
                                    <?php
                                } ?>
                            </ul>
                            <?php
                        }

                        foreach ($data['groupsWithStudent'] as $group) {
                            ?>
                            <ul class="collection with-header <?= $data['editable'] ? 'connectedSortable' : ''  ?>"
                                data-group-id="<?= $group->idGroup ?>">
                                <li class="collection-header" >
                                    <h5><?= $group->groupName ?>
                                        <?php
                                        if ($data['deletable']) { ?>
                                            <a class="secondary-content" href="<?= base_url('Process_Group/delete'
                                                . '/' . $group->idGroup
                                                . '/' . $data['semester']->idSemester) ?>"
                                               data-confirm="Êtes-vous sûr de vouloir supprimer ce groupe ?">
                                                <i class="material-icons small">delete</i>
                                            </a>
                                            <?php
                                        } ?>
                                    </h5>
                                </li>
                                <?php
                                if (count($group->students)) {
                                    foreach ($group->students as $student) { ?>
                                        <li class="collection-item"
                                            data-group-id="<?= $group->idGroup ?>"
                                            data-student-id="<?= $student->idStudent ?>">
                                            <div>
                                                <?php
                                                if ($data['editable']) { ?>
                                                    <a href="<?= base_url('Process_Group/delete_student'
                                                    . '/' . $group->idGroup
                                                    . '/' . $student->idStudent
                                                    . '/' . $data['semester']->idSemester) ?>">
                                                        <i class="material-icons">delete</i>
                                                    </a>
                                                    <?php
                                                } ?>
                                                <?= $student->idStudent . ' ' . $student->surname . ' ' . $student->name ?>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                } else { ?>
                                    <li class="collection-item no-student">Aucun élève</li>
                                    <?php
                                } ?>
                            </ul>
                            <?php
                        } ?>
                    </div>
                    <?php
                } ?>
            </div>
            <?php
            if ($data['deletable']) { ?>
                <div class="card-action">
                    <a href="<?= base_url('Process_Group/add/' . $data['semester']->idSemester) ?>"
                       class="btn-flat waves-effect waves-light">Ajouter groupe</a>
                </div>
                <?php
            } ?>
        </div>
        <?php
        if ($data['editable']) { ?>
            <div class="row">
                <div class="col s12">
                    <div class="card grey lighten-5">
                        <form action="<?= base_url('Process_Administration/importGroups/' . $data['semester']->idSemester) ?>"
                            method="post" enctype="multipart/form-data">
                            <div class="card-content">
                                <span class="card-title" >Importer un fichier de groupe</span>
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
                                <button class="btn-flat waves-effect waves-light" type="submit">Importer</button>
                                <a href="<?= base_url('Process_Administration/exportGroups'
                                . '/' . $data['semester']->idSemester) ?>" class="btn-flat waves-effect waves-light">
                                Exporter</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
        } ?>
    </div>

    <?php
    if (count($data['subjects'])) {
        ?>
        <div class="card grey lighten-5">
            <div id="education-wrapper" class="card-content row collection">
                <?php
                if ($data['editable']) {
                    ?>
                    <div class="col l3">
                        <span class="card-title">Professeurs</span>
                        <ul id="teachers" class="collapsible"></ul>
                    </div>
                    <?php
                } ?>
                <div <?= $data['editable'] ? 'class="col l9"' : '' ?>>
                    <span class="card-title">Tableau des affectations</span>
                    <table id="education-association" class="bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <?php
                                if (!empty($data['groupsWithStudent'])) {
                                    foreach ($data['groupsWithStudent'] as $group) { ?>
                                        <th><?= $group->groupName ?></th>
                                        <?php
                                    }
                                    ?>
                                    <th>Tous</th>
                                    <?php
                                } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($data['subjects'] as $subject) {
                                $subjectDescription = $subject->subjectCode
                                    . ' ' . $subject->moduleName
                                    . ($subject->subjectName !== ''
                                        ? ' : ' . $subject->subjectName : '');
                                ?>
                                <tr data-subject-id="<?= $subject->idSubject ?>">
                                    <td><?= $subjectDescription ?></td>
                                    <?php
                                    if (!empty($data['groupsWithStudent'])) {
                                        foreach ($data['groupsWithStudent'] as $group) {
                                            if (isset($data['educations'][$group->idGroup][$subject->idSubject])) {
                                                $education = $data['educations'][$group->idGroup][$subject->idSubject];
                                                $tooltip = $education->name . ' ' . $education->surname;
                                                $icon = 'person';
                                            } else {
                                                $education = new stdClass;
                                                $education->idTeacher = 0;
                                                $tooltip = 'Assigner au ' . $group->groupName;
                                                $icon = 'error_outline';
                                            } ?>
                                            <td data-group-id="<?= $group->idGroup ?>"
                                                data-subject-id="<?= $subject->idSubject ?>"
                                                data-teacher-id="<?= $education->idTeacher ?>">
                                                <i class="small material-icons tooltipped"
                                                   data-tooltip="<?= $tooltip ?>"
                                                   data-delay="0"><?= $icon ?></i>
                                            </td>
                                            <?php
                                        }
                                        ?>
                                        <td data-group-id="all"
                                            data-subject-id="<?= $subject->idSubject ?>">
                                            <i class="small material-icons tooltipped"
                                               data-tooltip="Assigner à tous les groupes"
                                               data-delay="0">select_all</i>
                                        </td>
                                        <?php
                                    } ?>
                                </tr>
                                <?php
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    } ?>
</main>
