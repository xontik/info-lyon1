<main>

  <h2>Controle "<?php echo $data['control']->nomControle; ?>" du :<?php echo date_create_from_format('Y-m-d', $data['control']->dateControle)->format("d/m/Y"); ?></h2>
  <p><?php echo $data['matiere']->codeMatiere." - ".$data['matiere']->nomMatiere; ?> </p>
  <form method="post" action="<?php echo base_url("process_professeur/addmarks/" . ($data['control']->idControle)); ?>">
    <table>
      <tr>
        <th>Nom</th>
        <th>Prenom</th>
        <th>Note /<?= $data['control']->diviseur ?></th>
      </tr>

      <?php
      foreach ($data['marks'] as $mark) {
        echo "<tr>";
        echo "<td>". $mark->nom. "</td>";
        echo "<td>". $mark->prenom. "</td>";
        echo "<td><input type='number' name='".$mark->numEtudiant."' value='".((!is_null($mark->valeur))?$mark->valeur:"")."'/></td>";
        echo "</tr>";

      }

      echo"</table>";

      ?>
      <input type="submit" value="Envoyer" name="send" />
      <a href="<?= base_url('Professeur/controle')?>">Retour</a>
    </form>
  </main>
