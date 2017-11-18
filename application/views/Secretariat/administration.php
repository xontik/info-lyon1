<main class="container">
    <div class="section">
        <h4 class="header">Gestion des parcours</h4>
        <?php
        // Are there modifiable parcours
        if (count($data['courses'])):
            ?>
            <div class="card grey lighten-5">
                <form id="delete" action="<?= base_url('Process_Course/delete') ?>" method="post">
                    <div class="card-content">
                        <span class="card-title">Relations Parcours/UE</span>
                        <div class="input-field row">
                            <select class="col s12 m8 l5" id="futureCourseId" name="courseId">
                                <?php
                                foreach ($data['courses'] as $course) {
                                    ?>
                                    <option value="<?= $course->idCourse ?>"
                                        ><?= $course->courseType . ' démarrant en ' . $course->creationYear ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                            <label for="futureCourseId">Parcours à modifier</label>
                        </div>
                        <div class="row">
                            <ul id="TUin" class="collection with-header col s12 l10 offset-l1 no-padding">
                                <li class="collection-header"><h5>UE lié au module</h5></li>
                            </ul>
                            <div class="col s12 center-align">
                                <button type="button" class="btn" id="add">
                                    <i class="material-icons">keyboard_arrow_up</i>
                                </button>
                                <button type="button" class="btn" id="remove">
                                    <i class="material-icons">keyboard_arrow_down</i>
                                </button>
                            </div>
                            <ul id="TUout" class="collection with-header col s12 l10 offset-l1 no-padding">
                                <li class="collection-header"><h5>UEs disponibles</h5></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-action">
                        <button type="submit" class="btn-flat waves-effect waves-red">Supprimer ce parcours</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        <div class="card grey lighten-5">
            <form action="<?= base_url('Process_Course/add') ?>" method="post">

                <div class="card-content">
                    <span class="card-title">Ajouter un parcours</span>
                    <div class="row no-margin">
                        <div class="input-field col s12 m6 l3">
                            <input type="number" name="year" id="year" min="<?= date('Y') + 1 ?>"
                                   value="<?= date('Y') + 1 ?>">
                            <label for="year">Année d'entrée en application</label>
                        </div>
                        <div class="input-field col s12 m6 l3">
                            <select id="type" name="type">
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                                <option value="S4">S4</option>
                            </select>
                            <label for="type">Type de semestre</label>
                        </div>
                    </div>
                </div>
                <div class="card-action">
                    <button class="btn-flat waves-effect waves-light" type="submit">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
    <div class="section">
        <h4 class="header">Gestion des semestres</h4>
        <div class="card grey lighten-5">
            <div class="card-content">
                <span class="card-title">Liste</span>
                <table id='tableSemestre'>
                    <thead>
                        <tr>
                            <th>Année scolaire</th>
                            <th>Type semestre</th>
                            <th>Groupes</th>
                            <th>Gérer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($data['semesters'] as $semester) {
                            $semesterData = $semester['data'];
                            ?>
                            <tr>
                                <td><?= $semesterData->schoolYear ?></td>
                                <td><?= $semesterData->courseType . ' - '
                                    . ($semesterData->delayed ? 'Différé' : 'Normal') ?></td>
                                <td>
                                    <?php
                                    if (count($semester['groups']) > 0) {
                                        foreach ($semester['groups'] as $key => $group) {
                                            echo (($key > 0) ? ' - ' : '') . $group['groupName'];
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="<?= base_url('Administration/semester/' . $semesterData->idSemester) ?>">
                                        <i class="material-icons"><?= $semester['state'] != 'after' ? 'edit' : 'add_to_queue' ?></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card grey lighten-5">
            <form action="<?= base_url('Process_Semester/add') ?>" method="post">
                <div class="card-content">
                    <span class="card-title">Création</span>
                    <div class="row">
                        <div class="input-field col s12 m6 l5">
                            <select id="courseId" name="courseId">
                                <?php foreach ($data['courseTypes'] as $course): ?>
                                    <option value="<?= $course->idCourse ?>"
                                        ><?= $course->courseType ?> - PPN <?= $course->creationYear ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="courseId">Parcours</label>
                        </div>
                        <div class="input-field col s12 m6 l5 offset-l1">
                            <select id="schoolYear" name="schoolYear">
                            </select>
                            <label for="schoolYear">Année scolaire</label>
                        </div>
                    </div>
                    <p>
                        <input type="checkbox" name="delayed" id="delayed">
                        <label for="delayed">Differé</label>
                    </p>
                </div>
                <div class="card-action">
                    <button type="submit" class="btn-flat waves-effect waves-light">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</main>
