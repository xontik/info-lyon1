<main class="container">
    <div class="section">
        <h4 class="header">Questions</h4>
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
                            foreach ($data['answers'][$question->idQuestion] as $answer) {
                                ?>
                                <li class="divider"></li>
                                <li><p <?= !$answer->prof ? 'class="right-align"' : '' ?>><?= $answer->texte ?></p></li>
                                <?php
                            }
                            ?>
                        </ul>
                        <form action="<?= base_url('Process_Question/answer') ?>" method="POST">
                            <input type="hidden" name="idQuestion" value ="<?= $question->idQuestion ?>"/>
                            <div class="btn-footer">
                                <div class="input-field">
                                    <textarea class="materialize-textarea" name="texte" id="texte"></textarea>
                                    <label for="texte">Réponse</label>
                                </div>
                                <button type="submit" class="waves-effect waves-light btn">Répondre</button>
                            </div>
                        </form>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="card grey lighten-5">
        <div class="card-content">
            <span class="card-title">Poser une question</span>
            <form action="<?= base_url('/Process_Question/send') ?>" method="POST">
                <div class="input-field">
                    <input type="text" name="titre" id="titre" autocomplete="off" data-length="255"/>
                    <label for="titre">Titre</label>
                </div>
                <div class="input-field col s12">
                    <textarea class="materialize-textarea" name="texte" id="texte"></textarea>
                    <label for="texte">Question</label>
                </div>
                <div class="input-field row">
                    <select name="idProfesseur" id="idProfesseur" class="col s12 m8 l5">
                        <option value="" disabled selected>Choisir un professeur</option>
                        <?php
                        foreach ($data['teachers'] as $teacher)
                        { ?>
                            <option value="<?= $teacher->idProfesseur ?>"><?= $teacher->prenom . ' ' . $teacher->nom ?></option>
                            <?php
                        } ?>
                    </select>
                    <label for="idProfesseur"></label>
                </div>
                <div class="btn-footer">
                    <button type="submit" class="waves-effect waves-light btn">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</main>