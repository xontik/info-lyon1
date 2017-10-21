<main>
    <section>
        <header>Les questions</header>
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
                                <?php /*echo $etuQuestion->numEtudiant . ' - '
                                . $student->prenom . ' ' . strtoupper($student->nom);*/
                                    echo $etuQuestion->idProfesseur; // TODO : afficher le nom du prof
                                ?>
                            </div>
                        </div>
                    </li>
                    <ul>
                        <?php
                        $listeReponses = $this->repMod->getAnswers($etuQuestion->idQuestion);
                        foreach ($listeReponses as $reponse) {
                            $estProf = '';
                            if ($reponse->prof == 1) {
                                $estProf = 'isProf';
                            }
                            echo '<li class="qr ' . $estProf . '">' . $reponse->texte . '</li>';
                        }
                        ?>
                        <form action="<?php echo current_url(); ?>" method="POST">
                            <input type="hidden" name="r_idQuestion" value ="<?php echo $etuQuestion->idQuestion; ?>"/>
                            <div>
                                <input type="text" name="r_texte" autocomplete="off"/>
                                <input type="submit" value = "Répondre" />
                            </div>
                        </form>
                    </ul>
                </div>
            <?php
            }
            ?>
        </ul>
        <section>
            <h1>Poser une question</h1>
            <form action="<?php echo current_url(); ?>" method="POST">
                <input autocomplete="off" name="q_titre" placeholder="Titre" type="text" />
                <input autocomplete="off" name="q_texte" placeholder="Question" type="text" />
                <!-- TODO : Sélection du prof (en attendant via son id) -->
                <input autocomplete="off" name="q_idProfesseur" placeholder="Professeur" type="text" />
                <input type="submit" value="Envoyer" />
            </form>
        </section>
    </section>
</main>