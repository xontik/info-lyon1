<main>
  <section>
    <h2>Gestion des parcours</h2>

    <?php
    if(count($data['parcours'])){ //AKA est ce qu'il ya des parcours modifiable
      ?>
      <section>
        <h3>Relation parcours/UE</h3>
        <form id="delete" action="<?= base_url('Process_secretariat/deleteParcours')?>" method="post">
          <label for="parcours">Selectioner un parcours à modifier :</label><br>
          <select id="parcours" name="parcours">
            <?php
            foreach($data['parcours'] as $parcours){
              echo '<option value="'.$parcours->idParcours.'">'.$parcours->type.' démarrant en '.$parcours->anneeCreation.'</option>';
            }
            ?>
          </select>
          <input type="submit" name="suppr" value="Supprimer ce parcours">
          <div id="inout">
            <div>
              <label for="UEin">UE lié au modules :</label>
              <select multiple name="UEin" id="UEin">

              </select>
            </div>
            <div>
              <input type="button" name="add" id="add" value="<">
              <input type="button" name="remove" id="remove" value=">">
            </div>
            <div>
              <label for="UEout">UE disponible :</label>
              <select multiple name="UEout" id="UEout">

              </select>
            </div>
          </div>
        </form>
      </section>

    <?php }?>
    <section>
      <form action="<?= base_url('Process_secretariat/addParcours')?>" method="post">
        <h3>Ajouter un parcours</h3>
        <label for="year">Année d'entrée en application : </label>
        <input type="number" name="year" id="year" min="<?= (int)(date('Y')+1)?>" value="<?= (int)(date('Y')+1)?>">

        <label for="type"> Type de semestre :</label>
        <select id="type" name="type">
          <option value="S1">S1</option>
          <option value="S2">S2</option>
          <option value="S3">S3</option>
          <option value="S4">S4</option>
        </select>
        <input type="submit" name="send" value="Ajouter">
      </form>
    </section>
    <section>
      <h2>Gestion des semestres</h2>
      <section>
        <h3>Liste des semestres</h3>
        <table id='tableSemestre'>
          <thead>
            <tr>
              <th>Gerer</th>
              <th>Année scolaire</th>
              <th>Type semestre</th>
              <th>Groupes</th>
              </tr>
            </thead>

            <tbody>


              <?php
              foreach ($data['semestres'] as $semestre) {
                $sem = $semestre['data'];


                ?>
                <tr class="<?= $semestre['etat'] ?>" >
                  <td>
                    <?php
                    if($semestre['etat'] != 'after'){
                      ?>
                      <a href="<?= base_url('Secretariat/gestionSemestre/').$sem->idSemestre ?>"><?= html_img('note_edit.png','Gerer')?></a>
                      <?php
                    }
                     ?>
                  </td>
                  <td><?= $sem->anneeScolaire.' - '.$sem->type ?></td>

                  <td><?= ($sem->differe == 0)?'Normal':'Différé' ?></td>
                  <td>
                    <?php
                    if(count($semestre['groups']) > 0){
                      foreach ($semestre['groups'] as $key => $group) {
                        echo (($key > 0)?' - ':'').$group['nomGroupe'];
                      }
                    }
                    ?>
                  </td>



                </tr>
                <?php
              }
              ?>
            </tbody>
          </table>
        </section>

      </section>
      <section>
        <h2>Gestion des groupes</h2>
        <p>Importer un groupe</p>
        <p>Liste des groupe</p>
      </section>
      <section>
        <h2>Attribution professeurs a un couple Groupe-Matiere</h2>
        <p>Ici ajout manuel</p>
        <p>Ici export csv pour un smestre</p>
        <p>Ici import d'un csv</p>
      </section>
    </section>

  </main>
