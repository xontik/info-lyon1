<main class="container">
    <div class="section">
        <div class="header">
            <h4>Les questions</h4>
        </div>
        <ul class="collapsible" data-collapsible="accordion">
            <?php foreach ($data['etuQuestions'] as $etuQuestion)
            { ?>
                <li>
                    <div class="collapsible-header">
                        <div><?= $etuQuestion->texte ?></div>
                        <div>
                            <?= $data['etuTeachers'][$etuQuestion->idProfesseur]->prenom . ' ' .
                            $data['etuTeachers'][$etuQuestion->idProfesseur]->nom
                            ?>
                        </div>
                    </div>
                    <div class="collapsible-body">
                        <ul>
                            <?php
                            foreach ($data['etuAnswers'][$etuQuestion->idQuestion] as $reponse) {
                                ?>
                                <li <?= ($reponse->prof == 0) ? 'class="right-align"' : '' ?>><?= $reponse->texte ?></li>
                                <?php
                            }
                            ?>
                        </ul>
                        <form action="/Process_etudiant/repondreQuestion" method="POST">
                            <input type="hidden" name="r_idQuestion" value ="<?= $etuQuestion->idQuestion ?>"/>
                            <div class="btn-footer">
                                <div class="input-field">
                                    <textarea name="r_texte" id="r_texte"></textarea>
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
                <input type="text" name="q_titre" id="q_titre" autocomplete="off"/>
                <label for="q_titre">Titre</label>
            </div>
            <div class="input-field">
                <textarea name="q_texte" id="q_texte"></textarea>
                <label for="q_texte">Question</label>
            </div>
            <div class="input-field">
                <select name="q_idProfesseur" id="q_idProfesseur">
                    <option value="null" disabled selected>Choisir un professeur</option>
                    <?php
                    foreach ($data['etuTeachers'] as $teacher)
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
