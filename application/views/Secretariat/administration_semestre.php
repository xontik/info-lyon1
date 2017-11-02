<main class="container">
    <!-- TODO Ajouter un lien pour revenir en arrière (page Administration) -->
    <section>
        <h2>
            Gestion du semestre:
            <?= $data['semestre']->type
            . ' - ' . $data['semestre']->anneeScolaire
            . ' ' . ($data['semestre']->differe == 1 ? ' différé' : '') ?>
        </h2>
        <a href="<?= base_url('Process_Semestre/delete/' . $data['semestre']->idSemestre) ?>">Supprimer ce semestre</a>

        <?php if (count($data['groups']) > 0) { ?>
            <form action="<?= base_url('Process_Group/add_student/') . $data['semestre']->idSemestre ?>"
                  method="post">
                <table>
                    <thead>
                        <tr>
                            <?php
                            $maxstudents = 0;
                            foreach ($data['groups'] as $group) {
                                echo '<th colspan="3">' . $group['nomGroupe'] . '</th>';
                                if (count($group['students']) > $maxstudents) {
                                    $maxstudents = count($group['students']);
                                }
                            } ?>
                        </tr>
                        <tr>
                            <?php
                            for ($i = 0; $i < count($data['groups']); $i++) {
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
                    for ($i = 0; $i < $maxstudents; $i++):?>
                        <tr>
                            <?php foreach ($data['groups'] as $group) {
                                if (isset($group['students'][$i]['numEtudiant'])) {
                                    ?>
                                    <td>
                                        <a class="deleter"
                                           href="<?= base_url('Process_Group/delete_student'
                                               . '/' . $group['idGroupe']
                                               . '/' . $group['students'][$i]['numEtudiant']
                                               . '/' . $data['semestre']->idSemestre) ?>">
                                            <i class="material-icons">delete</i>
                                        </a>
                                        <?= $group['students'][$i]['numEtudiant'] ?>
                                    </td>
                                    <td>
                                        <?= $group['students'][$i]['nom'] ?>
                                    </td>
                                    <td>
                                        <?= $group['students'][$i]['prenom'] ?>
                                    </td>
                                <?php
                                } else {
                                    ?>
                                    <td></td>
                                    <td></td>
                                    <td></td>
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
                                        <select id="grp<?= $group['idGroupe'] ?>" name="grp<?= $group['idGroupe'] ?>">
                                            <optgroup label="Sans groupe">
                                                <?php foreach ($data['freeStudents'] as $student): ?>
                                                    <option value="<?= $student->numEtudiant ?>"><?= $student->nom . ' ' . $student->prenom ?></option>
                                                <?php endforeach; ?>
                                                <?php foreach ($data['groups'] as $grp):
                                                    if ($grp['idGroupe'] == $group['idGroupe']) {
                                                        continue;
                                                    } ?>
                                                    <optgroup label="<?= $grp['nomGroupe'] ?>">
                                                        <?php foreach ($grp['students'] as $student): ?>

                                                            <option value="<?= $student['numEtudiant'] ?>"><?= $student['nom'] . ' ' . $student['prenom'] ?></option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        </select>
                                        <label for="grp<?= $group['idGroupe'] ?>">Ajout etudiant :</label>
                                    </div>
                                </td>
                                <td>
                                    <button type="submit" name="submit" class="btn-flat" value="<?= $group['idGroupe'] ?>">
                                        Ajouter
                                    </button>
                                </td>
                            <?php } ?>
                        </tr>
                        <tr>
                            <?php foreach ($data['groups'] as $group): ?>
                                <td colspan="3">
                                    <a href="<?= base_url('Process_Group/delete'
                                                . '/' . $group['idGroupe']
                                                . '/' . $data['semestre']->idSemestre) ?>">
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
                <a href="<?= base_url('Process_Administration/getCSVGroupeSemestre'
                    . '/' . $data['semestre']->idSemestre) ?>">
                    Exporter groupes de ce semestre
                </a>
            </li>
            <li>
                <form action="<?= base_url('Process_Administration/importCSV/' . $data['semestre']->idSemestre) ?>"
                      method="post" enctype="multipart/form-data">
                    <input type="hidden" name="MAX_FILE_SIZE" value="30000"/>
                    <input type="file" name="import" value="">
                    <button type="submit" name="importSem">Importer</button>
                </form>
            </li>
            <li>
                <form action="<?= base_url('Process_Group/add/' . $data['semestre']->idSemestre) ?>"
                      method="post">
                    <label for="nomGroupe">Nom du groupe : </label>
                    <input type="text" name="nomGroupe" id="nomGroupe">
                    <button type="submit" name="addGroupe">Ajouter Groupe</button>
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
