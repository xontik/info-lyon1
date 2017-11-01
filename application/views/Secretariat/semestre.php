        <main>
            <section>
                <h2>Gestion du semestre : <?= $data['semestre']->type.' - '.$data['semestre']->anneeScolaire.' '.(($data['semestre']->differe == 1)?' différé':'')?></h2>
                <a href="<?= base_url('Process_secretariat/deleteSemestre/').$data['semestre']->idSemestre ?>">Supprimer ce semestre</a>



                <?php if(count($data['groups']) > 0 ){?>
                    <form action="<?= base_url('Process_secretariat/addStudentGroup/').$data['semestre']->idSemestre ?>" method="post" >


                        <table>
                            <thead>
                                <tr>
                                    <?php
                                    $maxstudents = 0;
                                    foreach ($data['groups'] as $group) {
                                        echo '<th colspan="3">'.$group['nomGroupe'].'</th>';
                                        if(count($group['students'])>$maxstudents){
                                            $maxstudents = count($group['students']);
                                        }
                                    }
                                    echo '</tr>'.PHP_EOL;
                                    echo '<tr>';
                                    for ($i=0; $i < count($data['groups']); $i++) {
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
                                    <?php foreach ($data['groups'] as $group) {
                                        //TODO add lien de delete
                                        if(isset($group['students'][$i]['numEtudiant'])){?>
                                            <td>
                                                <?php // TODO: js confirm ?>
                                                <a class='deleter'  href="<?= base_url('Process_secretariat/deleteRelation/').$group['idGroupe'].'/'.$group['students'][$i]['numEtudiant'].'/'.$data['semestre']->idSemestre ?>" ><?= html_img('trash_delete.png','Supprimer etudiant') ?></a>
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

                                <?php endfor;  ?>
                                <tr>


                                    <?php foreach ($data['groups'] as $group){ ?>
                                        <td>
                                            <label for="grp<?= $group['idGroupe']?>">Ajout etudiant :</label>
                                        </td>
                                        <td>
                                            <select id="grp<?= $group['idGroupe']?>" name="grp<?= $group['idGroupe']?>">
                                                <optgroup label="Sans groupe">
                                                    <?php foreach ($data['freeStudents'] as $student):?>
                                                            <option value="<?= $student->numEtudiant ?>"><?= $student->nom.' '.$student->prenom ?></option>
                                                    <?php endforeach; ?>
                                                    <?php foreach ($data['groups'] as $grp):
                                                        if($grp['idGroupe'] == $group['idGroupe']){continue;}?>
                                                        <optgroup label="<?= $grp['nomGroupe'] ?>">
                                                        <?php foreach ($grp['students'] as $student): ?>

                                                            <option value="<?= $student['numEtudiant'] ?>"><?= $student['nom'].' '.$student['prenom'] ?></option>
                                                        <?php endforeach; ?>
                                                        </optgroup>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            </select>
                                        </td>
                                        <td>
                                            <button type="submit" name="submit" value="<?= $group['idGroupe'] ?>">Ajouter</button>
                                        </td>
                                    <?php } ?>
                                </tr>
                                <?php /**/ ?>
                                <tr>

                                    <?php foreach ($data['groups'] as $group): ?>
                                        <td colspan="3"><a href="<?= base_url('Process_secretariat/deleteGroupe/').$group['idGroupe'].'/'.$data['semestre']->idSemestre ?>">Supprimer ce groupe</a></td>

                                    <?php endforeach;?>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                <?php } ?>
                </section>
                <section>
                    <h2>Actions Groupe</h2>
                    <ul>
                        <li>
                            <a href="<?= base_url('Process_secretariat/getCSVGroupeSemestre/').$data['semestre']->idSemestre ?>">Exporter groupes de ce semestre</a>
                        </li>
                        <li>
                            <form  action="<?= base_url('Process_secretariat/importCSV/'.$data['semestre']->idSemestre)?>" method="post" enctype="multipart/form-data">

                                <input type="file" name="import" value="">
                                <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
                                <button type="submit" name="importSem">Importer</button>
                            </form>
                        </li>
                        <li>
                            <form action="<?= base_url('Process_secretariat/addGroupe/').$data['semestre']->idSemestre ?>" method="post">
                                <label for="nomGroupe">Nom du groupe : </label>
                                <input type="text" name="nomGroupe" id="nomGroupe">
                                <button type="submit" name="addGroupe">Ajouter Groupe</button>
                            </form>
                        </li>
                    </ul>

                </section>
                <section>
                  <h2>Attribution professeurs a un couple Groupe-Matiere</h2>
                  <p>Ici ajout manuel</p>
                  <p>Ici export csv pour un smestre</p>
                  <p>Ici import d'un csv</p>
                </section>


        </main>
