  <main>
    <section>
      <h2>Gestion des parcours</h2>
      <?php
        if(count($data['parcours'])){ //AKA est ce qu'il ya des parcours modifiable
       ?>
      <form action="<?= base_url('Process_secretariat/joinUESemestre')?>" method="post">
        <label for="parcours">Selectioner un parcours à modifier :</label><br>
        <select id="parcours" name="parcours">
          <?php
          foreach($data['parcours'] as $parcours){
            echo '<option value="'.$parcours->idParcours.'">'.$parcours->type.' démarrant en '.$parcours->anneeCreation.'</option>';
          }
          ?>
        </select>
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
    <?php }?>
    </section>
    <section>
      <h2>Attribution professeurs a un couple Groupe-Matiere</h2>
      <p>Ici ajout manuel</p>
      <p>Ici export csv pour un smestre</p>
      <p>Ici import d'un csv</p>
    </section>
    <section>
      <h2>Creation des semestres</h2>
      <a href="#">Creer les semestres de l'année <?= date('Y').'-'.((int)(date('Y')+1)) ?></a>
    </section>
  </main>
