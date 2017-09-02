<?php
/**
* Created by PhpStorm.
* User: xontik
* Date: 24/04/2017
* Time: 00:33
*/
?>
<main>
  <form method="post"
  action="<?php echo base_url("process_professeur/addcontrole/" . ($data['promo'] ? 'promo' : '')) ?>">
  <?php if ($data['promo'] === false){ ?>
    <label for="enseignement">Selectioner un couple groupe/Matières : </label>
    <select name="enseignement" id="enseignement">
      <?php
      foreach ($data["select"] as $d) {
        echo "<option value='" . $d->idEnseignement . "'>" . $d->nomGroupe . " en " . $d->nomMatiere . "</option>";
      }
    }else{ ?>
      <label for="matiere">Selectioner une matières : </label>
      <select name="matiere" id="matiere">
        <?php
        foreach ($data["select"] as $d) {
          echo "<option value='" . $d->codeMatiere . "'>" . $d->codeMatiere . " - " . $d->nomMatiere . "</option>";
        }

      } ?>
    </select><br>

    <label for="nom">Libellé : </label>
    <input type="text" id="nom" name="nom"/><br>
    <label for="coeff">Coefficient : </label>
    <input type="number" id="coeff" name="coeff" step="0.05" min="0"/><br>
    <label for="diviseur">Diviseur</label>
    <input type="number" id="diviseur" name="diviseur" min="0"/><br>
    <label for="type">Type : </label>
    <?php if ($data['promo'] == false){ ?>
      <select id="type" name="type">
        <option value="DS Groupe">DS Groupe</option>
        <option value="CC">CC</option>
      </select><br>
    <?php } ?>
    <label for="date">Date du controle : </label>
    <input type="date" id="date" name="date"/><br>
    <input type="submit" name="valid" value="Ajouter"><a href="<?php echo site_url("professeur/controle")?>">Retour</a>
  </form>
</main>
