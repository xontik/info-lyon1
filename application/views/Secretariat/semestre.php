        <main>
            <section>
                <h2>Gestion du semestre : <?= $data['semestre']->type.' - '.$data['semestre']->anneeScolaire.' '.(($data['semestre']->differe == 1)?' différé':'')?></h2>



                <?php if(count($data['students']) > 0 ){?>
                    <form action="index.html" method="post" enctype="multipart/form-data">


                        <table>
                            <thead>
                                <tr>
                                    <?php
                                    $maxstudents = 0;
                                    foreach ($data['students'] as $group) {
                                        echo '<th colspan="3">'.$group['nomGroupe'].'</th>';
                                        if(count($group['students'])>$maxstudents){
                                            $maxstudents = count($group['students']);
                                        }
                                    }
                                    echo '</tr>'.PHP_EOL;
                                    echo '<tr>';
                                    for ($i=0; $i < count($data['students']); $i++) {
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
                                for ($i=0; $i < $maxstudents; $i++):?>
                                    <tr>
                                    <?php foreach ($data['students'] as $group) {
                                        if(isset($group['students'][$i])){?>
                                            <td>
                                                <?= $group['students'][$i]['numEtudiant'] ?>
                                            </td>
                                            <td>
                                                <?= $group['students'][$i]['nom'] ?>
                                            </td>
                                            <td>
                                                <?= $group['students'][$i]['prenom'] ?>
                                            </td>
                                        <?php }else{ ?>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <?php
                                        }

                                    } ?>
                                    </tr>

                                <?php endfor; ?>
                                <tr>


                                    <?php foreach ($data['students'] as $group): ?>
                                        <td>
                                            <label for="student<?= $group['nomGroupe']?>">Ajout etudiant :</label>
                                        </td>
                                        <td>
                                            <select id="student<?= $group['nomGroupe']?>" name="student<?= $group['nomGroupe']?>">
                                                <?php foreach ($data['freeStudents'] as $student): ?>
                                                    <option value="<?= $student->numEtudiant ?>"><?= $student->nom.' '.$student->prenom ?></option>

                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="submit">Ajouter</button>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>

                                <tr>

                                    <?php
                                    foreach ($data['students'] as $group) {
                                        ?>

                                        <td colspan="3"><a href="#">Supprimer ce groupe</a></td>

                                        <?php
                                    }

                                    ?>
                                </tr>
                            </tbody>
                        </table>
                    </form>

                        <section>
                            <h2>Actions</h2>
                            <form  action="#" method="post" enctype="multipart/form-data">

                            <a href="<?= base_url('Process_secretariat/getCSVSemestre/').$data['semestre']->idSemestre ?>">Exporter ce semestre</a>
                            <input type="file" name="import" value="">
                            <button type="submit" name="button">Importer</button>
                            <!-- TODO Import csv-->
                            <a href="#">Supprimer ce semestre</a>

                            <!-- TODO form add groupe -->
                            <a href="#">Ajouter groupe</a>
                        </form>

                        </section>

                <?php } ?>
            </section>
        </main>
