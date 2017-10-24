<main class="container">
    <section class="table">
        <header><h1>Les questions</h1></header>
        <ul class="collapsible" data-collapsible="accordion">
            <?php foreach ($data['etuQuestions'] as $etuQuestion) { ?>
                <li>
                    <div class="collapsible-header">
                        <div><?= $etuQuestion->texte ?></div>
                        <div>
                            <?= $this->teacherMod->getProfInfo($etuQuestion->idProfesseur)->prenom . ' ' .
                            $this->teacherMod->getProfInfo($etuQuestion->idProfesseur)->nom;
                            ?>
                        </div>
                    </div>
                    <div class="collapsible-body">
                        <ul>
                            <?php
                            $listeReponses = $this->questionsMod->getAnswers($etuQuestion->idQuestion);
                            foreach ($listeReponses as $reponse) {
                                ?>
                                <li <?= ($reponse->prof == 0) ? 'class="right-align"' : '' ?>><?= $reponse->texte ?></li>
                                <?php
                            }
                            ?>
                            <li>
                                <form action="/Process_etudiant/repondreQuestion" method="POST">
                                    <input type="hidden" name="r_idQuestion" value ="<?= $etuQuestion->idQuestion ?>"/>
                                    <div>
                                        <input type="text" name="r_texte" autocomplete="off"/>
                                        <input class="waves-effect waves-light btn" type="submit" value = "Répondre" />
                                    </div>
                                </form>
                            </li>
                        </ul>
                    </div>
                </li>
<?php } ?>
        </ul>
    </section>
    <section>
        <h1>Poser une question</h1>
        <form action="/Process_etudiant/envoyerQuestion" method="POST">
            <input autocomplete="off" name="q_titre" placeholder="Titre" type="text" />
            <input autocomplete="off" name="q_texte" placeholder="Question" type="text" />
            <select name="q_idProfesseur">
                <option value="null" disabled selected>Choisir un prof</option>
                <?php foreach ($data['etuTeachers'] as $teacher) { ?>
                    <option value="<?php echo $teacher->idProfesseur; ?>"><?= $teacher->prenom . ' ' . $teacher->nom ?></option>            
<?php } ?>
            </select>
            <input class="waves-effect waves-light btn" type="submit" value="Envoyer" />
        </form>
    </section>
</main>
