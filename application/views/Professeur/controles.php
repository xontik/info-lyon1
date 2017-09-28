<main>
  <p>
    <a href="<?php echo base_url('professeur/addControle')?>" >Ajouter un controle</a><br>
    <a href="<?php echo base_url('professeur/addControle/promo')?>" >Ajouter un DS de promo</a><br>

  </p>
  <br>
  <?php
  if(count($data['controls']) > 0) {
    ?>
  <form method="post" action="<?php echo base_url('professeur/controle')?>">
    <?php
    if(count($data['groupes']) > 1){
      echo "<p>Groupes :</p>";
      foreach ($data['groupes'] as $groupe) {
        $checked = "";
        if(in_array($groupe->idGroupe,$data["restrict"]["groupes"])){
          $checked = "checked";
        }
        echo '<label for="id'.$groupe->nomGroupe.'">'.$groupe->nomGroupe.$groupe->type.'</label><input type="checkbox" id="id'.$groupe->nomGroupe.'" name="'.$groupe->idGroupe.'" '.$checked.'>';
      }
    }
    if(count($data['matieres'])){
      echo "<p>Matieres :</p>";
      foreach ($data['matieres'] as $matiere) {
        $checked = "";
        if(in_array($matiere->codeMatiere,$data["restrict"]["matieres"])){
          $checked = "checked";
        }
        echo '<label for="id'.$matiere->codeMatiere.'">'.$matiere->nomMatiere.'</label><input type="checkbox" id="id'.$matiere->codeMatiere.'" name="'.$matiere->codeMatiere.'" '.$checked.' >';
      }

    }


    echo "<p>Type de controles</p>";
    $checked  = "";
    if(in_array("DSPROMO",$data["restrict"]["DS"])){
      $checked = "checked";
    }
    echo '<label for="idDSPromo">Ds Promo</label><input type="checkbox" id="idDSPromo" name="DSPROMO" '.$checked.'>';
    $checked  = "";
    if(in_array("CC",$data["restrict"]["DS"])){
      $checked = "checked";
    }
    echo '<label for="idCC">CC</label><input type="checkbox" id="idCC" name="CC" '.$checked.'>';
    ?>
    <br>
    <input type="submit" name="filter" value="Filter"/>
  </form>
  <br>
  <?php
  $mat = null;

    ?>
    <h2>Controles</h2>
    <table>
      <tr>
        <th>Matière</th>
        <th>Libélé</th>
        <th>Groupe</th>
        <th>Type</th>
        <th>Coeff</th>
        <th>Div</th>
        <th>Mediane</th>
        <th>Moyenne</th>
        <th>Date</th>
        <th>Supprimer</th>
        <th>Modifier</th>
        <th>Ajouter Note</th>
      </tr>

      <?php
      foreach ($data['controls'] as $control) {
        $date = date_create_from_format('Y-m-d', $control->dateControle);
        echo "<tr>";

        echo "<td>". $control->codeMatiere." - ".$control->nomMatiere . "</td>";
        echo "<td>". $control->nomControle . "</td>";
        echo "<td>". $control->nomGroupe . "</td>";
        echo "<td>". $control->typeControle . "</td>";
        echo "<td>". $control->coefficient . "</td>";
        echo "<td>". $control->diviseur . "</td>";
        echo "<td>". ($control->median != null ? ( $control->median) : "Non calculée") . "</td>";
        echo "<td>". ($control->average != null ? ($control->average) : "Non calculée") . "</td>";
        echo "<td>". $date->format("d/m/Y") . "</td>";
        echo "<td><a href='" . base_url("process_professeur/deletecontrole/" . $control->idControle) . "'>X</a></td>";
        echo "<td><a href='" . base_url("professeur/editcontrole/" . $control->idControle) . "'>Edit</a></td>";
        echo "<td><a href='" . base_url("professeur/ajoutNotes/" . $control->idControle) . "'>Notes</a></td>";
        echo "</tr>";

      }

      echo"</table>";
    }
    ?>

  </main>
