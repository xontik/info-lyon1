<main>
    <section>
        <header><h1>Les questions</h1></header>
        <ul>
            <?php foreach ($data['etuQuestions'] as $etuQuestion) { ?>
                <div>
                    <li class = "question qr">
                        <div>
                            <div>
                                <?php
                                $i = 0;
                                while (!($i >= 30 OR $i == strlen($etuQuestion->texte))) {
                                    if (ord($etuQuestion->texte[$i]) <= 127) {
                                        echo $etuQuestion->texte[$i];
                                        $i++;
                                    } else {
                                        echo $etuQuestion->texte[$i] . $etuQuestion->texte[$i + 1];
                                        $i = $i + 2;
                                    }
                                }
                                if ($i >= 30 AND $i < strlen($etuQuestion->texte)) {
                                    echo '...';
                                }
                                ?>
                            </div>
                            <div>
                                <?php
                                    echo $this->teacherMod->getProfInfo($etuQuestion->idProfesseur)->prenom . ' ' .$this->teacherMod->getProfInfo($etuQuestion->idProfesseur)->nom;
                                ?>
                            </div>
                        </div>
                    </li>
                    <ul>
                        <?php
                        $listeReponses = $this->questionsMod->getAnswers($etuQuestion->idQuestion);
                        foreach ($listeReponses as $reponse) {
                            $estProf = '';
                            if ($reponse->prof == 1) {
                                $estProf = 'isProf';
                            }
                            echo '<li class="qr ' . $estProf . '">' . $reponse->texte . '</li>';
                        }
                        ?>
                        <form action="/Process_etudiant/repondreQuestion" method="POST">
                            <input type="hidden" name="r_idQuestion" value ="<?php echo $etuQuestion->idQuestion; ?>"/>
                            <div>
                                <input type="text" name="r_texte" autocomplete="off"/>
                                <input type="submit" value = "Répondre" />
                            </div>
                        </form>
                    </ul>
                </div>
            <?php } ?>
        </ul>
        <section>
            <h1>Poser une question</h1>
            <form action="/Process_etudiant/envoyerQuestion" method="POST">
                <input autocomplete="off" name="q_titre" placeholder="Titre" type="text" />
                <input autocomplete="off" name="q_texte" placeholder="Question" type="text" />
                <!-- <input autocomplete="off" name="q_idProfesseur" placeholder="Professeur" type="text" /> -->
                <select name="q_idProfesseur">
                    <option value="null" disabled selected>Choisir un prof</option>
                    <?php foreach ($data['etuTeachers'] as $teacher) { ?>
                        <option value="<?php echo $teacher->idProfesseur; ?>"><?php echo $teacher->prenom . ' ' . $teacher->nom; ?></option>            
                    <?php } ?>

                </select>
                <input type="submit" value="Envoyer" />
            </form>
        </section>
    </section>
</main>