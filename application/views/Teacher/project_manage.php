<main class="container">
    <div class="card grey lighten-5">
        <div class="card-content">
            <span class="card-title">Gestion du projet</span>
            <form class="" action="#!" method="post">
                <div class="row">
                    <div class="input-field col s12 m9">
                        <input type="text" name="projectName" id="projectName" value="<?= $data['project']->projectName ?>" >
                        <label for="projectName">Nom du projet</label>
                    </div>
                    <div class="input-field col s12 m3">
                        <button class="btn" type="submit" name="send">Enregistrer</button>
                    </div>
                </div>
            </form>
            <div class="input-field with-autocomplete">
                <input type="text" id="student" class="autocomplete" placeholder="Rechercher un élève....">
                <label for="student">Etudiant à ajouter</label>
            </div>
            <?php // ici autocompletion ?>
            <ul class="collection with-header" id='student-list' data-project-id="<?= $data['project']->idProject ?>">
                <li class="collection-header"><h5>Liste des élèves</h5></li>
                <?php if (!empty($data['members'])) { ?>
                    <?php foreach ($data['members'] as $member): ?>
                        <li class="collection-item">
                            <div><?= $member->name ?>
                                <span class="secondary-content">
                                    <a href="#!"><i class="material-icons">delete</i></a>
                                </span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php } else { ?>
                    <li class="collection-item">Aucun élève dans ce projet</li>
                <?php } ?>
            </ul>


        </div>

    </div>
</main>
