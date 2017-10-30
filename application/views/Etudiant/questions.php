<main class="container">
    <div class="section">
        <h4 class="header">Les questions</h4>
        <ul class="collapsible" data-collapsible="accordion">
            <?php foreach ($data['questions'] as $question)
            { ?>
                <li>
                    <div class="collapsible-header">
                        <div><?= $question->titre ?></div>
                        <div>
                            <?= $data['teachers'][$question->idProfesseur]->prenom . ' ' .
                            $data['teachers'][$question->idProfesseur]->nom
                            ?>
                        </div>
                    </div>
                    <div class="collapsible-body">
                        <p class="right-align"><?= $question->texte ?></p>
                        <ul>
                            <?php
                            foreach ($data['answers'][$question->idQuestion] as $reponse) {
                                ?>
                                <li class="divider"></li>
                                <li><p <?= !$reponse->prof ? 'class="right-align"' : '' ?>><?= $reponse->texte ?></p></li>
                                <?php
                            }
                            ?>
                        </ul>
                        <form action="/Process_etudiant/repondreQuestion" method="POST">
                            <input type="hidden" name="r_idQuestion" value ="<?= $question->idQuestion ?>"/>
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
        <h4 class="header">Poser une question</h4>
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
                    <option value="" disabled selected>Choisir un professeur</option>
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