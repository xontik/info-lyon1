<main class="container">
    <div class="section">
        <h4 class="header">Gestion des parcours</h4>
        <?php
        // Are there modifiable parcours
        if (count($data['parcours'])):
            ?>
            <div class="card grey lighten-5">
                <div class="card-content">
                    <span class="card-title">Relations Parcours/UE</span>

                    <form id="delete" action="<?= base_url('Process_Parcours/delete') ?>" method="POST">
                        <div class="input-field row">
                            <select class="col s12 m8 l5" id="parcours" name="parcoursId">
                                <?php
                                foreach ($data['parcours'] as $parcours) {
                                    ?>
                                    <option value="<?= $parcours->idParcours ?>"
                                        ><?= $parcours->type . ' démarrant en ' . $parcours->anneeCreation ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                            <label for="parcours">Parcours à modifier</label>
                        </div>
                        <div class="row">
                            <ul id="UEin" class="collection with-header col s12 l10 offset-l1 no-padding">
                                <li class="collection-header"><h5>UE lié au module</h5></li>
                            </ul>
                            <div class="col s12 m2 l12 center-align">
                                <button type="button" class="btn" name="add" id="add">
                                    <i class="material-icons">keyboard_arrow_up</i>
                                </button>
                                <button type="button" class="btn" name="remove" id="remove">
                                    <i class="material-icons">keyboard_arrow_down</i>
                                </button>
                            </div>
                            <ul id="UEout" class="collection with-header col s12 l10 offset-l1 no-padding">
                                <li class="collection-header"><h5>UEs disponibles</h5></li>
                            </ul>
                        </div>
                        <div class="btn-footer">
                            <button class="btn waves-effect waves-red" type="submit" name="suppr">Supprimer ce parcours</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        <div class="card grey lighten-5">
            <div class="card-content">
                <span class="card-title">Ajouter un parcours</span>
                <form action="<?= base_url('Process_Parcours/add') ?>" method="post">
                    <div class="row">
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
                        <div class="col s12 l2 push-l4 right-align">
                            <button class="btn" type="submit" name="addParcours">Ajouter</button>
                        </div>
                    </div>
                    <div class="btn-footer">
                    </div>
                </form>
            </div>
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
                        foreach ($data['semestres'] as $semestre) {
                            $sem = $semestre['data'];
                            ?>
                            <tr class="<?= $semestre['etat'] ?>">
                                <td><?= $sem->anneeScolaire ?></td>
                                <td><?= $sem->type . ' - ' . ($sem->differe ? 'Différé' : 'Normal') ?></td>
                                <td>
                                    <?php
                                    if (count($semestre['groups']) > 0) {
                                        foreach ($semestre['groups'] as $key => $group) {
                                            echo (($key > 0) ? ' - ' : '') . $group['nomGroupe'];
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if ($semestre['etat'] != 'after') {
                                        ?>
                                        <a href="<?= base_url('Administration/Semestre/' . $sem->idSemestre) ?>">
                                            <i class="material-icons">edit</i>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card grey lighten-5">
            <div class="card-content">
                <span class="card-title">Création</span>
                <form action="<?= base_url('Process_Semestre/add') ?>" method="post">
                    <div class="row">
                        <div class="input-field col s12 m6 l5">
                            <select id="parcours" name="parcoursId">
                                <?php foreach ($data['parcoursForSemester'] as $parcours): ?>
                                    <option value="<?= $parcours->idParcours ?>"
                                        ><?= $parcours->type ?> - PPN <?= $parcours->anneeCreation ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <label for="parcours">Parcours</label>
                        </div>
                        <div class="input-field col s12 m6 l5 offset-l1">
                            <select id="anneeScolaire" name="anneeScolaire">
                                <?php for ($i = 0; $i < 3; $i++) :
                                    $year = (int)(date('Y')); ?>
                                    <option value="<?= $year + $i ?>"><?= ($year + $i) . '-' . ($year + $i + 1) ?></option>
                                <?php endfor; ?>
                            </select>
                            <label for="anneeScolaire">Année scolaire</label>
                        </div>
                    </div>
                    <p>
                        <input type="checkbox" name="differe" id="differe">
                        <label for="differe">Differé</label>
                    </p>
                    <!-- TODO l'année en fonction du select #AJAX -->
                    <div class="btn-footer">
                        <button type="submit" name="addSemester" class="btn waves-effect">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
