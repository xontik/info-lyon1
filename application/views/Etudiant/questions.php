<main class="container">
    <div class="section">
        <div class="header">
            <h4>Les questions</h4>
        </div>
        <ul class="collapsible" data-collapsible="accordion">
            <?php foreach ($data['questions'] as $etuQuestion)
            { ?>
                <li>
                    <div class="collapsible-header">
                        <div><?= $etuQuestion->titre ?></div>
                        <div>
                            <?= $data['teachers'][$etuQuestion->idProfesseur]->prenom . ' ' .
                            $data['teachers'][$etuQuestion->idProfesseur]->nom
                            ?>
                        </div>
                    </div>
                    <div class="collapsible-body">
                        <p class="right-align"><?= $etuQuestion->texte ?></p>
                        <ul>
                            <?php
                            foreach ($data['answers'][$etuQuestion->idQuestion] as $reponse) {
                                ?>
                                <li class="divider"></li>
                                <li><p <?= !$reponse->prof ? 'class="right-align"' : '' ?>><?= $reponse->texte ?></p></li>
                                <?php
                            }
                            ?>
                        </ul>
                        <form action="/Process_etudiant/repondreQuestion" method="POST">
                            <input type="hidden" name="r_idQuestion" value ="<?= $etuQuestion->idQuestion ?>"/>
                            <div class="btn-footer">
                                <div class="input-field">
                                    <textarea class="materialize-textarea" name="r_texte" id="r_texte"></textarea>
                                    <label for="r_texte">Réponse</label>
                                </div>
                                <button type="submit" class="waves-effect waves-light btn">Répondre</button>
                            </div>
                        </form>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="section">
        <div class="header">
            <h4>Poser une question</h4>
        </div>
        <form action="/Process_etudiant/envoyerQuestion" method="POST">
            <div class="input-field">
                <input type="text" name="q_titre" id="q_titre" autocomplete="off" data-length="255"/>
                <label for="q_titre">Titre</label>
            </div>
            <div class="input-field col s12">
                <textarea class="materialize-textarea" name="q_texte" id="q_texte"></textarea>
                <label for="q_texte">Question</label>
            </div>
            <div class="input-field row">
                <select name="q_idProfesseur" id="q_idProfesseur" class="col s12 m8 l5">
                    <option value="null" disabled selected>Choisir un professeur</option>
                    <?php
                    foreach ($data['teachers'] as $teacher)
                    { ?>
                        <option value="<?= $teacher->idProfesseur ?>"><?= $teacher->prenom . ' ' . $teacher->nom ?></option>
                        <?php
                    } ?>
                </select>
                <label for="q_idProfesseur"></label>
            </div>
            <div class="btn-footer">
                <button type="submit" class="waves-effect waves-light btn">Envoyer</button>
            </div>
        </form>
    </div>
</main>
